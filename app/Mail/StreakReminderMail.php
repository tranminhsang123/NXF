<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StreakReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public array $roadmap
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Nhắc học: streak của bạn sắp đứt')
            ->view('emails.streak-reminder');
    }
}
