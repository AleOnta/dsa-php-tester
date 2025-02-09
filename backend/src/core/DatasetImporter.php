<?php

namespace Backend\Core;

use Backend\Models\Dataset;
use Backend\Models\Job;
use Backend\Models\UploadStatus;
use Backend\Repositories\DatasetsRepository;
use Backend\Repositories\JobRepository;
use Backend\Services\DatasetsService;
use Backend\Services\JobService;
use Backend\Utils\AppConstants;
use RuntimeException;

class DatasetImporter
{
    private Job $job;
    private Dataset $dataset;
    private string $fileName;
    private string $fileType;
    private array $operationReport;
    private JobService $jobService;
    private DatasetsService $datasetsService;

    public function __construct(int $datasetId, int $jobId)
    {
        $db = \Backend\Database\Database::getInstance();
        $this->jobService = new JobService(new JobRepository($db));
        $this->datasetsService = new DatasetsService(new DatasetsRepository($db));
        $this->setJob($jobId);
        $this->setDataset($datasetId);
    }

    public function getFilename()
    {
        return $this->dataset;
    }

    public function getFileType()
    {
        return $this->fileType;
    }

    public function setJob(int $jobId)
    {
        # retrieve the job by id
        $this->job = $this->jobService->getJobById($jobId);
    }

    public function setDataset(int $datasetId)
    {
        # retrieve dataset by id
        $dataset = $this->datasetsService->getDatasetById($datasetId);
        # set the dataset attr
        $this->dataset = (new Dataset())->hydrate($dataset);
        $this->fileName = $this->dataset->getName();
        $this->fileType = $this->dataset->getType();
    }

    public function importDatasetJSON()
    {
        # file wasn't copied on the server
        if (!file_exists(AppConstants::DATASETS_DIR . $this->fileName)) {
            throw new RuntimeException("Server Error");
        }
        # open the JSON file as stream
        $objects = \JsonMachine\Items::fromFile(AppConstants::DATASETS_DIR . $this->fileName);
        # upload the job record
        $this->jobService->updateJob($this->job->getId(), [
            'status' => UploadStatus::PROCESSING->value,
            'progress' => 1,
            'message' => 'importing into database'
        ]);
        # setting up a counter variable
        $count = $atError = 0;
        # loop through each object
        foreach ($objects as $obj) {
            # attempting insert
            $import = $this->datasetsService->storeJsonObject($this->dataset->getId(), json_encode($obj));
            # updating the counter
            if (!$import) {
                $atError++;
                continue;
            }
            $count++;
        }
        # updating job as completed
        $this->jobService->updateJob($this->job->getId(), [
            'status' => UploadStatus::COMPLETED->value,
            'progress' => 2,
            'message' => "imported a total of {$count} records into database. {$atError} records were skipped."
        ]);
    }

    public function importDatasetCSV() {}
}
