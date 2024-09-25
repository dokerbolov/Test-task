<?php

namespace Helpers;

class Responses
{
    /**
     *
     * @param mixed $data
     * @param int $statusCode
     * @return void
     */
    public function success($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
        exit;
    }

    /**
     *
     * @param string $message
     * @param int $statusCode
     */
    public function error($message, $statusCode = 400)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
        exit;
    }
}
