<?php

namespace Backend\Services;

use Backend\Repositories\DatasetsRepository;

class DatasetsService
{
    private DatasetsRepository $datasetsRepository;

    public function __construct(DatasetsRepository $datasetsRepository)
    {
        $this->datasetsRepository = $datasetsRepository;
    }
}
