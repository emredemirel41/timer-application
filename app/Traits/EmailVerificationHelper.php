<?php

namespace App\Traits;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait EmailVerificationHelper
{
      //EMAIL VERIFICATION FUNCTIONS START

    public function createEmailVerificationToken($email)
    {
        $token = uniqid();
        DB::table('email_verification')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        return $token;
    }

    public function checkEmailVerificationToken($email, $token)
    {
        $result = DB::table('email_verification')->where('email', '=', $email)->where('token', '=', $token)->exists();
        if ($result) {
            User::where('email', $email)->first()->update([
                'email_verified_at' => Carbon::now()
            ]);
            DB::table('email_verification')->where('email', $email)->delete();
        }

        return $result ? true : false;
    }

    public function checkEmailRequestCounter($email)
    {
        $limit = 10;
        $time_limit_in_sec = 90;
        $items = DB::table('email_verification')->where('email', '=', $email)->orderBy('created_at', 'desc')->get();
        if (count($items) >= $limit) {
            $item = $items->first();
            $createdAt = Carbon::parse($item->created_at);
            $expiresAt = $createdAt->addSeconds($time_limit_in_sec);
            $now = Carbon::now();

            if ($now->greaterThanOrEqualTo($expiresAt)) {
                // passed more than 90 sn
                DB::table('email_verification')->where('email', $email)->delete(); //delete old records.
                return true;
            } else {
                // passed less than 90 sn
                return false;
            }
        } else {
            return true;
        }
    }

    //EMAIL VERIFICATION FUNCTIONS END

    //******************************* */

    //RESET PASSWORD FUNCTIONS START

    public function createResetPasswordToken($email)
    {
        $token = mt_rand(100000, 999999);
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        return $token;
    }

    public function checkResetPasswordCounter($email)
    {
        $limit = 3;
        $time_limit_in_sec = 90;
        $items = DB::table('password_resets')->where('email', '=', $email)->orderBy('created_at', 'desc')->get();
        if (count($items) >= $limit) {
            $item = $items->first();
            $createdAt = Carbon::parse($item->created_at);
            $expiresAt = $createdAt->addSeconds($time_limit_in_sec);
            $now = Carbon::now();

            if ($now->greaterThanOrEqualTo($expiresAt)) {
                // passed more than 90 sn
                DB::table('password_resets')->where('email', $email)->delete(); //delete old records.
                return true;
            } else {
                // passed less than 90 sn
                return false;
            }
        } else {
            return true;
        }
    }

    public function checkResetPasswordToken($email, $token)
    {
        $time_limit_in_min = 10;
        $result = DB::table('password_resets')->where('email', $email)->where('token', $token)->first();
        if(!$result){
            return false;
        }

        $createdAt = Carbon::parse($result->created_at);
        $expiresAt = $createdAt->addMinutes($time_limit_in_min);
        $now = Carbon::now();
        if ($now->greaterThanOrEqualTo($expiresAt)) {
            return false;
        }

        return true;
    }

    //RESET PASSWORD FUNCTIONS END
    //******************************* */
}
