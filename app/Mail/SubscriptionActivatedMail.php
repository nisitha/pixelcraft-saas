<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\SubscriptionPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionActivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Company $company,
        public SubscriptionPayment $payment
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your PixelCraft Subscription Has Been Activated! 🎉',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-activated',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
