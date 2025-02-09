<?php

namespace Backend\Controllers;

use Backend\Exceptions\FileUploadException;
use Backend\Exceptions\MissingDatasetException;
use Backend\Services\DatasetsService;
use Backend\Services\JobService;
use Backend\Utils\AppConstants;

class DatasetsController extends Controller
{
    private DatasetsService $datasetsService;
    private JobService $jobService;

    public function __construct(DatasetsService $datasetsService, JobService $jobService)
    {
        parent::__construct();
        $this->datasetsService = $datasetsService;
        $this->jobService = $jobService;
    }

    public function index()
    {
        # check if the dataset has been uploaded
        if (!isset($_FILES['dataset'])) {
            throw new FileUploadException(
                "Error during file upload",
                ['dataset' => 'no file was uploaded to the server']
            );
        }
        # extract the file data
        $file = $_FILES['dataset'];
        # validate the uploaded file
        $this->datasetsService->validateFileUpload($file);
        # generate a unique name
        $filename = $this->datasetsService->validateFileName($file['name']);
        # define the path for storing the dataset
        $path = AppConstants::DATASETS_DIR . $filename;
        # store the dataset internally
        if (!move_uploaded_file($file['tmp_name'], $path)) {
            # error while storing the file
            throw new FileUploadException(
                'Error during file upload',
                ['internal' => 'Internal Server Error. Try again later']
            );
        }
        # create the dataset record
        $datasetId = $this->datasetsService->createDataset($filename, $this->datasetsService->getFileType($file), $file['size']);
        # change the file permissions
        chmod($path, 644);
        # create a new file upload job
        $jobId = $this->jobService->createFileUploadJob($filename, $datasetId);
        # spawn an upload job
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            # spawn job on windows environment
            $cmd = "start /B php " . __DIR__ . "\..\process_dataset.php " . escapeshellarg($datasetId) . " " . escapeshellarg($jobId);
            pclose(popen($cmd, 'r'));
        } else {
            # spawn job on unix / linux environment
            $cmd = "php " . __DIR__ . "/../process_dataset.php " . escapeshellarg($datasetId) . " " . escapeshellarg($jobId) . ' > /dev/null 2>&1 &';
            exec($cmd);
        }
        # return response to the client
        $this->response(
            201,
            [
                'status' => 'ok',
                'message' => 'file correctly uploaded'
            ]
        );
    }
}
