<?php

namespace Backend\Models;

class ApiKey
{
    protected ?int $id;
    protected int $user_id;
    protected string $api_key;
    protected string $created_at;
    protected string $expires_at;
    protected bool $is_active;

    public function getApiKey()
    {
        return $this->api_key;
    }

    public function getExpiresAt()
    {
        return $this->expires_at;
    }

    public function hydrate(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->user_id = $data['user_id'] ?? 0;
        $this->api_key = $data['api_key'] ?? '';
        $this->created_at = $data['created_at'] ?? '';
        $this->expires_at = $data['expires_at'] ?? '';
        $this->is_active = $this->isValid();
        return $this;
    }

    public function isValid()
    {
        # check if apikey has expired
        return (strtotime($this->expires_at) - strtotime(date('Y-m-d H:i:s'))) > 0;
    }

    public function secondsToExipration()
    {
        # return 0 if its already expired
        if (!$this->isValid()) {
            return 0;
        }
        # count s to expiration
        return strtotime($this->expires_at) - strtotime('now');
    }

    public function generateApiKey($length = 32)
    {
        return $apikey = bin2hex(random_bytes($length));
    }
}
