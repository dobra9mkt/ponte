<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SenhaRedefinida extends Notification implements ShouldQueue
{
    use Queueable;

    /** @return list<string> */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('ponte.emails.senha_redefinida.assunto'))
            ->view(['emails.mensagem', 'emails.mensagem-texto'], [
                'titulo' => __('ponte.emails.senha_redefinida.assunto'),
                'texto' => __('ponte.emails.senha_redefinida.texto'),
                'botao' => __('ponte.emails.senha_redefinida.botao'),
                'url' => route('equipe.entrar'),
            ]);
    }
}
