<?php

namespace App\Listeners;

use App\Events\ResetPassword;
use App\Notifications\ResetPasswordNotification;
use App\Traits\EmailVerificationHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendResetTokenEmail
{
    use EmailVerificationHelper;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ResetPassword  $event
     * @return void
     */
    public function handle(ResetPassword $event)
    {
        $token = $this->createResetPasswordToken($event->user->email);
        $event->user->notify(new ResetPasswordNotification($token));
    }
}
