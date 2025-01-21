<?php

namespace App\Models;

class ApiKey
{
    protected ?int $id;
    protected int $user_id;
    protected string $api_key;
    protected string $created_at;
    protected string $expires_at;
    protected bool $is_active;

    public function hydrate(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->user_id = $data['user_id'] ?? 0;
        $this->api_key = $data['api_key'] ?? '';
        $this->created_at = $data['created_at'] ?? '';
        $this->expires_at = $data['expires_at'] ?? '';
        $this->is_active = strtotime($this->expires_at) > strtotime(date('Y-m-d H:i:s')) ?? false;
    }
}
