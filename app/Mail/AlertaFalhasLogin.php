<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlertaFalhasLogin extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  list<string>  $ips
     */
    public function __construct(public int $quantidade, public array $ips) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('ponte.emails.alerta_login.assunto'));
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.mensagem',
            text: 'emails.mensagem-texto',
            with: [
                'titulo' => __('ponte.emails.alerta_login.assunto'),
                'texto' => __('ponte.emails.alerta_login.texto', ['qtd' => $this->quantidade, 'ips' => implode(', ', $this->ips)]),
                'botao' => __('ponte.emails.alerta_login.botao'),
                'url' => route('equipe.painel'),
            ],
        );
    }
}
