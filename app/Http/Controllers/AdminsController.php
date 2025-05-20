<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/admins",
     *     summary="List admin users",
     *     description="Retrieves a list of all users who are administrators. Requires admin privileges.",
     *     operationId="listAdmins",
     *     tags={"Admin"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved list of admin users",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     description="Admin user details",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Admin User"),
     *                     @OA\Property(property="email", type="string", format="email", example="admin@example.com")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - User does not have permission",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="You do not have permission to perform this action")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return ApiResponse::handle(function () {
            
            if (!Auth::user()->is_admin) {
                throw new Exception('You do not have permission to perform this action');
            }

            $admins = User::where('is_admin', true)->get();

            return $admins;
        });
    }

    /**
     * @OA\Patch(
     *     path="/v1/users/{id}",
     *     summary="Update user admin status",
     *     description="Updates the 'is_admin' status of a specific user identified by ID. Requires admin privileges.",
     *     operationId="updateUserAdminStatus",
     *     tags={"Admin"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user whose admin status is to be updated",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="New admin status",
     *         @OA\JsonContent(
     *             required={"is_admin"},
     *             @OA\Property(property="is_admin", type="boolean", example=true, description="Set to true for admin, false for regular user")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Admin status updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="string", example="Admin status updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="array",
     *                     @OA\Items(type="string", example="The selected id is invalid.")
     *                 ),
     *                 @OA\Property(
     *                     property="is_admin",
     *                     type="array",
     *                     @OA\Items(type="string", example="The is admin field must be true or false.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - User does not have permission",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="You do not have permission to perform this action")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        return ApiResponse::handle(function () use ($request) {

            $validator = Validator::make(array_merge($request->all(), ['id' => $id]), [
                'id' => ['bail', 'required', 'integer', 'exists:users,id'],
                'is_admin' => ['bail', 'required', 'boolean']
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            if (!Auth::user()->is_admin) {
                throw new Exception('You do not have permission to perform this action');
            }

            $admin = User::findOrFail($request->id);
            $admin->is_admin = $request->is_admin;
            $admin->save();

            return 'Admin status updated successfully';
        });
    }
}
