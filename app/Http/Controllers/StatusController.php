<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Custom response using to Ajax JQuery 
class StatusController extends Controller
{
	/**
     * Success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function successResponse($message, $result = [], $code = 200)
    {
    	$response = [
            'success' => true,
            'message' => $message,
            'result'  => $result
        ];

        return response()->json($response, $code);
    }

    /**
     * Error response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function errorResponse($message, $result = [], $code = 400)
    {
    	$response = [
            'success' => false,
            'message' => $message,
            'result'  => $result
        ];

        return response()->json($response, $code);
    }
    
}
