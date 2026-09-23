<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifikasiAntreanMendekati extends Mailable
{
    use Queueable, SerializesModels;

    public $antreanTujuan;
    public $antreanDipanggil;
    public $profilPengunjung;
    public $loket;

    /**
     * Create a new message instance.
     */
    public function __construct($antreanTujuan, $antreanDipanggil)
    {
        $this->antreanTujuan    = $antreanTujuan;
        $this->antreanDipanggil = $antreanDipanggil;
        $this->profilPengunjung = $antreanTujuan->pengunjung;
        $this->loket            = $antreanTujuan->loket;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $nomor = $this->antreanTujuan->nomor_antrian;
        $namaLoket = $this->loket->nama_loket ?? 'Loket Pelayanan';
        return new Envelope(
            subject: "🔔 Giliran Anda Mendekati! Nomor Antrean [{$nomor}] - {$namaLoket}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notifikasi_antrean_mendekati',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
