<?php

namespace App\Mail;

use App\Models\Found;
use App\Models\Lost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LostHasFound extends Mailable
{
    use Queueable, SerializesModels;

    public Lost $lost;

    public Found $found;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Lost $lost, Found $found)
    {
        $this->lost = $lost;
        $this->found = $found;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('view.name');
    }
}
