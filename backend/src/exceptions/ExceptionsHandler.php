<?php

namespace Backend\Exceptions;

use Exception;

class ExceptionsHandler
{

    public function handle(\Throwable $exception): void
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

    private function formatResponse(\Throwable $exception): array
    {
        return match (true) {
            $exception instanceof \Backend\Exceptions\ValidationException => $this->handleValidationException($exception),
            $exception instanceof \Backend\Exceptions\MissingParameterException => $this->handleMissingParameterException($exception),
            $exception instanceof \Backend\Exceptions\InvalidRequestException => $this->handleInvalidRequestException($exception),
            default => $this->handleDefaultException($exception)
        };
    }

    private function handleDefaultException(Exception $exception)
    {
        return [
            'status' => 500,
            'body' => [
                'error' => true,
                'message' => $exception->getMessage()
            ]
        ];
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

    private function handleMissingParameterException(MissingParameterException $exception)
    {
        return [
            'status' => 400,
            'body' => [
                'error' => true,
                'message' => $exception->getMessage(),
                'details' => $exception->getErrors()
            ]
        ];
    }

    private function handleInvalidRequestException(InvalidRequestException $exception)
    {
        return [
            'status' => 400,
            'body' => [
                'error' => true,
                'message' => $exception->getMessage(),
                'details' => $exception->getErrors()
            ]
        ];
    }
}
