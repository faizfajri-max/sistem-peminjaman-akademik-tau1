<?php
/**
 * Response Helpers
 * Helper untuk mengirim response JSON
 */

class Response {
    /**
     * Send JSON response
     */
    public static function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Send success response
     */
    public static function success($data = [], $message = 'Success') {
        self::json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], 200);
    }

    /**
     * Send error response
     */
    public static function error($message, $status = 400) {
        self::json([
            'success' => false,
            'error' => $message
        ], $status);
    }

    /**
     * Send validation error
     */
    public static function validationError($errors) {
        self::json([
            'success' => false,
            'error' => 'Validation failed',
            'errors' => $errors
        ], 422);
    }
}
