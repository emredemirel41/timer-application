<?php

namespace App\Traits;

trait ApiResponser
{
    protected function successResponse($data, $message = null, $code = 200)
    {
        return response()->json([
            'code' => $code,
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function errorResponse($message = null, $code, $data = null)
    {
        return response()->json([
            'code' => $code,
            'status' => false,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
