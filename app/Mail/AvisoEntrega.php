<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AvisoEntrega extends Mailable implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable;

    /** @param array<string, string> $dados */
    public function __construct(public string $evento, public string $url, public array $dados = []) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('entregas.email_'.$this->evento, $this->dados));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.mensagem', text: 'emails.mensagem-texto', with: [
            'titulo' => __('entregas.email_'.$this->evento, $this->dados),
            'texto' => __('entregas.email_texto_'.$this->evento, $this->dados),
            'botao' => __('entregas.email_botao_'.$this->evento),
            'url' => $this->url,
        ]);
    }
}
