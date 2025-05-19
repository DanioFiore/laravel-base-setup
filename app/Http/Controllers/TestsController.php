<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="Test controller",
 *     version="1.0.0",
 *     description="API di test"
 * )
 */

class TestsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/test",
     *     summary="test endpoint",
     *     tags={"Tests"},
     *     @OA\Response(
     *         response=200,
     *         description="Risposta dal test"
     *     )
     * )
     */
    public function test()
    {
        return ApiResponse::handle(function() {
            return 'test success';
        });
    }
}
