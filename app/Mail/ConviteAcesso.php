<?php

namespace App\Mail;

use App\Models\Cliente;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConviteAcesso extends Mailable implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Cliente $cliente, public string $token) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('ponte.emails.convite.assunto'));
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.mensagem',
            text: 'emails.mensagem-texto',
            with: [
                'titulo' => __('ponte.emails.convite.titulo', ['contato' => $this->cliente->contato_nome]),
                'texto' => __('ponte.emails.convite.texto', ['empresa' => $this->cliente->empresa]),
                'botao' => __('ponte.emails.convite.botao'),
                'url' => route('convite', $this->token),
            ],
        );
    }
}
