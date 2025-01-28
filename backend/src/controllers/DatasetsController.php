<?php

namespace Backend\Controllers;

use Backend\Services\DatasetsService;

class DatasetsController extends Controller
{

    private DatasetsService $datasetsService;

    public function __construct(DatasetsService $datasetsService)
    {
        parent::__construct();
        $this->datasetsService = $datasetsService;
    }

    public function index()
    {
        dd("OK");
    }
}
