<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CatalogResource;
use App\Domain\Order\CatalogService;

class CatalogController extends Controller
{
    public function __construct(private CatalogService $catalog)
    {
    }

    public function index()
    {
        return new CatalogResource($this->catalog->all());
    }
}
