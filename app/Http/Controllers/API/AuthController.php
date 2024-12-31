<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="API Documentation",
 *     version="1.0.0",
 *     description="This is the API documentation for the project.",
 *     @OA\Contact(
 *         email="contact@example.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 */

class AuthController extends Controller
{
/**
 * @OA\Post(
 *     path="/api/register",
 *     tags={"Authentication"},
 *     summary="Register a new user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"name", "email", "password", "age", "city"},
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", example="john@example.com"),
 *                 @OA\Property(property="password", type="string", example="password123"),
 *                 @OA\Property(property="age", type="integer", example=25),
 *                 @OA\Property(property="city", type="string", example="New York")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User registered successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="User Registered Successfully"),
 *             @OA\Property(property="user", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validation Error"),
 *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
 *         )
 *     )
 * )
 */
    public function register(Request $request)
    {
        $validateUser = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|min:6',
            'age' => 'required|integer',
            'city' => 'required|string|max:30',
        ]);

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validateUser->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'age' => $request->age,
            'city' => $request->city,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User Registered Successfully',
            'user' => $user,
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Authentication"},
     *     summary="Login a user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User Logged in Successfully"),
     *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."),
     *             @OA\Property(property="token_type", type="string", example="bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Email & Password does not match")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $validateUser = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validateUser->errors()
            ], 422);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $authUser = Auth::user();
            return response()->json([
                'status' => true,
                'message' => 'User Logged in Successfully',
                'token' => $authUser->createToken("API Token")->plainTextToken,
                'token_type' => 'bearer'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Email & Password does not match',
            ], 401);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Authentication"},
     *     summary="Logout a user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="You logged out successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json([
            'status' => true,
            'message' => 'You logged out successfully',
        ], 200);
    }
}




















































































































































































































// namespace App\Http\Controllers\Api;  

// use App\Http\Controllers\Controller; 
// use Illuminate\Http\Request;
// use App\Models\User;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Validator;
// use OpenApi\Annotations as OA;

// class AuthController extends Controller
// {
//      /**
//      * @OA\Info(
//      *     title="My API",
//      *     version="1.0.0",
//      *     description="This is the API documentation for my project",
//      *     termsOfService="http://example.com/terms",
//      *     contact={
//      *         @OA\Contact(
//      *             email="contact@example.com"
//      *         )
//      *     },
//      *     license={
//      *         @OA\License(
//      *             name="MIT",
//      *             url="https://opensource.org/licenses/MIT"
//      *         )
//      *     }
//      * )
//      */
    
//      /**
//      * @OA\Post(
//      *     path="/api/register",
//      *     tags={"Authentication"},
//      *     summary="Register a new user",
//      *     @OA\RequestBody(
//      *         required=true,
//      *         @OA\JsonContent(
//      *             required={"name", "email", "password", "age", "city"},
//      *             @OA\Property(property="name", type="string", example="John Doe"),
//      *             @OA\Property(property="email", type="string", example="john@example.com"),
//      *             @OA\Property(property="password", type="string", example="password123"),
//      *             @OA\Property(property="age", type="integer", example=25),
//      *             @OA\Property(property="city", type="string", example="New York")
//      *         )
//      *     ),
//      *     @OA\Response(
//      *         response=201,
//      *         description="User registered successfully"
//      *     ),
//      *     @OA\Response(
//      *         response=422,
//      *         description="Validation error"
//      *     )
//      * )
//      */
//     public function register(Request $request){
//         $validateUser = Validator::make($request->all(),[ 
//             'name'=>'required',
//             'email'=>'required|string|unique:users,email',
//             'password'=>'required|min:6',
//             'age'=>'required|integer',
//             'city'=>'required|string|max:30',
//         ]);
//         if($validateUser->fails()){
//             return response()->json([
//                 'status'=>false,
//                 'messsage'=>'Validation Error',
//                 'errors'=>$validateUser->errors()->all()
//             ],401);
//         }
//         $user = User::create([
//             'name'=>$request->name,
//             'email'=>$request->email,
//             'password'=>$request->password,
//             'age'=>$request->age,
//             'city'=>$request->city,

//         ]);
//         return response()->json([
//             'status'=>true,
//             'messsage'=>'User Registered Successfully',
//             'user'=>$user,
//         ],200);
//      }
//      /**
//      * @OA\Post(
//      *     path="/api/login",
//      *     tags={"Authentication"},
//      *     summary="Login a user",
//      *     @OA\RequestBody(
//      *         required=true,
//      *         @OA\JsonContent(
//      *             required={"email", "password"},
//      *             @OA\Property(property="email", type="string", example="john@example.com"),
//      *             @OA\Property(property="password", type="string", example="password123")
//      *         )
//      *     ),
//      *     @OA\Response(
//      *         response=200,
//      *         description="Login successful"
//      *     ),
//      *     @OA\Response(
//      *         response=401,
//      *         description="Invalid credentials"
//      *     )
//      * )
//      */
//     public function login(Request $request){
//             $validateUser = Validator::make($request->all(),[
//                 'email'=> 'required|email',
//                 'password'=>'required',
//             ]);
//         if($validateUser->fails()){
//             return response()->json([
//                 'status'=>false,
//                 'message'=>'Authentication Fails',
//                 'errors'=>$validateUser->errors()->all()
//             ],404);
//       } 
//         if(Auth::attempt(['email'=>$request->email, 'password'=>$request->password])){
//             $authUser = Auth::user();
//             return response()->json([
//                 'status'=>true,
//                 'messsage'=>'User Logged in  Successfully',
//                 'token'=>$authUser->createToken("API Token")->plainTextToken,
//                 'token_type'=>'bearer'
//             ],200);
//         }else{
//             return response()->json([
//                 'status'=>false,
//                 'message'=>'Email & Password does not match',
//             ],401);

//         }    
//     } 
//         /**
//      * @OA\Post(
//      *     path="/api/logout",
//      *     tags={"Authentication"},
//      *     summary="Logout a user",
//      *     security={{"bearerAuth":{}}},
//      *     @OA\Response(
//      *         response=200,
//      *         description="Successfully logged out"
//      *     ),
//      *     @OA\Response(
//      *         response=401,
//      *         description="Unauthorized"
//      *     )
//      * )
//      */

//     public function logout(Request $request){
//         $user = $request->user();
//         $user->tokens()->delete();
//         return response()->json([
//             'status'=>true,
//             'user'=>$user,
//             'message'=> 'You logged Out Successfully',
//         ],200);
//     }
// }
