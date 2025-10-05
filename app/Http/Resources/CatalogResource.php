<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CatalogResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['status' => 'ok', 'data' => $this->resource];
    }
}
