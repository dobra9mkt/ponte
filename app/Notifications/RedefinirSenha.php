<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RedefinirSenha extends Notification implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable;

    public function __construct(public string $token) {}

    /** @return list<string> */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('ponte.emails.senha.assunto'))
            ->view(['emails.mensagem', 'emails.mensagem-texto'], [
                'titulo' => __('ponte.emails.senha.assunto'),
                'texto' => __('ponte.emails.senha.texto'),
                'botao' => __('ponte.emails.senha.botao'),
                'url' => route($notifiable->ehEquipe() ? 'equipe.senha.redefinir' : 'senha.redefinir', ['token' => $this->token, 'email' => $notifiable->email]),
            ]);
    }
}
