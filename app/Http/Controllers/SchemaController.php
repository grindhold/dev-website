<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\Filesystem;

class SchemaController extends Controller
{
    public function getSchema(Filesystem $fs, $version, $entity)
    {
        // embedded: LegislativeTerm, Membership, AgendaItem, Consultation
        try {
            // TODO: remove this check once 1.1 is released
            if ($version === '1.1') {
                $version = 'master';
            }

            $loadEntity = $entity;

            if ($version === '1.0' && $entity === 'LegislativeTerm') {
                $loadEntity = 'Body';
            }

            if ($version === '1.0' && $entity === 'Membership') {
                $loadEntity = 'Organization';
            }

            if ($version === '1.0' && $entity === 'AgendaItem') {
                $loadEntity = 'Meeting';
            }

            if ($version === '1.0' && $entity === 'Consultation') {
                $loadEntity = 'Paper';
            }

            $schema = $fs->get("schema/{$version}/$loadEntity.json");
            $schema = json_decode($schema, true);

            if ($entity !== $loadEntity) {
                $schema = $schema['properties'][lcfirst($entity)];
            }

            return response()->json(
                $schema,
                200,
                [],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );
        } catch (\Exception $e) {
            return response('File not found.', 404, ['Content-type' => 'text/plain']);
        }
    }
}
