<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Events\NewUserRegistered;
use App\Http\Resources\User\UserResource;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:50',
            'email' => 'required|string|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|max:20|confirmed',
            'password_confirmation' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('The given data was invalid.', 422, $validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $data['user'] =  new UserResource($user);
        $data['access_token'] =  $user->createToken('RemolyApp')->plainTextToken;

        event(new NewUserRegistered($user)); //Register Event Triggered
    
        return $this->successResponse($data, 'User created successfully',201);
    }

    /**
     * Login the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|max:20',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('The given data was invalid.', 422, $validator->errors());
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->errorResponse('These credentials do not match our records.', 401, null);
        }

        $user = User::where('email', $request->email)->first();

        $data['user'] =  new UserResource($user);
        $data['access_token'] =  $user->createToken('RemolyApp')->plainTextToken;

        return $this->successResponse($data, 'User login successfully',200);
    }

    /**
     * Logout the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out'
        ]);
    }

}
