<?php

namespace App\Http\Controllers;

use App\Events\NewUserRegistered;
use App\Traits\EmailVerificationHelper;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    use EmailVerificationHelper;
    public function check_verification(Request $request){
        if(!$this->checkEmailVerificationToken($request->email,$request->token)){
            return $this->errorResponse('These credentials do not match our records.', 401, null);
        }
        return $this->successResponse(null, 'Email has been verified successfully.',200);
    }

    public function resend_verification(Request $request){
        $user = $request->user();
        //Check count of request email if 10 or above restricted for 1 min.
        if(!$this->checkEmailRequestCounter($user->email)){
            return $this->errorResponse('Too Many Requests. Please wait 90 sec', 429, null);
        }
        
        event(new NewUserRegistered($user)); 
        return $this->successResponse( null, 'Verification Email has been sent successfully.',200);
    }
}
