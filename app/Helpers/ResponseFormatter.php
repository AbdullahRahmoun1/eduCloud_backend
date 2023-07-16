<?php
namespace App\Helpers;

class ResponseFormatter{

    protected static $response = [
        'message' => null,
        'data' => null,
    ];

    protected static $headers = [
        "Access-Control-Allow-Origin"=>"*",
        "Access-Control-Allow-Method"=>"GET, POST, PUT, PATCH, DELETE, OPTIONS",
        "Access-Control-Allow-Headers"=>"Content-Type, Authorization",
    ];

    /**
     * Give success response.
     */
    public static function success($data = null, $message = null)
    {
        self::$response['message'] = $message;
        self::$response['data'] = $data;

        return response()->json(self::$response,200, self::$headers);
    }

    /**
     * Give error response.
     */
    public static function error($data = null, $message = null, $code = 400)
    {
        self::$response['message'] = $message;
        self::$response['data'] = $data;

        return response()->json(self::$response, $code, self::$headers);
    }
}