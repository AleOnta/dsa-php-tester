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
            $exception instanceof \Error => $this->handleErrors($exception),
            $exception instanceof \PDOException => $this->handleDatabaseException($exception),
            $exception instanceof ValidationException => $this->handleValidationException($exception),
            $exception instanceof NotFoundException => $this->handleExceptionWithNoDetails(404, $exception),
            $exception instanceof MissingApiKeyException => $this->handleExceptionWithNoDetails(400, $exception),
            $exception instanceof InvalidApiKeyException => $this->handleExceptionWithNoDetails(401, $exception),
            $exception instanceof InvalidRequestException => $this->handleInvalidRequestException($exception),
            $exception instanceof MissingParameterException => $this->handleMissingParameterException($exception),
            default => $this->handleDefaultException($exception)
        };
    }

    private function handleErrors(\Error $error)
    {
        return [
            'status' => 500,
            'body' => [
                'error' => true,
                'message' => 'A critical error has occured.',
                'details' => $error->getMessage(),
                'file' => $error->getFile(),
                'line' => $error->getLine()
            ]
        ];
    }

    private function handleExceptionWithNoDetails(int $code, \Exception $e)
    {
        return [
            'status' => $code,
            'body' => [
                'error' => true,
                'message' => $e->getMessage()
            ]
        ];
    }

    private function handleDefaultException(Exception $exception)
    {
        return [
            'status' => 500,
            'body' => [
                'error' => true,
                'message' => 'An unexpected exception occurred.',
                'details' => $exception->getMessage()
            ]
        ];
    }

    private function handleDatabaseException(\PDOException $exception)
    {
        return [
            'status' => 500,
            'body' => [
                'error' => 'Database error',
                'message' => 'An unexpected database exception occurred.',
                'details' => $exception->getMessage()
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
