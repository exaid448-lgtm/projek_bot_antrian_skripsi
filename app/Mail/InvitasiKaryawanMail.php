<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitasiKaryawanMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitasi;
    public $loket;

    /**
     * Create a new message instance.
     */
    public function __construct($invitasi, $loket)
    {
        $this->invitasi = $invitasi;
        $this->loket = $loket;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Undangan Registrasi Akun Karyawan MPP Banjarbaru',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invitasi_karyawan',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
