<?php

namespace Backend\Core;

use DateTime;
use PDO;

class RateLimiter
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getApiKeyRequests(string $apikey)
    {
        # prepare the query
        $stmt = $this->db->prepare("SELECT api_key, request_time FROM request_logs WHERE api_key = ?");
        # execute the query
        $stmt->execute([$apikey]);
        # return the result
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
    }

    public function storeRequestLog(string $apikey, string $endpoint)
    {
        # prepare the query
        $stmt = $this->db->prepare("INSERT INTO request_logs (api_key, endpoint) VALUES (:api_key, :endpoint);");
        # bind query parameters
        $stmt->bindParam('api_key', $apikey, PDO::PARAM_STR);
        $stmt->bindParam('endpoint', $endpoint, PDO::PARAM_STR);
        # store in db
        return $stmt->execute();
    }

    public function checkRateLimit(string $apikey, string $endpoint, int $limit, int $window)
    {
        # get current time
        $now = new DateTime();
        # get the start time since to look for requests
        $start = (clone $now)->modify("-{$window} seconds");
        # extract apikey request in timeframe
        $stmt = $this->db->prepare("SELECT COUNT(*) as request_made FROM request_logs WHERE api_key = :api_key AND endpoint = :endpoint AND request_time >= :start");
        # execute the query
        $stmt->execute([
            'api_key' => $apikey,
            'endpoint' => $endpoint,
            'start' => $start->format('Y-m-d H:i:s')
        ]);
        $requests = $stmt->fetch(PDO::FETCH_ASSOC);
        $requests_count = (int) $requests['request_made'];
        # check if the n of request exceed the limit
        if ($requests_count >= $limit) {
            # block the request
            return true;
        }
        # store the request log
        $this->storeRequestLog($apikey, $endpoint);
        # allow the request
        return false;
    }
}
