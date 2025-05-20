<?php

namespace App\Http\Controllers;

use Exception;
use App\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UsersController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="List all users",
     *     description="Retrieves a list of all users.",
     *     operationId="listUsers",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved list of users",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     description="User details",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", format="email", example="john.doe@example.com")
     *                 )
     *             )
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
            
            return User::all();
        });
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Get user details",
     *     description="Retrieves details for a specific user by ID. Requires authentication. Users can view their own profile or, if admin, any user's profile.",
     *     operationId="showUser",
     *     tags={"Users"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to retrieve",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved user details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="User details",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="john.doe@example.com")
     *             )
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
     *             @OA\Property(property="message", type="string", example="You cannot view other users")
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
    public function show(int $id): JsonResponse
    {
        return ApiResponse::handle(function () use ($id) {

            $validator = Validator::make(['id' => $id], [
                'id' => ['bail', 'required', 'integer', 'exists:users,id'],
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
            
            if ($id !== Auth::user()->id || Auth::user()->is_admin) {
                throw new Exception('You cannot view other users');
            }

            $user = User::findOrFail($id);

            return $user;
        });
    }

    /**
     * @OA\Patch(
     *     path="/api/users",
     *     summary="Update user details",
     *     description="Updates details for a specific user by ID provided in the request body. Authenticated users can update their own profile; admins can update any user's profile.",
     *     operationId="updateUser",
     *     tags={"Users"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User details to update",
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="integer", example=1, description="ID of the user to update"),
     *             @OA\Property(property="name", type="string", nullable=true, example="Jane Doe", description="New name for the user (optional)"),
     *             @OA\Property(property="email", type="string", format="email", nullable=true, example="jane.doe@example.com", description="New email for the user (optional)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="string", example="User updated successfully")
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
     *                     property="name",
     *                     type="array",
     *                     @OA\Items(type="string", example="The name field must be a string.")
     *                 ),
     *                  @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string", example="The email field must be a valid email address.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - No fields to update or other client errors",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No fields to update")
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
     *             @OA\Property(property="message", type="string", example="You cannot update other users")
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
    public function update(Request $request): JsonResponse
    {
        return ApiResponse::handle(function () use ($request) {

            $validator = Validator::make($request->all(), [
                'id' => ['bail', 'required', 'integer', 'exists:users,id'],
                'name' => ['bail', 'nullable', 'string', 'max:255'],
                'email' => ['bail', 'nullable', 'string', 'email', 'max:255'],
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            if ($request->input('id') !== Auth::user()->id && !Auth::user()->is_admin) {
                throw new Exception('You cannot update other users');
            }

            if (!$request->has('name') && !$request->has('email')) {
                throw new Exception('No fields to update');
            }

            $user = User::find($request->input('id'));
            
            if ($request->has('name')) {
                $user->name = $request->input('name');
            }

            if ($request->has('email')) {
                $user->email = $request->input('email');
            }

            $user->save();

            return 'User updated successfully';
        });
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Soft delete a user",
     *     description="Soft deletes a user by ID. Authenticated users can soft delete their own profile; admins can soft delete any user's profile.",
     *     operationId="softDeleteUser",
     *     tags={"Users"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to soft delete",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User soft-deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="string", example="User soft-deleted successfully")
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
     *             @OA\Property(property="message", type="string", example="You cannot delete other users")
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
    public function softDestroy(int $id): JsonResponse
    {
        return ApiResponse::handle(function () use ($id) {

            $validator = Validator::make(['id' => $id], [
                'id' => ['bail', 'required', 'integer', 'exists:users,id'],
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            if ($id !== Auth::user()->id && !Auth::user()->is_admin) {
                throw new Exception('You cannot delete other users');
            }

            $user = User::findOrFail($id);
            $user->delete();

            return 'User soft-deleted successfully';
        });
    }

    /**
     * @OA\Patch(
     *     path="/api/users/{id}/restore",
     *     summary="Restore a soft-deleted user",
     *     description="Restores a soft-deleted user by ID. Requires authentication. Authenticated users can only restore their own soft-deleted profile.",
     *     operationId="restoreUser",
     *     tags={"Users"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the soft-deleted user to restore",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User restored successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="string", example="User restored successfully")
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
     *             @OA\Property(property="message", type="string", example="You cannot restore other users")
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
    public function restore(int $id): JsonResponse
    {
        return ApiResponse::handle(function () use ($id) {

            $validator = Validator::make(['id' => $id], [
                'id' => ['bail', 'required', 'integer', 'exists:users,id'],
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            if ($id !== Auth::user()->id) {
                throw new Exception('You cannot restore other users');
            }

            $user = User::withTrashed()->findOrFail($id);
            $user->restore();

            return 'User restored successfully';
        });
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}/force-delete",
     *     summary="Permanently delete a user",
     *     description="Permanently deletes a user by ID, including soft-deleted users. Authenticated users can permanently delete their own profile; admins can permanently delete any user's profile.",
     *     operationId="forceDeleteUser",
     *     tags={"Users"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to permanently delete (can be soft-deleted)",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User permanently deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="string", example="User permanently deleted successfully")
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
     *             @OA\Property(property="message", type="string", example="You cannot delete other users")
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
    public function destroy(int $id): JsonResponse
    {
        return ApiResponse::handle(function () use ($id) {

            $validator = Validator::make(['id' => $id], [
                'id' => ['bail', 'required', 'integer', 'exists:users,id'],
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            if ($id !== Auth::user()->id && !Auth::user()->is_admin) {
                throw new Exception('You cannot delete other users');
            }

            $user = User::withTrashed()->findOrFail($id);
            $user->forceDelete();

            return 'User permanently deleted successfully';
        });
    }
}
