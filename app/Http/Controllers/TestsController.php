<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use Illuminate\Http\Request;

class TestsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/test",
     *     summary="Test endpoint",
     *     description="A simple test endpoint to verify API functionality",
     *     operationId="testEndpoint",
     *     tags={"Test"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="string",
     *                 example="test success"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Internal server error"
     *             )
     *         )
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
