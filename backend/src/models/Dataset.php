<?php

namespace Backend\Models;

class Dataset
{

    protected ?int $id;
    protected string $name;
    protected string $type;
    protected int $size;
    protected string $created_at;
    protected bool $uploaded;

    public function __construct() {}

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function getSize(string $unit = 'MB')
    {
        return match ($unit) {
            'B' => $this->size,
            'KB' => $this->size / 1024,
            'MB' => ($this->size / 1024) / 1024
        };
    }

    public function setSize(int $size)
    {
        $this->size = $size;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getUploaded()
    {
        return $this->uploaded;
    }

    public function setUploaded(bool $uploaded)
    {
        $this->uploaded = $uploaded;
    }
}
