<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Events\NewUserRegistered;
use App\Events\ResetPassword;
use App\Http\Resources\User\UserResource;
use App\Traits\EmailVerificationHelper;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    use EmailVerificationHelper;
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
        return $this->successResponse(null, 'Logged out',200);
    }

    /**
     * Forget Password the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forget_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|exists:users,email',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('The given data was invalid.', 422, $validator->errors());
        }

        $user = User::where('email',$request->email)->first();

        if(!$this->checkResetPasswordCounter($user->email)){
            return $this->errorResponse('Too Many Requests. Please wait 90 sec', 429, null);
        }

        event(new ResetPassword($user)); //Register Event Triggered

        return $this->successResponse(null, 'Your reset code has been sent successfully',200);

    }


    public function reset_password(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|max:20|confirmed',
            'password_confirmation' => 'required'

        ]);

        if ($validator->fails()) {
            return $this->errorResponse('The given data was invalid.', 422, $validator->errors());
        }

        if(!$this->checkResetPasswordToken($request->email,$request->token)){
            return $this->errorResponse('These credentials do not match our records.', 401, null);
        }

        $user = User::where('email',$request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        DB::table('password_resets')->where('email', '=', $request->email)->delete();

        return $this->successResponse(null, 'User password has been changed successfully',200);
    }
}
