<?php
namespace App\Helpers;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class ResponseFormatter{

    protected static $response = [
        'message' => null,
        'data' => null,
    ];

    /**
     * Give success response.
     */
    public static function success($message = 'Success!', $data = null,$commit=false)
    {
        self::$response['message'] = $message;
        self::$response['data'] = $data;
        if($commit)
        DB::commit();
        abort(response()->json(self::$response,200));
    }

    /**
     * Give error response.
     */
    public static function error($message = null, $data = null, $code = 400)
    {
        self::$response['message'] = $message;
        self::$response['data'] = $data;
        abort(response()->json(self::$response, $code));
    }

    public static function queryError(QueryException $error,
    $duplicate='Duplicate error!',
    $other='Something went Wrong!',
    $rollback=false){
        $code=$error->errorInfo[1];
        $msg=$code==1062?$duplicate:$other;
        if($rollback)
        DB::rollBack();
        abort(
            ResponseFormatter::error($msg,[
                'errorInfo'=>$error->errorInfo[2]
            ],422)
        );
    }
}