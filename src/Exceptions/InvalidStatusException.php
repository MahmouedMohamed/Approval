<?php

namespace Mahmoued\Approval\Exceptions;

use Exception;
use Illuminate\Support\Facades\Response;

class InvalidStatusException extends Exception
{
    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        //
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return Response::json([
            'success' => false,
            'code' => 422,
            'message' => 'Status is invalid!',
            'data' => [],
        ], 422);
    }
}
