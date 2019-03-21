<?php

namespace Proto\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Proto\Models\User;

class ProductBulkUpdateNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $log;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $log)
    {
        $this->log = $log;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->to('omnomcom@' . config('proto.emaildomain'), 'OmNomCom Committee')
            ->to('treasurer@' . config('proto.emaildomain'), config('proto.treasurer'))
            ->subject('OmNomCom bulk product stock update.')
            ->view('emails.omnomcom.bulkproductupdate');
    }
}
