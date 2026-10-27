# Registro de decisões

Mais recente primeiro. Cada mudança de regra ganha uma linha aqui antes de ir para o código.

| Data | Decisão | Quem |
|---|---|---|
| 27/10/2026 | "Esqueci minha senha" também na tela de entrar da equipe: só para conta com 2FA configurado, mantém o 2FA, encerra as sessões e avisa por e-mail que a senha foi trocada. Antes a senha da equipe só mudava por comando no servidor (RN-05a, SEG-09a) | Rose |
| 14/10/2026 | A publicação roda os testes em MySQL 8.4, como na Hostinger, depois que a primeira publicação falhou numa mudança de estrutura que o SQLite aceita e o MySQL recusa. No computador, os testes continuam em SQLite | Rose |
| 14/10/2026 | E-mails do portal saem pelo SMTP da Hostinger, com uma caixa de e-mail criada lá; o Resend deixa de ser usado. Primeira publicação pelo GitHub, com o Ponte como site separado na Hostinger (`ponte.dobra9.com.br`, pasta própria fora da do site) | Rose |
| 14/10/2026 | Teste de invasão local: código do 2FA passa a ter limite de 10 erros por hora por pessoa, de qualquer IP; card de serviço ou fase na lixeira devolve 404 ao cliente; prévia de PDF sem `sandbox` registrada como risco aceito (SEG-05, SEG-24) | Rose |
| 14/10/2026 | Para o cliente, a rodada aparece como "Revisão 1 de 3"; na última, aviso "Esta é a última revisão antes da entrega." (RN-17) | Rose |
| 14/10/2026 | Datas separadas: serviço com início e término próprios; fases com as suas; uma não altera a outra (RN-13) | Rose |
| 14/10/2026 | O cliente vê o serviço quando a equipe clica em Mostrar ao cliente e, dentro dele, só as fases em andamento ou finalizadas (RN-16a) | Rose |
| 14/10/2026 | Botão Mostrar/Ocultar ao cliente no card "Editar serviço"; cliente vê o serviço em linha do tempo vertical, sem o responsável, com as rodadas de aprovação marcadas (RN-16, RN-17) | Rose |
| 14/10/2026 | Botão Excluir (com confirmação) para cliente, serviço e fase; tudo vai para a Lixeira, com Restaurar. Fase com aprovação publicada também pode ser excluída (substitui "só ocultar" de RN-12) (RN-82, RN-83) | Rose |
| 14/10/2026 | Serviços antigos dos testes (Rebranding e Site) desativados no banco local; ficam só os seis do doc 14 | Rose |
| 14/10/2026 | Nova estrutura da área da equipe (doc 14): barra lateral Painel, Cadastrar cliente e Clientes (só consulta); cliente com e-mail de avisos; serviço escolhido numa lista com as fases do fluxo padrão carregadas e editáveis; fase com responsável, o que fazer, entrada e término; aprovação opcional em qualquer fase por card em rascunho, um card por rodada; Mostrar para cliente bloqueia o card, prazo de 5 dias úteis sem feriados (editável), fase "Aguardando cliente" e e-mail; lembrete na véspera e alerta de vencido à equipe; aprovou → fase segue, ajuste → nova rodada. Substitui os docs 12 e 13 e o catálogo-zero de RN-10/RN-11. Login do cliente fica para depois | Rose |
| 14/10/2026 | Seis serviços com fluxos padrão (Sistemas sob medida, Aplicativos, Criação de sites, Segurança e remoção de vírus, Servidores web Linux, Tráfego pago); tudo editável, com tela Serviços e "Outro" para serviço fora do escopo (RN-10, RN-10a) | Rose |
| 14/10/2026 | Responsável da fase em texto livre, para incluir freelancers sem acesso ao portal (RN-12). Aba Clientes só consulta confirmada. Lembrete 1 dia útil antes, fase volta a "Em andamento" após aprovar e nova rodada com os links da anterior: confirmados | Rose |
| 14/10/2026 | Detalhes do doc 14 ainda a confirmar: o dia da publicação não conta no prazo; alerta de vencido no Painel e por e-mail, uma vez; anexos da equipe de qualquer tipo, sempre baixados exceto PDF/imagens | Claude, a confirmar com a Rose |
| 14/10/2026 | Rodadas de aprovação: no máximo 3 por fase, não existe 4ª. A 3ª segue o mesmo fluxo; se o cliente pedir ajustes nela, a equipe envia a "Versão final", que o cliente só aprova; novos ajustes são orçados à parte. O card da versão final traz "Mensagem para o cliente", escrita pela equipe, sob o título "O que você precisa fazer agora"; na linha do tempo, mensagem comemorativa fixa "Etapa concluída com sucesso! Para seguirmos, clique em Ver." e o botão "Ver", sem aviso de revisões; o botão de aprovação da versão final tem texto editável e o topo do card diz "Autorização pendente" (RN-71) | Rose |
| 14/10/2026 | Cliente entra direto na linha do tempo, sem tela de lista; com dois ou mais serviços, abas abaixo das boas-vindas, com bolinha dourada onde há aprovação pendente (RN-16b) | Rose |
| 14/10/2026 | Topo do cliente enxuto: frase de boas-vindas discreta; um cartão com seletor de serviços, nome do serviço, barra de progresso e previsão | Rose (deixou o desenho com o Claude) |
| 14/10/2026 | Aba Clientes reorganizada: resumo do serviço com progresso e as aprovações dentro de cada fase, sem bloco de histórico separado (RN-81) | Rose (deixou o desenho com o Claude) |
| 14/10/2026 | Linha do tempo do cliente: rodadas de fase concluída ficam fechadas atrás de "Ver histórico"; fases em andamento sempre abertas (RN-17) | Rose |
| 14/10/2026 | Lixeira com "Excluir definitivamente" e "Esvaziar lixeira": o item some da Lixeira e não se restaura, mas os dados ficam guardados no banco (RN-82a) | Rose |
| 14/10/2026 | Minha conta do cliente: dados só leitura, baixar o próprio contrato e trocar senha; sai "Sair de todos os aparelhos" (RN-07 mudada) | Rose |
| 14/10/2026 | RN-20 a RN-29 substituídas pelo modelo de rodadas (ficam RN-21 e RN-21a); saem do código o contador de revisões do serviço, a "correção nossa", o ajuste extra, a escolha de propostas e a página antiga de fase do cliente | Rose |
| 14/10/2026 | Depois de arquivado, o link do cliente mostra a página "projeto concluído e arquivado" com os canais oficiais e dobra9.com.br (RN-65a, fase 5) | Rose |
| 14/10/2026 | Na versão final o termo é sempre "autorizar / autorizado", nunca "aprovado" (RN-71) | Rose |
| 14/10/2026 | Tela do cliente com a logo completa da Dobra 9 e texto de boas-vindas; "Nome para o cliente" opcional em cada serviço (Criação de sites → "Desenvolvimento do seu site") (RN-16c, RN-16d) | Rose |
| 13/10/2026 | Etapa "Cliente aprova" pode ser ocultada ou mostrada mesmo antes da primeira entrega; só etapas "Só a equipe vê" ficam fora desse menu. No formulário, "Só a equipe vê" é a primeira opção de "Como esta etapa funciona" | Claude, a confirmar com a Rose |
| 13/10/2026 | Rose autorizou o ciclo prático de aprovação: etapas internas; PDF, imagens e links por versão; Aprovar entrega/Pedir ajustes; observações em campo livre com rascunho; histórico e registro da versão aprovada. Mantida a proibição de commit/push até aprovação. | Rose |
| 13/10/2026 | Rose autorizou simplificar o fluxo: trabalho criado pelo nome para cada cliente, sem catálogo, datas opcionais, etapas com botão Salvar e área do cliente redesenhada. Sem commit ou push até aprovação. | Rose |
| 13/10/2026 | Novos trabalhos em preparação até Mostrar ao cliente; existentes preservados. Liberação apenas do acompanhamento, sem convite/e-mail. Aprovações e arquivos continuam nas fases seguintes. | Implementação do plano aprovado |
| 13/10/2026 | Fluxo simples com cards preenchíveis: serviço criado com um toque no modelo; etapas editadas direto no card, salvando sozinhas; um botão de próximo passo por etapa; o resto no menu ⋯ (RN-10, RN-12) | Rose |
| 13/10/2026 | Datas seguintes andam junto automaticamente; prazo final = data da última etapa (RN-13) | Rose |
| 13/10/2026 | Início do cliente mais leve: frase "o que fazer agora" e um card por serviço; contador de revisões só dentro do serviço (RN-25) | Rose |
| 13/10/2026 | Etapas reordenadas com botões subir/descer (funciona igual no celular), em vez de arrastar (RN-12) | Claude, a confirmar com a Rose |
| 13/10/2026 | Remover etapa só antes de ela ir para o cliente; depois, só ocultar (RN-12) | Claude, a confirmar com a Rose |
| 13/10/2026 | Segunda e terça de Carnaval não contam como dias úteis; prazo final em branco = data da última etapa (RN-13) | Claude, a confirmar com a Rose |
| 13/10/2026 | "Liberar pendência" para o cliente fica na fase 4, junto com o envio de arquivos | Claude, a confirmar com a Rose |
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
