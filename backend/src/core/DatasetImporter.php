<?php

namespace Backend\Core;

use Backend\Models\Dataset;
use Backend\Repositories\DatasetsRepository;
use Backend\Utils\AppConstants;
use RuntimeException;

class DatasetImporter
{

    private Dataset $dataset;
    private string $fileName;
    private string $fileType;
    private array $operationReport;
    private DatasetsRepository $datasetsRepository;

    public function __construct(DatasetsRepository $datasetRepo, int $datasetId)
    {
        $this->datasetsRepository = $datasetRepo;
        $this->setDataset($datasetId);
    }

    public function setDataset(int $datasetId)
    {
        $dataset = $this->datasetsRepository->findDatasetById($datasetId);
        if (!$dataset) {
            throw new RuntimeException("No Dataset found with id {$datasetId}");
        }
        $dataset = (new Dataset())->hydrate($dataset);

        $data = file_get_contents(AppConstants::UPLOADS_DIR . '/datasets/' . $dataset->getName());

        dd(json_decode($data, 1));
    }
}
