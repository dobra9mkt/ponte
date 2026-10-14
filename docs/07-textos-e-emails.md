# 07 · Textos e e-mails

Tom da Dobra 9: direto, cordial, sem jargão, sem promessas absolutas. Tratar o cliente por "você".
Estes textos vão para `lang/pt_BR/ponte.php` (chaves entre colchetes). `:frente`, `:n`, `:total`
etc. são substituídos pelo sistema.

## Mensagens do portal

| Chave | Texto |
|---|---|
| `[ajuste.confirmar]` | Você vai usar a revisão :n de :total do :frente. Ela vale para o :frente inteiro; depois desta, restam :restantes. Confira se incluiu todos os ajustes: o que chegar depois conta como uma nova revisão. |
| `[ajuste.rascunho_salvo]` | Rascunho salvo às :hora. Você pode sair e continuar depois: nada é enviado até você confirmar. |
| `[ajuste.continuar]` | Continuar ajustes (:qtd itens) |
| `[ajuste.ultima]` | Esta é a sua última revisão incluída no :frente. Ajustes depois dela serão orçados à parte. |
| `[boas_vindas.titulo]` | Bem-vindo à Ponte, o sistema que nos conecta a você em cada etapa. |
| `[arquivado]` | Seu projeto foi concluído e arquivado. Obrigado pela confiança! Para novos serviços, fale com a gente pelos nossos canais oficiais em dobra9.com.br. |
| `[versao_final]` | Versão final |
| `[versao_final.celebracao]` | Etapa concluída com sucesso! Para seguirmos, clique em Ver. |
| `[versao_final.o_que_fazer]` | O que você precisa fazer agora |
| `[versao_final.botao]` | Autorizar versão final |
| `[versao_final.autorizado]` | Autorizado · Autorizado em :data, às :hora · Autorização registrada. |
| `[email.autorizada]` | :empresa autorizou a versão final de :etapa |
| `[versao_final.pendente]` | Autorização pendente |
| `[versao_final.sem_ajustes]` | A versão final só pode ser autorizada. |
| `[ajuste.esgotadas]` | As :total revisões do :frente já foram usadas. Você pode solicitar um ajuste extra: a Dobra 9 envia o orçamento antes de começar. |
| `[ajuste.extra_enviado]` | Pedido de ajuste extra enviado. A Dobra 9 vai te mandar o orçamento; o trabalho começa depois do acerto. |
| `[ajuste.enviado]` | Ajustes enviados. Avisaremos quando a nova versão estiver pronta. |
| `[ajuste.devolvida]` | Este ajuste era uma correção nossa e não contou como revisão. Você continua com :restantes de :total disponíveis. |
| `[escolha.regra]` | Escolha uma proposta como base. Elementos das outras podem ser pedidos na revisão, mas não misturamos propostas inteiras. |
| `[escolha.recomendada]` | Recomendada pela Dobra 9 |
| `[aprovar.confirmar]` | Aprovar :etapa (versão :versao)? Depois de aprovado, a etapa não volta. |
| `[aprovado]` | Aprovado em :data às :hora por :nome. Obrigado! Seguimos para a próxima etapa. |
| `[esperando]` | Aguardando a sua resposta há :dias dias úteis. O cronograma anda junto com a sua resposta. |
| `[precisa_de_voce]` | Precisa de você |
| `[tudo_em_dia]` | Tudo em dia! A Dobra 9 está trabalhando no seu projeto. |
| `[revisoes.contador]` | :usadas de :total revisões usadas |
| `[revisoes.nenhuma]` | Nenhuma revisão usada |
| `[material.recebido]` | Recebemos, obrigado! Vamos conferir e seguir. |
| `[material.complemento]` | A Dobra 9 pediu um complemento: :mensagem |
| `[login.erro]` | E-mail ou senha incorretos. |
| `[login.bloqueado]` | Muitas tentativas. Tente de novo em :segundos segundos. |
| `[convite.invalido]` | Este link não vale mais. Peça um novo à Dobra 9. |
| `[senha.regra]` | Use pelo menos 10 caracteres. Evite senhas que você já usa em outros sites. |
| `[senha.email_enviado]` | Se este e-mail estiver cadastrado, você vai receber o link em alguns minutos. |

## WhatsApp (RN-34)

Mensagem pronta do botão "Avisar no WhatsApp" (a equipe pode editar antes de enviar):

| Situação | Texto |
|---|---|
| Entrega publicada | Oi, :contato! :etapa do :frente está pronto para você ver e aprovar: :link |
| Pendência liberada | Oi, :contato! Para seguir com o :frente, precisamos que você envie :etapa pelo portal: :link |
| Lembrete | Oi, :contato! Só lembrando: :etapa está esperando a sua resposta no portal: :link |

## Boas-vindas (C3)

1. **Responda dentro do prazo.** Em geral são 3 dias úteis; o prazo de cada entrega aparece na tela. O cronograma anda junto com a sua resposta.
2. **Você tem 3 revisões por serviço.** O contador fica sempre visível. Depois da 3ª, ajustes são orçados à parte.
3. **Uma revisão é um pacote.** Junte todos os ajustes e envie de uma vez. Erros nossos não contam como revisão.

Botão: **Entendi, vamos lá**

## E-mails

> 14/10/2026: os e-mails de aprovação seguem RN-72 a RN-75 e vão para o **e-mail de avisos** do
> cliente. Tom sóbrio, sem exclamação (ver `14-fluxo-equipe.md`). Os demais textos do cliente serão
> revistos na fase do login do cliente.

Todos: remetente **Dobra 9 <contato@dobra9.com.br>**, layout simples com o logo, um botão, rodapé
"Você recebeu este e-mail porque tem um projeto com a Dobra 9." Nunca anexar arquivos nem copiar
itens de ajuste (SEG-46).

| Para | Quando | Assunto | Corpo (resumo) | Botão |
|---|---|---|---|---|
| Cliente | "Enviar acesso" (RN-02) | Seu acesso ao acompanhamento do projeto · Dobra 9 | Olá, :contato! Preparamos um espaço para você acompanhar o projeto da :empresa, aprovar as entregas e enviar o que precisarmos. Crie sua senha pelo botão abaixo; o link vale por 72 horas. | Criar minha senha |
| Cliente (e-mail de avisos) | Mostrar para cliente (RN-72) | Aprovação pendente: :etapa · :frente | A :rodada de :etapa do :frente está disponível para sua revisão. Prazo para resposta: :prazo. | Revisar |
| Cliente (e-mail de avisos) | 1 dia útil antes do prazo (RN-74) | Lembrete: aprovação de :etapa vence em :prazo | A :rodada de :etapa do :frente aguarda sua resposta até :prazo. | Revisar |
| Cliente | Escolha publicada | Suas propostas de :frente chegaram | Preparamos :n propostas para você escolher, com a nossa recomendação. | Ver propostas |
| Cliente | Pendência liberada | Precisamos de :etapa | Para seguir com o :frente, precisamos que você envie: :etapa, até :prazo. | Enviar agora |
| Cliente | Revisão devolvida | Seu ajuste não contou como revisão | Corrigimos um erro nosso em :etapa e devolvemos a revisão. | Ver |
| Cliente | Redefinir senha | Redefinir sua senha · Dobra 9 | Recebemos um pedido para redefinir sua senha. Se não foi você, ignore este e-mail. | Redefinir senha |
| Equipe | Prazo vencido (RN-75) | Prazo vencido: :empresa · :etapa | A :rodada de :etapa (:frente) venceu em :prazo sem resposta do cliente. | Abrir fase |
| Equipe | Cliente aprovou / escolheu | ✔ :empresa aprovou :etapa | :nome aprovou :etapa (v:versao) às :hora. | Abrir etapa |
| Equipe | Cliente pediu ajuste | :empresa pediu ajustes em :etapa (revisão :n de :total) | :qtd itens de ajuste. | Ver ajustes |
| Equipe | Ajuste extra | Orçar ajuste extra · :empresa | :empresa usou as :total revisões do :frente e solicitou ajuste extra em :etapa. | Ver pedido |
| Equipe | Material recebido | :empresa enviou :etapa | :qtd arquivos e mensagem. | Conferir |
| Equipe | Segunda 8h | Semana no Ponte · :data | Vence esta semana, esperando cliente, esperando vocês. | Abrir painel |
| Equipe | 30 dias após concluir | Pronto para arquivar: :frente · :empresa | O serviço foi concluído em :data. Gere o pacote, guarde e remova do servidor. | Arquivar |
| Equipe | Muitas falhas de login | ⚠ Tentativas de login suspeitas | :qtd tentativas falhas na última hora (IPs: …). | Ver registro |
