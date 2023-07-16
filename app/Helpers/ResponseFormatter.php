<?php
namespace App\Helpers;

class ResponseFormatter{

    protected static $response = [
        'message' => null,
        'data' => null,
    ];

    /**
     * Give success response.
     */
    public static function success($message = null, $data = null)
    {
        self::$response['message'] = $message;
        self::$response['data'] = $data;

        return response()->json(self::$response,200);
    }

    /**
     * Give error response.
     */
    public static function error($message = null, $data = null, $code = 400)
    {
        self::$response['message'] = $message;
        self::$response['data'] = $data;

        return response()->json(self::$response, $code);
    }
}