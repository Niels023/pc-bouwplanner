<?php

namespace App\Http\Controller;

use App\Compatibility\CompatibilityChecker;
use App\Http\ApiException;
use App\Http\Request;
use App\Http\Response;
use App\Model\Build;
use App\Model\Part;
use App\Model\SavedBuild;
use App\Repository\BuildRepository;
use App\Repository\PartRepository;

class BuildApiController
{
    private const MAX_NAME_LENGTH = 60;

    public function __construct(
        private PartRepository $parts,
        private BuildRepository $builds,
        private CompatibilityChecker $checker,
    ) {
    }

    public function store(Request $request): Response
    {
        $data = $request->json();
        $name = $this->validName($data['name'] ?? null);
        $build = $this->buildFromIds($data['part_ids'] ?? null);

        if ($build->isEmpty()) {
            throw new ApiException(422, 'empty_build', 'Kies minstens één onderdeel voordat je de build opslaat.');
        }

        return Response::json($this->savedBuildToArray($this->builds->save($name, $build)), 201);
    }

    public function show(Request $request): Response
    {
        $saved = $this->builds->findByShareCode($request->routeParams['code']);

        if ($saved === null) {
            throw new ApiException(404, 'build_not_found', 'Geen build gevonden met deze code.');
        }

        return Response::json($this->savedBuildToArray($saved));
    }

    public function check(Request $request): Response
    {
        $build = $this->buildFromIds($request->json()['part_ids'] ?? null);

        return Response::json($this->checker->check($build)->toArray());
    }

    private function validName(mixed $name): string
    {
        $name = is_string($name) ? trim($name) : '';

        if ($name === '' || mb_strlen($name) > self::MAX_NAME_LENGTH) {
            $message = 'Geef de build een naam van 1 tot ' . self::MAX_NAME_LENGTH . ' tekens.';
            throw new ApiException(422, 'invalid_name', $message);
        }

        return $name;
    }

    private function buildFromIds(mixed $ids): Build
    {
        if (!$this->isListOfIds($ids)) {
            throw new ApiException(422, 'invalid_part_ids', 'part_ids moet een lijst met id\'s van onderdelen zijn.');
        }

        $ids = array_values(array_unique($ids));
        $parts = $this->parts->findByIds($ids);

        $unknownIds = array_diff($ids, array_map(fn (Part $part) => $part->id, $parts));
        if ($unknownIds !== []) {
            $list = implode(', ', $unknownIds);
            throw new ApiException(422, 'unknown_part', "Deze onderdelen bestaan niet: $list.");
        }

        $categories = array_map(fn (Part $part) => $part->category()->value, $parts);
        if (count($categories) !== count(array_unique($categories))) {
            throw new ApiException(422, 'duplicate_category', 'Je kunt maar één onderdeel per categorie kiezen.');
        }

        return new Build(...$parts);
    }

    private function isListOfIds(mixed $ids): bool
    {
        if (!is_array($ids) || !array_is_list($ids)) {
            return false;
        }

        foreach ($ids as $id) {
            if (!is_int($id) || $id < 1) {
                return false;
            }
        }

        return true;
    }

    /** @return array<string, mixed> */
    private function savedBuildToArray(SavedBuild $saved): array
    {
        return [
            'share_code' => $saved->shareCode,
            'url' => '/build/' . $saved->shareCode,
            'name' => $saved->name,
            'created_at' => $saved->createdAt->format('Y-m-d H:i:s'),
            'parts' => array_map(fn (Part $part) => $part->toArray(), $saved->build->parts()),
            'check' => $this->checker->check($saved->build)->toArray(),
        ];
    }
}
