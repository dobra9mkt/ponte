<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Lang;

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
        $chaveTitulo = 'entregas.email_titulo_'.$this->evento;

        return new Content(view: 'emails.mensagem', text: 'emails.mensagem-texto', with: [
            'titulo' => __(Lang::has($chaveTitulo) ? $chaveTitulo : 'entregas.email_'.$this->evento, $this->dados),
            'saudacao' => $this->saudacao(),
            'texto' => __('entregas.email_texto_'.$this->evento, $this->dados),
            'botao' => __('entregas.email_botao_'.$this->evento),
            'url' => $this->url,
        ]);
    }

    /** Só os e-mails ao cliente levam o nome; os da equipe saem sem saudação. */
    private function saudacao(): ?string
    {
        if (! array_key_exists('nome', $this->dados)) {
            return null;
        }

        return $this->dados['nome'] !== ''
            ? __('entregas.email_saudacao', ['nome' => $this->dados['nome']])
            : __('entregas.email_saudacao_sem_nome');
    }
}
