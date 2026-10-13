# Registro de decisões

Mais recente primeiro. Cada mudança de regra ganha uma linha aqui antes de ir para o código.

| Data | Decisão | Quem |
|---|---|---|
| 13/10/2026 | Privacidade avisa que registramos entradas e ações no portal com IP e navegador; ficha do cliente deixa de mostrar o "último acesso" | Rose |
| 13/10/2026 | Histórico de atividades discreto: recolhido na ficha do cliente e fora do painel | Rose |
| 13/10/2026 | Área da equipe com barra lateral e telas de detalhe em duas colunas (conteúdo + painel lateral) | Rose |
| 13/10/2026 | Pedido de ajuste com rascunho salvo sozinho e "Onde?" por item; não gasta revisão até enviar (RN-21, RN-21a) | Rose |
| 13/10/2026 | Confirmação de revisão avisa que ela vale para o serviço inteiro e quantas restam (RN-22). Continuam 3 por frente | Rose |
| 13/10/2026 | Prazo de resposta por etapa, padrão 3 dias úteis; 5 em "Mapa do site e textos" e "Site no link de teste" (RN-30) | Rose |
| 13/10/2026 | Botão "Avisar no WhatsApp" para a equipe, com mensagem pronta; o portal não envia nada (RN-34) | Rose |
| 13/10/2026 | Escolha de propostas no celular com abas e tela cheia | Rose |
| 13/10/2026 | Confirmação antes de aprovar em etapas que pedem (ex.: site de teste) (RN-43) | Rose |
| 13/10/2026 | Arquivamento 30 dias após concluir; quem baixa o pacote é a equipe, não o cliente | Rose |
| 13/10/2026 | Contrato anexado visível só para a equipe (Rose e Elias) | Rose |
| 13/10/2026 | Ao concluir, "Arquivar e remover": pacote baixado e dados apagados do servidor, para evitar vazamento | Rose |
| 13/10/2026 | Convite de acesso nunca automático: botão "Enviar acesso" | Rose |
| 13/10/2026 | Cadastro com endereço e contrato; "Adicionar serviço" com prazos; etapas editáveis em cada projeto | Rose |
| 13/10/2026 | Contador de revisões em destaque | Rose |
| 13/10/2026 | 1 login por cliente; e-mail e senha com "lembrar de mim"; remetente contato@dobra9.com.br; identidade da Dobra 9; Hostinger atual | Rose |
| 13/10/2026 | Endereço ponte.dobra9.com.br; calendario.dobra9.com.br é de colaboradores e não é usado | Rose |
| 13/10/2026 | Rebranding: direção visual + 2 propostas com recomendação; escolher não gasta revisão; propostas não se misturam | Rose |
| 13/10/2026 | 3 revisões por frente; depois, ajuste extra cobrado | Rose |
| 13/10/2026 | Só o processo de trabalho: cobrança e gestão de contratos fora do portal | Rose |
| 13/10/2026 | Laravel, simples e seguro; equipe = Rose e Elias | Rose |

## Decisões técnicas (propostas no planejamento, a confirmar na fase 1)

| Assunto | Proposta |
|---|---|
| Destino das cópias de segurança | Armazenamento compatível com S3 fora da Hostinger (ex.: Backblaze B2) |
| Interatividade | Alpine.js na build CSP, sem Livewire/React |
| Login e 2FA | Laravel Fortify com telas próprias |
| Limite de arquivo | 50 MB; vídeos maiores por link |
| Validade do convite | 72 horas |

## Decisões técnicas da fase 1

| Assunto | Decisão |
|---|---|
| Versão | Laravel 13 (estável em 13/10/2026), PHP 8.3 |
| Ambiente local | Laragon (PHP 8.3, MySQL 8.4, Mailpit para ver os e-mails sem enviar). Desenvolvimento com SQLite em arquivo; testes com SQLite em memória; produção com MySQL |
| Login e 2FA | Do Fortify usamos só o 2FA (código do aplicativo, QR, códigos de recuperação). Telas e rotas de entrar são nossas, separadas para cliente e equipe |
| Convite e redefinição de senha | E-mails vão cifrados para a fila, para o link nunca ficar legível no banco |
| PHPStan | Configuração em `phpstan.neon.dist` (o antivírus do computador bloqueia o nome `phpstan.neon`) |
