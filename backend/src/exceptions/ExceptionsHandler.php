<?php

namespace Backend\Exceptions;

use Throwable;

class ExceptionsHandler
{

    public function handle(Throwable $exception): void
    {
        # format the response to return
        $response = $this->formatResponse($exception);
        # set the response status code
        http_response_code($response['status']);
        # set the response type as json
        header('Content-Type: application/json');
        # send the response to the client
        echo json_encode($response['body']);
    }

    private function formatResponse(Throwable $exception): array
    {
        return match (true) {
            $exception instanceof \Backend\Exceptions\ValidationException => $this->handleValidationException($exception),
        };
    }

    private function handleValidationException(ValidationException $exception)
    {
        return [
            'status' => 422,
            'body' => [
                'error' => true,
                'message' => $exception->getMessage(),
                'details' => $exception->getErrors()
            ]
        ];
    }
}
