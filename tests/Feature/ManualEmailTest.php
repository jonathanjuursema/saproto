<?php

use App\Mail\ManualEmail;
use App\Models\Activity;
use App\Models\Email;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

it('sends a manual email to an event', function () {
    Mail::fake();
    /** @var Email $email */
    $email = Email::factory(['to_event' => true, 'time' => Carbon::now()->subMinutes(2)->getTimestamp(), 'sender_address' => 'board'])->has(Event::factory()->has(Activity::factory()->has(User::factory(), 'allUsers')))->create();
    $this->artisan('proto:emailcron')->assertSuccessful()
        ->expectsOutput('There are 1 queued e-mails.');

    Mail::assertQueued(ManualEmail::class, function ($mail) use ($email): true {
        $mail->build();
        $this->assertTrue($mail->hasFrom('board@'.Config::string('proto.emaildomain')));
        $this->assertTrue($mail->hasTo($email->recipients()->first()->email));

        return true;
    });
});
