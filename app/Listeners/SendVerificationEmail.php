<?php

namespace App\Listeners;

use App\Events\NewUserRegistered;
use App\Notifications\VerifyEmailNotification;
use App\Traits\EmailVerificationHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class SendVerificationEmail implements ShouldQueue
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
     * @param  \App\Events\NewUserRegistered  $event
     * @return void
     */
    public function handle(NewUserRegistered $event)
    {
        $token = $this->createEmailVerificationToken($event->user->email);
        $url = '/email-verification?email='.$event->user->email.'&token='.$token;
        $event->user->notify(new VerifyEmailNotification($url));
    }
}
