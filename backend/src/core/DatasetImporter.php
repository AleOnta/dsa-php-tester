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

    public function getFilename()
    {
        return $this->dataset;
    }

    public function setDataset(int $datasetId)
    {
        # retrieve dataset by id
        $dataset = $this->datasetsRepository->findDatasetById($datasetId);
        if (!$dataset) {
            # dataset not found
            throw new RuntimeException("No Dataset found with id {$datasetId}");
        }
        # set the dataset attr
        $this->dataset = (new Dataset())->hydrate($dataset);
        $this->fileName = $this->dataset->getName();
        $this->fileType = $this->dataset->getType();
    }
}
