<?php

namespace Backend\Models;

use RuntimeException;

class Job
{
    protected ?int $id;
    protected ?string $file;

    protected UploadStatus $status;
    protected int $progress;
    protected ?string $message;
    protected string $created_at;
    protected string $updated_at;
    protected int $dataset_id;

    public function __construct($data = [])
    {
        if (count($data) > 0) {
            $this->hydrate($data);
        } else {
            $this->created_at = date('Y-m-d H:i:s');
        }
    }

    public function hydrate(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->file = $data['file'] ?? null;
        $this->status = UploadStatus::from($data['status']);
        $this->progress = $data['progress'];
        $this->message = $data['message'] ?? null;
        $this->created_at = $data['created_at'];
        $this->updated_at = $data['updated_at'];
        $this->dataset_id = $data['dataset_id'];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(string $file)
    {
        $this->file = $file;
    }

    public function getStatus()
    {
        return $this->status->value;
    }

    public function setStatus(string $status)
    {
        if (!in_array($status, UploadStatus::getAvailable())) {
            throw new RuntimeException("Invalid status ({$status}) passed as Job status");
        }
        $this->status = UploadStatus::from($status);
    }

    public function getProgress()
    {
        return $this->progress;
    }

    public function setProgress(int $progress)
    {
        $this->progress = $progress;
    }

    public function getMessage()
    {
        return $this->message ?? null;
    }

    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(string $updated_at)
    {
        $this->updated_at = $updated_at;
    }

    public function getDatasetId()
    {
        return $this->dataset_id;
    }

    public function setDatasetId(int $id)
    {
        $this->dataset_id = $id;
    }

    public function values()
    {
        return [
            'file' => $this->file ?? null,
            'status' => $this->status->value,
            'progress' => $this->progress,
            'message' => $this->message ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'dataset_id' => $this->dataset_id
        ];
    }
}

enum UploadStatus: string
{
    case INITIALIZED = 'initialized';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function color()
    {
        return match ($this) {
            self::INITIALIZED => '',
            self::PROCESSING => '',
            self::COMPLETED => '',
            self::FAILED => ''
        };
    }

    public static function getAvailable()
    {
        return [
            'initialized',
            'processing',
            'completed',
            'failed'
        ];
    }
}
