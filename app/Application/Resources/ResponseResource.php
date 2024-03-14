<?php

namespace App\Application\Resources;

class ResponseResource
{
    public static function toArray($data = null): array
    {
        return [
            'status' => 'ok',
            'data' => $data,
        ];
    }
}
