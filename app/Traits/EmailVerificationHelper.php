<?php

namespace App\Traits;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait EmailVerificationHelper
{

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
        $items = DB::table('email_verification')->where('email', '=', $email)->orderBy('created_at', 'desc')->get();
        if (count($items) >= 3) {
            $item = $items->first();
            $createdAt = Carbon::parse($item->created_at);
            $expiresAt = $createdAt->addSeconds(90);
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
}
