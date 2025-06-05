<?php

use Symfony\Component\HttpFoundation\Response;

if (!function_exists('successResponse')) {
    function successResponse($data, $status = Response::HTTP_OK)
    {
        return response()->json([
            'success' => true,
            'data'    => $data
        ], $status);
    }
}

if (!function_exists('errorResponse')) {
    function errorResponse($message, $status = Response::HTTP_BAD_REQUEST)
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $status);
    }
}
