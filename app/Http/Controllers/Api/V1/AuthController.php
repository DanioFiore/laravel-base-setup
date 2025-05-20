<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/register",
     *     summary="Register a new user",
     *     description="Registers a new user and returns their details and an API token.",
     *     operationId="registerUser",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User registration details",
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "confirmPassword"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="confirmPassword", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully registered",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *                 @OA\Property(property="token", type="string", example="1|abcdefg12345...")
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
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string", example="The email has already been taken.")
     *                 ),
     *                 @OA\Property(
     *                     property="confirmPassword",
     *                     type="array",
     *                     @OA\Items(type="string", example="The confirm password and password must match.")
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
    public function register(Request $request): JsonResponse
    {
        return ApiResponse::handle(function() use ($request) {

            $validator = Validator::make($request->all(), [
                'name' => ['bail', 'required'],
                'email' => ['bail', 'required', 'string', 'email', 'unique:users,email'],
                'password' => ['bail', 'required'],
                'confirmPassword' => ['bail', 'required', 'same:password']
            ]);
    
            if ($validator->fails()){
                throw new ValidationException($validator);
            }

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $token = $user->createToken('registerToken')->plainTextToken;
            $success['name'] =  $user->name;
            $success['email'] = $user->email;
            $success['token'] = $token;
    
            return $success;
        });
    }

    /**
     * @OA\Post(
     *     path="/v1/login",
     *     summary="Authenticate user",
     *     description="Authenticates a user with email and password and returns user data and an API token.",
     *     operationId="loginUser",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User login credentials",
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully authenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     description="Authenticated user details",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", format="email", example="john.doe@example.com")
     *                 ),
     *                 @OA\Property(property="token", type="string", example="1|abcdefg12345...")
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
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string", example="The selected email is invalid.")
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="array",
     *                     @OA\Items(type="string", example="The password field is required.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
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
    public function login(Request $request): JsonResponse
    {
        return ApiResponse::handle(function() use ($request) {

            $validator = Validator::make($request->all(), [
                'email'    => ['bail', 'required', 'string', 'email', 'exists:users,email'],
                'password' => ['bail', 'required', 'string']
            ]);
    
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
    
            $user = User::where('email', $request->email)->first();
    
            // check if the user exists and the provided password is correct.
            if (!$user || !Hash::check($request->password, $user->password)) {
                throw new Exception('Invalid credentials');
            }
    
            // generate a token for the authenticated user.
            $token = $user->createToken('loginToken')->plainTextToken;
    
            // return the user data and token in the response
            return [
                'user'   => $user,
                'token'  => $token,
            ];
        });
    }

    /**
     * @OA\Post(
     *     path="/v1/logout",
     *     summary="Logout user",
     *     description="Logs out the currently authenticated user by revoking their access token.",
     *     operationId="logoutUser",
     *     tags={"Auth"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="string", example="Logged out successfully")
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
     *      @OA\Response(
     *         response=403,
     *         description="Forbidden - User not authenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="User not authenticated")
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
    public function logout(): JsonResponse
    {
        return ApiResponse::handle(function() {

            $user = Auth::user();

            if (!$user) {
                throw new Exception('User not authenticated');
            }
    
            // revoke the current access token.
            $user->currentAccessToken()->delete();
    
            return 'Logged out successfully';
        });
    }
}
