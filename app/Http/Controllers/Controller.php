<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function __construct(Request $request) {
    }

    protected function responseJSON($code, $status, $message, $data=null) {
        return response()->json(['code'=>$code, 'status'=>$status, 'message'=>$message, 'data'=>$data])->setStatusCode($code);
    }

    protected function success($message, $data=null) {
        return $this->responseJSON(Response::HTTP_OK, 'success', $message, $data);
    }

    protected function fail($message, $data=null) {
        return $this->responseJSON(Response::HTTP_OK, 'fail', $message, $data);
    }

    protected function error($code, $message, $data=null) {
        return $this->responseJSON($code, 'error', $message, $data);
    }

}
