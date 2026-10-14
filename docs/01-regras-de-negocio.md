# 01 · Regras de negócio

Cada regra tem um código (RN-xx) para ser citada nos testes e nos commits. Regra mudou → muda aqui
primeiro e registra em [`decisoes.md`](decisoes.md).

## Cadastro e acesso

- **RN-01** · Cada cliente tem **um** login (um `User` com papel `cliente` ligado ao `Cliente`).
- **RN-02** · Cadastrar um cliente **não** envia nada. O convite só sai quando a equipe clica em
  **"Enviar acesso"**. O botão pode ser usado de novo para reenviar (o link anterior deixa de valer).
- **RN-03** · O convite é um link de uso único, válido por **72 horas**, que leva à tela de criar senha.
  Expirado → a equipe reenvia.
- **RN-04** · O cliente entra com e-mail e senha e pode marcar **"lembrar de mim"** (sessão de 30 dias
  naquele aparelho).
- **RN-05** · A equipe (Rose e Elias) entra pelo caminho secreto e **sempre** com 2FA por aplicativo
  autenticador. Sem 2FA configurado, a conta da equipe não acessa nada além da tela de configurar o 2FA.
- **RN-06** · Cadastro do cliente: empresa, pessoa de contato, e-mail, telefone, endereço, observações
  e o **e-mail de avisos de aprovação** (padrão: o e-mail do contato; pode ser outro). Telefone,
  endereço e observações ficam criptografados no banco.
- **RN-07** · O **contrato** pode ser anexado ao cadastro (PDF), guardado criptografado. A equipe baixa;
  o **cliente baixa só o próprio contrato** em Minha conta.
- **RN-08** · O cliente desativado não entra mais; os dados continuam até o arquivamento.

## Serviços (frentes) e fases (etapas)

Nas telas: **serviço** e **fase**. No código: `Frente` e `Etapa`. Fluxo completo da equipe em
[`14-fluxo-equipe.md`](14-fluxo-equipe.md) (14/10/2026).

- **RN-10** · Na ficha do cliente, **Cadastrar serviço**: a equipe escolhe o serviço numa lista
  e, se quiser, a data de início. As fases do **fluxo padrão** daquele serviço (`Modelo`) já vêm
  carregadas. **Outro** pede só o nome e começa sem fases. Serviços e fluxos padrão iniciais em
  [`14-fluxo-equipe.md`](14-fluxo-equipe.md): Sistemas sob medida, Aplicativos Android e iPhone,
  Criação de sites, Segurança e remoção de vírus, Servidores web Linux, Tráfego pago.
- **RN-10a** · **Tudo é editável:** na tela **Serviços** a equipe cria serviços novos, renomeia,
  desativa (exclusão suave) e edita as fases do fluxo padrão (nome, responsável, o que fazer, duração
  em dias úteis, ordem).
- **RN-11** · As fases carregadas são uma cópia: editar, adicionar, remover ou reordenar fases de um
  serviço não altera o fluxo padrão nem outros clientes, e mudar o fluxo padrão não altera serviços
  já cadastrados. Os serviços existentes são preservados.
- **RN-12** · Cada fase tem: nome, **responsável** (texto livre, pois freelancers sem acesso ao
  portal também respondem por fases), **o que fazer**,
  **data de entrada** e **previsão de término**. Edição direto no card, aplicada só ao clicar em
  **Salvar**. Qualquer fase pode ser excluída (vai para a lixeira, RN-82).
- **RN-13** · **Datas separadas:** o serviço tem **início e término próprios**, digitados no cadastro ou
  em "Editar serviço"; cada fase tem **entrada e término próprios**. Mudar a data do serviço não muda as
  fases, e mudar a data de uma fase não muda o serviço. Com data de início, as fases que têm duração no
  fluxo padrão já vêm com datas em dias úteis; sem isso, ficam **A combinar**. Mudar o término de uma
  fase move as fases seguintes os mesmos dias úteis. Dias úteis: segunda a sexta, menos os feriados de
  `config/feriados.php` (nacionais, de Belo Horizonte e a segunda e a terça de Carnaval).
- **RN-14** · Situações da fase: **A fazer**, **Em andamento**, **Aguardando cliente**, **Ajustes**,
  **Concluída**. "Aguardando cliente" e "Ajustes" só são alcançadas pelos cards de aprovação
  (RN-70 a RN-76); as demais a equipe muda pelos botões do card. Transições em [`02-fluxos.md`](02-fluxos.md).
  Os tipos de etapa antigos (`pendencia_cliente` etc.) saem das telas da equipe; pendências serão
  revistas na fase 4. A escolha de propostas saiu (14/10/2026).
- **RN-16a** · No serviço mostrado, o cliente vê **só as fases em andamento ou finalizadas**. Fases
  "A fazer" ficam escondidas e aparecem quando a equipe clica em **Iniciar** (ou mostra uma aprovação nela).
- **RN-16c** · Cada serviço tem um **Nome para o cliente** opcional (no fluxo padrão e no serviço do cliente).
  O cliente vê esse nome; a equipe continua vendo o nome do serviço. Ex.: Criação de sites → "Desenvolvimento
  do seu site". Vazio, o cliente vê o nome do serviço.
- **RN-16d** · Telas do cliente com a logo completa da Dobra 9 no topo (sem "ponte.") e, no Início e na linha
  do tempo, a frase de boas-vindas discreta acima do cartão do serviço (doc 07).
- **RN-16b** · O cliente entra **direto na linha do tempo**; não há tela de lista de serviços. Com dois ou mais
  serviços, um seletor no topo do cartão do serviço troca de serviço (o aberto em destaque branco); a aba com aprovação pendente tem uma bolinha dourada.
  Ao entrar, abre o serviço com aprovação pendente; sem pendência, o mais recente. Sem serviço mostrado, o
  Início mostra só as boas-vindas e o aviso de preparação.
- **RN-16** · Serviço novo nasce visível só para a equipe. No card "Editar serviço", ao lado de Excluir,
  **Mostrar ao cliente** libera o acompanhamento no portal (sem enviar e-mail) e **Ocultar do cliente**
  esconde de novo. Mostrar um card de aprovação também libera o serviço.
- **RN-17** · O cliente vê o serviço como uma **linha do tempo vertical**: "Começamos em DD/MM/AAAA",
  depois cada fase com situação, início e término previsto e o que será feito. O **responsável não
  aparece** para o cliente. Os cards de aprovação mostrados entram na linha, dentro da fase, como
  **"Revisão 1 de 3"**, "Revisão 2 de 3" (o total são as revisões incluídas no serviço). Na última, o
  cliente vê o aviso **"Esta é a última revisão antes da entrega."** Além do total, aparece só
  "Revisão 4". Cada rodada mostra a resposta (aprovado, ajustes solicitados ou aguardando). Em fase
  concluída, as rodadas ficam fechadas atrás de "Ver histórico (N)"; fases em andamento ficam sempre abertas. Fases
  ocultas e rascunhos não aparecem. A linha vai sendo formada à medida que a equipe inicia e conclui fases (RN-16a).
- **RN-15** · Um serviço pode depender de outro (ex.: o Layout do Site só começa após a entrega final do
  Rebranding). Na v1 isso é só informativo: um aviso na tela da equipe, sem bloqueio.

## Cards de aprovação

- **RN-70** · Em qualquer fase, só quando precisar, **Adicionar aprovação** cria um card em
  **rascunho**: o que revisar (obrigatório), anexos de qualquer tipo (RN-50) e links (só https).
  O rascunho é visível só para a equipe e pode ser editado ou excluído (exclusão suave).
- **RN-71** · **Um card por rodada**: 1ª, 2ª, 3ª revisão, numeradas dentro da fase. Os cards
  anteriores ficam como histórico, só leitura. Uma fase tem no máximo um card aberto (em rascunho ou
  aguardando o cliente) por vez. **No máximo 3 revisões por fase; não existe 4ª.** A 3ª segue o
  mesmo fluxo (aprovar ou pedir ajustes). Se o cliente pedir ajustes na 3ª, a equipe responde com
  **Enviar versão final**: um card "Versão final", que não conta como revisão, em que o cliente só pode
  **aprovar**. No card da versão final, o campo é **Mensagem para o cliente**, escrito pela equipe; na
  página do card ele aparece sob o título "O que você precisa fazer agora". Na linha do tempo, o cliente
  vê uma mensagem comemorativa fixa (igual para todos os serviços e fases) e o botão **Ver**, sem aviso de
  revisões. O card da versão final tem também o campo **Botão de aprovação**
  (editável, vem com "Aprovar versão final"); na página do card, o cliente vê no topo "Autorização pendente". Na versão final o termo é
  sempre **autorizar / autorizado** (botão, etiqueta, datas, mensagens e e-mail), nunca "aprovado". Depois da 3ª aprovada ou da versão
  final, a fase não recebe novos cards.
- **RN-72** · **Mostrar para cliente** publica o card: o conteúdo fica bloqueado para edição (com
  hash do que foi mostrado), a fase vai para **Aguardando cliente** e o e-mail de avisos do cliente
  recebe o aviso com o prazo. Card publicado não se apaga.
- **RN-73** · Prazo de resposta: **5 dias úteis** a partir da publicação (o dia da publicação não conta),
  pulando fins de semana e feriados. O rascunho mostra a data prevista; depois de publicar, a equipe pode
  mudar a data à mão; a mudança grava uma `Atividade` e reagenda o lembrete.
- **RN-74** · **1 dia útil antes do vencimento**, lembrete automático por e-mail ao cliente, uma vez
  por card.
- **RN-75** · **Prazo vencido** sem resposta: alerta para a equipe no Painel e por e-mail, uma vez por card.
- **RN-76** · Retorno do cliente (telas na fase do login do cliente): **aprovou** → card "Aprovado em …"
  (RN-40) e a fase volta para **Em andamento** e segue; **pediu ajuste** → card "Ajustes solicitados"
  com o texto do cliente e a fase vai para **Ajustes**; a equipe faz e clica em **Nova rodada**, que cria
  o card da próxima revisão em rascunho com os links da anterior.

## Área da equipe

- **RN-80** · Barra lateral: **Painel**, **Cadastrar cliente** (onde tudo é editado), **Clientes**
  (só consulta), **Serviços** (fluxos padrão, RN-10a) e **Lixeira** (RN-82).
- **RN-81** · A aba **Clientes** é **somente visualização**: clientes, serviços, fases, situação e o
  histórico de aprovações. Não tem nenhum botão de edição.
- **RN-82** · **Excluir** cliente, serviço ou fase pede confirmação e manda o item para a **Lixeira**
  (exclusão suave: nada é apagado do banco). Cliente excluído não entra no portal; seus serviços somem
  junto e voltam junto. A Lixeira (item da barra lateral) lista o que foi excluído, com a data, e o botão
  **Restaurar**, que devolve o item ao lugar de antes. Serviço de cliente excluído e fase de serviço
  excluído não aparecem sozinhos na Lixeira: voltam ao restaurar o item de cima.
- **RN-82a** · Na Lixeira, **Excluir definitivamente** (por item) e **Esvaziar lixeira** (tudo), ambos com
  confirmação, tiram o item da Lixeira para sempre: não aparece mais em lugar nenhum e não pode ser
  restaurado. Os dados continuam guardados no banco (marcados como descartados), preservando as provas de
  aprovação; saem de vez só no arquivamento da fase 5.
- **RN-83** · Itens na lixeira não geram lembretes, alertas nem aparecem no Painel ou na aba Clientes.

## Revisões

> RN-20, RN-22 a RN-29 foram **substituídas em 14/10/2026** pelo modelo de rodadas: até 3 revisões por
> fase e a versão final (RN-71, RN-76). Saíram o contador de revisões do serviço inteiro, a "correção
> nossa", o "ajuste extra" cobrado pelo portal e a escolha de propostas. Continuam valendo:

- **RN-21** · Um pedido de ajustes = **um pacote** de observações enviado de uma vez: campo de texto
  livre e imagens opcionais.
- **RN-21a** · O texto fica salvo como **rascunho** (um por card e usuário) e pode ser completado em dias
  diferentes. Rascunho não avisa a equipe; só o envio confirmado conta. Ao aprovar ou receber nova
  rodada, o rascunho é descartado.

## Prazos e lembretes

- **RN-30** · Prazo de resposta das aprovações: **5 dias úteis** a partir da publicação do card,
  editável à mão (RN-73). Substitui o prazo de 3 dias por etapa.
- **RN-31** · Lembrete ao cliente 1 dia útil antes do vencimento (RN-74) e alerta à equipe quando
  vence (RN-75). Substitui o lembrete do 2º dia útil e o e-mail de vencido ao cliente.
- **RN-32** · O portal mostra quantos dias úteis a etapa está esperando pelo cliente, para ele e para a equipe.
- **RN-33** · Toda segunda às 8h a equipe recebe o resumo da semana (o que vence, o que espera o cliente).
- **RN-34** · Ao publicar uma entrega, liberar uma pendência ou mandar lembrete, a equipe tem o botão
  **"Avisar no WhatsApp"**: abre o WhatsApp dela com o número do cliente e a mensagem pronta com o
  link da etapa. Nada é enviado pelo portal; a equipe confere e envia.

## Aprovações e registro

- **RN-40** · Aprovar grava uma `Aprovacao` com: usuário, data e hora, IP, navegador, a entrega
  aprovada e um **hash** do conteúdo aprovado (arquivos + link + nota). Não pode ser editada nem apagada.
  A entrega preserva título, instrução, confirmação exigida, links e arquivos daquela versão.
  O hash de um link registra seu endereço, não congela o conteúdo de sites externos.
- **RN-41** · Toda ação relevante grava uma `Atividade` (entrada, envio de acesso, entrega publicada,
  aprovação, pedido de ajuste, download, arquivamento). Somente inserção.
- **RN-42** · Depois de aprovada, a etapa não volta. Uma mudança depois disso é uma nova etapa ou outro serviço.
- **RN-43** · Uma etapa pode ter uma **confirmação antes de aprovar** (`confirmar_antes_de_aprovar`),
  um texto que o cliente precisa marcar para o botão Aprovar funcionar. Ex.: no "Site no link de
  teste": "Naveguei pelo site no celular e no computador."

## Arquivos

- **RN-50** · Anexos da equipe nos cards: **qualquer tipo** de arquivo. Do cliente: PDF, PNG, JPG,
  WEBP, ZIP, DOCX, XLSX, PPTX, MP4. Limite de **50 MB** por arquivo. Só PDF e imagens PNG/JPG/WEBP
  podem ser abertos no navegador; o resto é sempre baixado (SEG-24).
- **RN-51** · Arquivos ficam fora da pasta pública e só são baixados por quem tem acesso, pela policy.
- **RN-52** · A tela "Arquivos" do cliente mostra só entregas aprovadas e entregas finais.

## Conclusão e arquivamento

- **RN-60** · A frente é concluída quando todas as etapas estão aprovadas ou concluídas (a equipe
  confirma). Etapas recorrentes não impedem a conclusão; elas encerram junto.
- **RN-61** · A frente concluída fica **30 dias** visível para o cliente. No 30º dia o portal avisa a
  equipe (no painel e por e-mail) que ela pode ser arquivada.
- **RN-62** · "Arquivar e remover" (só equipe): gera um pacote ZIP com todos os arquivos da frente e um
  **PDF do histórico** (etapas, entregas, aprovações com hash, revisões, atividades). A equipe baixa.
- **RN-63** · Só depois de a equipe **confirmar que guardou o pacote** (digitando o nome do cliente) o
  portal apaga do servidor: arquivos, entregas, pedidos, aprovações e atividades daquela frente.
  Fica só uma linha mínima: "frente X arquivada em DD/MM/AAAA por Y".
- **RN-64** · O pacote é exclusivo da equipe. O cliente não baixa o pacote de arquivamento.
- **RN-65** · Arquivadas todas as frentes, a equipe pode **arquivar o cliente**: o login é apagado e o
  cadastro (incluindo o contrato) vai para o mesmo processo de pacote + remoção.
- **RN-65a** · Quem abrir o link de um serviço ou cliente já arquivado vê uma página amigável: o projeto foi
  concluído e arquivado; para novos serviços, os canais oficiais e o link de dobra9.com.br.
- **RN-66** · Cópias de segurança antigas expiram em até 30 dias; depois disso nada do que foi
  arquivado resta no servidor.
