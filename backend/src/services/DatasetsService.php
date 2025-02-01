<?php

namespace Backend\Services;

use Backend\Exceptions\FileUploadException;
use Backend\Repositories\DatasetsRepository;

class DatasetsService
{
    private array $allowedFileTypes;
    private DatasetsRepository $datasetsRepository;

    public function __construct(DatasetsRepository $datasetsRepository)
    {
        $this->allowedFileTypes = ['text/csv', 'application/json'];
        $this->datasetsRepository = $datasetsRepository;
    }

    # check if any error has occurred during file upload
    private function checkUploadError(array $file)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new FileUploadException(
                'Error during file upload',
                ['error' => $file['error']]
            );
        }
    }

    # validate the uploaded file size (default to 15 MB)
    private function validateFileSize(array $file, $maxSize = 15 * 1024 * 1024)
    {
        if ($file['size'] > $maxSize) {
            throw new FileUploadException(
                'Error during file upload',
                ['file_size' => "Dataset size {$file['size']} bytes is over the maxSize limit set {$maxSize} bytes"]
            );
        }
    }

    # validate the uploaded file type
    private function validateFileType(array $file)
    {
        # extract the file type
        $mimeType = mime_content_type($file['tmp_name']);
        # check if the filetype is supported
        if (!in_array($mimeType, $this->getAllowedFileTypes())) {
            throw new FileUploadException(
                'Error during file upload',
                ['file_type' => "Dataset file type ({$mimeType}) isn't supported. File Types supported: " . $this->getAllowedFileTypeLabels()]
            );
        }
    }

    # groups file upload validation logic
    public function validateFileUpload(array $file)
    {
        # validate errors during upload
        $this->checkUploadError($file);
        # validate file size
        $this->validateFileSize($file);
        # validate file type
        $this->validateFileType($file);
    }

    # validate the filename and generate a safe filename
    public function validateFileName(string $originalName)
    {
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        return uniqid('dataset_', true) . '.' . $ext;
    }

    private function getAllowedFileTypes()
    {
        return $this->allowedFileTypes;
    }

    private function getAllowedFileTypeLabels()
    {
        $allowedTypes = "";
        foreach ($this->getAllowedFileTypes() as $fileType) {
            match ($fileType) {
                'text/csv' => $allowedTypes .= 'CSV,',
                'application/json' => $allowedTypes .= 'JSON,'
            };
        }
        return substr($allowedTypes, 0, strlen($allowedTypes) - 1);
    }
}
