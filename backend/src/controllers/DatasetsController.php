<?php

namespace Backend\Controllers;

use Backend\Exceptions\FileUploadException;
use Backend\Exceptions\MissingDatasetException;
use Backend\Services\DatasetsService;
use Backend\Utils\AppConstants;

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
        # change the file permissions
        chmod($path, 644);
    }
}
