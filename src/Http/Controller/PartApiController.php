<?php

namespace App\Http\Controller;

use App\Http\HttpException;
use App\Http\Input;
use App\Http\Request;
use App\Http\Response;
use App\Model\Part;
use App\Repository\PartRepository;

class PartApiController
{
    public function __construct(private PartRepository $parts)
    {
    }

    public function index(Request $request): Response
    {
        $parts = $this->parts->findByFilter(Input::partFilter($request));

        return Response::json([
            'count' => count($parts),
            'parts' => array_map(fn (Part $part) => $part->toArray(), $parts),
        ]);
    }

    public function show(Request $request): Response
    {
        $id = $request->routeParams['id'];
        $part = ctype_digit($id) ? $this->parts->findById((int) $id) : null;

        if ($part === null) {
            throw new HttpException(404, 'part_not_found', 'Dit onderdeel bestaat niet.');
        }

        return Response::json($part->toArray());
    }
}
