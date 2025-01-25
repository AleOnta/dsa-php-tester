<?php

namespace Backend\Services;

use Backend\Models\ApiKey;

class ApiKeyService
{

    private \Backend\Repositories\ApiKeyRepository $apiKeyRepo;

    public function __construct(\Backend\Repositories\ApiKeyRepository $apiKeyRepository)
    {
        $this->apiKeyRepo = $apiKeyRepository;
    }

    public function fetchKey($user_id)
    {
        # check if the user already has a key
        $apikey = $this->apiKeyRepo->findByUserId($user_id);
        # if apikey doesn't exist or is expired 
        if (!$apikey || !$apikey->isValid()) {
            # create a new apikey
            $key = new ApiKey();
            $key = $key->generateApiKey();
            # store the apikey
            $this->apiKeyRepo->storeApiKey($user_id, password_hash($key, PASSWORD_BCRYPT));
            # return the generated key
            return $this->apiKeyRepo->findByUserId($user_id);
        }
        return $apikey;
    }
}
