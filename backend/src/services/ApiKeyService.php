<?php

namespace Backend\Services;

use Backend\Exceptions\NotFoundException;
use Backend\Models\ApiKey;
use Exception;

class ApiKeyService
{

    private \Backend\Repositories\ApiKeyRepository $apiKeyRepo;

    public function __construct(\Backend\Repositories\ApiKeyRepository $apiKeyRepository)
    {
        $this->apiKeyRepo = $apiKeyRepository;
    }

    public function keyExists(string $apikey)
    {
        return $this->apiKeyRepo->keyExists($apikey);
    }

    public function fetchKey($user_id)
    {
        # check if the user already has a key
        $apikey = $this->apiKeyRepo->findByUserId($user_id);
        # if apikey doesn't exist or is expired 
        if (!$apikey || !$apikey->isValid()) {
            # create a new apikey
            $key = \Backend\Models\ApiKey::generateApiKey();
            # store the apikey
            $this->apiKeyRepo->storeApiKey($user_id, password_hash($key, PASSWORD_BCRYPT));
            # return the generated key
            return $this->apiKeyRepo->findByUserId($user_id);
        }
        return $apikey;
    }

    public function fetchKeyById(int $id)
    {
        return $apikey = (new ApiKey())->hydrate($this->apiKeyRepo->findById($id));
    }

    public function updateApiKey(int $id, string $apikey)
    {
        # define creation and expiration time
        $created_at = date('Y-m-d H:i:s');
        $expires_at = date('Y-m-d H:i:s', strtotime('+ 3 hours'));
        # execute the update
        $update = $this->apiKeyRepo->updateApiKey($id, $apikey, $created_at, $expires_at);
        if (!$update) {
            throw new Exception('Operation has failed. Try again later...');
        }
    }

    public function deleteApiKey(int $id)
    {
        $delete = $this->apiKeyRepo->destroyById($id);
        if (!$delete) {
            throw new Exception('Operation has failed. Try again later...');
        }
    }
}
