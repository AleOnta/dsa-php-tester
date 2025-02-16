<?php

namespace Backend\Middlewares;

use Closure;

class RateLimitApiKeyMiddleware extends Middleware
{
    private \PDO $db;
    private string $endpoint;
    private int $limit;
    private int $window;

    public function __construct(\PDO $db, string $endpoint, int $limit, int $window)
    {
        $this->db = $db;
        $this->endpoint = $endpoint;
        $this->limit = $limit;
        $this->window = $window;
    }

    public function handle($request, Closure $next)
    {
        # instantiate rate limiter class
        $rateLimiter = new \Backend\Core\RateLimiter($this->db);
        # retrieve api key from request headers
        $apikey = (new \Backend\Core\Request())->getApiKey();
        # check if requests are exceeding limits
        if ($rateLimiter->checkRateLimit('APIKEY', $apikey, $this->endpoint, $this->limit, $this->window)) {
            # block the request
            $this->return(
                429,
                'Too Many Requests',
                ['message' => 'You have exceeded the limit of requests available for this endpoint. Slow down']
            );
        }
        # allow the request
        $next();
    }
}
