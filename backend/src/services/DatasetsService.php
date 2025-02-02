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
        # check if an error has occurred
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new FileUploadException(
                'Error during file upload',
                ['error' => $this->getUploadErrorById($file['error'])]
            );
        }
    }

    # validate the uploaded file size (default to 20 MB)
    private function validateFileSize(array $file, $maxSize = 20 * 1024 * 1024)
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
        # get the uploaded file extension 
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        # check if the extension is allowed
        if (!in_array($ext, $this->getAllowedFileExtensions())) {
            throw new FileUploadException(
                'Error during file upload',
                ['file_type' => "Dataset file type ({$ext}) isn't supported. File Types supported: (" . implode(', ', $this->getAllowedFileExtensions()) . ')']
            );
        }

        # detect file content type
        $mimeType = mime_content_type($file['tmp_name']);
        # check if the content type of the file is supported
        if (!in_array($mimeType, $this->getAllowedFileTypes())) {
            throw new FileUploadException(
                'Error during file upload',
                ['file_type' => "Dataset Content-Type ({$mimeType}) isn't supported. Content-Type supported: (" . implode(', ', $this->getAllowedFileExtensions()) . ')']
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

    private function getAllowedFileExtensions()
    {
        $allowedTypes = [];
        foreach ($this->getAllowedFileTypes() as $fileType) {
            match ($fileType) {
                'text/csv' => $allowedTypes[] = 'csv',
                'application/json' => $allowedTypes[] = 'json'
            };
        }
        return $allowedTypes;
    }

    private function getUploadErrorById(int $id)
    {
        return match ($id) {
            0 => 'There is no error, the file uploaded with success',
            1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            3 => 'The uploaded file was only partially uploaded',
            4 => 'No file was uploaded',
            6 => 'Missing a temporary folder',
            7 => 'Failed to write file to disk.',
            8 => 'A PHP extension stopped the file upload.',
            default => 'Error not recognized'
        };
    }
}
