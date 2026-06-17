<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LoanSubmittedNotification extends Notification
{
    use Queueable;

    public $loan;

    public function __construct($loan)
    {
        $this->loan = $loan;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'New Loan Request',
            'message' => $this->loan->user->name .
                ' applied for KES ' .
                number_format($this->loan->amount),

            'loan_id' => $this->loan->id,

            'url' => route('loans.index')
        ];
    }
}