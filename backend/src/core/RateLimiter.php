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

    public function getRequestIP()
    {
        echo $_SERVER['REMOTE_ADDR'];
        die();
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

    public function storeRequestLog(string $type, string $identifier, string $endpoint)
    {
        # define the column to fill based on type
        $col = $type === 'ADDR' ? 'remote_address' : 'api_key';
        # prepare the query
        $stmt = $this->db->prepare("INSERT INTO request_logs ({$col}, endpoint) VALUES (:{$col}, :endpoint);");
        # bind query parameters
        $stmt->bindParam($col, $identifier, PDO::PARAM_STR);
        $stmt->bindParam('endpoint', $endpoint, PDO::PARAM_STR);
        # store in db
        return $stmt->execute();
    }

    public function checkRateLimit(string $type, string $identifier, string $endpoint, int $limit, int $window)
    {
        # get current time
        $now = new DateTime();
        # get the start time since to look for requests
        $start = (clone $now)->modify("-{$window} seconds");
        # define type of rate limit by type
        $filter = $type === 'ADDR' ? 'remote_address = :remote_address' : 'api_key = :api_key';
        # compose the query
        $query = "SELECT COUNT(*) as request_made FROM request_logs WHERE {$filter} AND endpoint = :endpoint AND request_time >= :start";
        # extract the requests in timeframe
        $stmt = $this->db->prepare($query);
        # define column based on type
        $key = $type === 'ADDR' ? 'remote_address' : 'api_key';
        # execute the query
        $stmt->execute([
            $key => $identifier,
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
        $this->storeRequestLog($type, $identifier, $endpoint);
        # allow the request
        return false;
    }
}
