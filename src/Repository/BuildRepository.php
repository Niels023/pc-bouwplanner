<?php

namespace App\Repository;

use App\Model\Build;
use App\Model\SavedBuild;
use DateTimeImmutable;
use PDO;

class BuildRepository
{
    private const SHARE_CODE_LENGTH = 8;

    // Zonder i, l, o, 0 en 1: die lijken op elkaar als je een code overtypt.
    private const SHARE_CODE_CHARACTERS = 'abcdefghjkmnpqrstuvwxyz23456789';

    public function __construct(
        private PDO $pdo,
        private PartRepository $parts,
    ) {
    }

    public function save(string $name, Build $build): SavedBuild
    {
        $shareCode = $this->newShareCode();

        $this->pdo->beginTransaction();

        $this->pdo->prepare('INSERT INTO builds (share_code, name) VALUES (?, ?)')->execute([$shareCode, $name]);
        $buildId = (int) $this->pdo->lastInsertId();

        $insertPart = $this->pdo->prepare('INSERT INTO build_parts (build_id, part_id) VALUES (?, ?)');
        foreach ($build->parts() as $part) {
            $insertPart->execute([$buildId, $part->id]);
        }

        $this->pdo->commit();

        return $this->findByShareCode($shareCode);
    }

    public function findByShareCode(string $shareCode): ?SavedBuild
    {
        $statement = $this->pdo->prepare('SELECT id, share_code, name, created_at FROM builds WHERE share_code = ?');
        $statement->execute([$shareCode]);
        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        return new SavedBuild(
            $row['share_code'],
            $row['name'],
            new DateTimeImmutable($row['created_at']),
            new Build(...$this->parts->findByIds($this->partIds($row['id']))),
        );
    }

    /** @return int[] */
    private function partIds(int $buildId): array
    {
        $statement = $this->pdo->prepare('SELECT part_id FROM build_parts WHERE build_id = ?');
        $statement->execute([$buildId]);

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    private function newShareCode(): string
    {
        $code = '';
        $lastIndex = strlen(self::SHARE_CODE_CHARACTERS) - 1;

        for ($i = 0; $i < self::SHARE_CODE_LENGTH; $i++) {
            $code .= self::SHARE_CODE_CHARACTERS[random_int(0, $lastIndex)];
        }

        return $code;
    }
}
