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
- **RN-06** · Cadastro do cliente: empresa, pessoa de contato, e-mail, telefone, endereço e observações.
  Telefone, endereço e observações ficam criptografados no banco.
- **RN-07** · O **contrato** pode ser anexado ao cadastro (PDF). É visível **só para a equipe**,
  guardado criptografado, e nunca aparece em nenhuma tela ou rota do cliente.
- **RN-08** · O cliente desativado não entra mais; os dados continuam até o arquivamento.

## Serviços (frentes) e etapas

- **RN-10** · A equipe adiciona serviços ao cliente com **"Adicionar serviço"**: escolhe um modelo
  (ou "em branco"), o nome e o prazo final.
- **RN-11** · Ao criar a frente a partir de um modelo, as etapas são **copiadas** do modelo. Editar as
  etapas da frente **não** altera o modelo, e editar o modelo **não** altera frentes já criadas.
- **RN-12** · Em cada frente a equipe pode adicionar, remover, renomear, reordenar etapas e mudar datas.
  Não é possível remover uma etapa que já tenha aprovação ou pedido de ajuste (só ocultar do cliente).
- **RN-13** · Datas previstas são calculadas em **dias úteis** a partir da data de início, usando a
  duração de cada etapa do modelo. Mudar a data de uma etapa oferece (com confirmação) empurrar as
  seguintes da mesma frente.
- **RN-14** · Tipos de etapa: `pendencia_cliente`, `aprovacao`, `escolha`, `trabalho_equipe`,
  `entrega_final`, `recorrente`. Os estados permitidos por tipo estão em [`02-fluxos.md`](02-fluxos.md).
- **RN-15** · Uma frente pode depender de outra (ex.: o Layout do Site só começa após a entrega final do
  Rebranding). Na v1 isso é só informativo: um aviso na tela da equipe, sem bloqueio.

## Revisões

- **RN-20** · Cada frente tem **3 revisões incluídas** (valor padrão do modelo, editável por frente
  pela equipe). A contagem vale para a frente inteira, não por etapa.
- **RN-21** · Uma revisão = **um pacote** de ajustes enviado de uma vez (lista de itens + anexos opcionais).
  Cada item pode dizer **em que página ou parte** está.
- **RN-21a** · O pacote é montado aos poucos: a lista fica salva como **rascunho** (um por etapa) e pode
  ser completada em dias diferentes. Rascunho não gasta revisão nem avisa a equipe; só o envio
  confirmado conta. Ao aprovar ou receber nova versão, o rascunho da etapa é descartado.
- **RN-22** · Antes de enviar, o cliente vê e confirma: "Você vai usar a revisão N de 3 do <frente>",
  com o aviso de que a revisão vale para o **serviço inteiro** e quantas restam depois dela.
- **RN-23** · Só gasta revisão o pedido de ajuste em etapa com `gasta_revisao = true`. **Não gastam:**
  aprovar qualquer coisa, aprovar a direção visual, escolher uma proposta.
- **RN-24** · A equipe pode marcar um pedido como **"correção nossa"** (erro da Dobra 9: link quebrado,
  erro de digitação, falha técnica). A revisão volta para o cliente, com registro de quem marcou, quando
  e por quê. O cliente vê a mensagem de revisão devolvida.
- **RN-25** · O contador aparece **em destaque** em três lugares: topo da tela da frente, cartão da frente
  na tela Início, e confirmação de cada pedido de ajuste.
- **RN-26** · Na última revisão disponível, a confirmação avisa que é a última.
- **RN-27** · Com as revisões esgotadas, o botão "Pedir ajuste" vira **"Solicitar ajuste extra"**.
  O pedido extra não muda a etapa para ajustes: ele avisa a equipe para orçar. Só quando a equipe
  clica em "Iniciar ajuste extra" (depois do acerto, feito fora do portal) a etapa vai para ajustes.
- **RN-28** · Escolha de proposta: o cliente escolhe **uma** opção como base. Pedir elemento de outra
  proposta é feito numa revisão. O portal não oferece "misturar".
- **RN-29** · Dois pedidos simultâneos (duas abas) nunca gastam a mesma revisão: contagem em transação
  com trava na frente.

## Prazos e lembretes

- **RN-30** · O cliente tem um prazo em dias úteis para responder uma entrega ou pendência, contado a
  partir do envio. O prazo é **de cada etapa** (`prazo_resposta_dias_uteis`, padrão **3**), vem do
  modelo e pode ser mudado na frente. Ex.: 5 dias para "Mapa do site e textos" e "Site no link de teste".
- **RN-31** · Lembrete por e-mail ao cliente no **2º dia útil** sem resposta, e outro quando o prazo
  vence. No máximo esses dois por entrega.
- **RN-32** · O portal mostra quantos dias úteis a etapa está esperando pelo cliente, para ele e para a equipe.
- **RN-33** · Toda segunda às 8h a equipe recebe o resumo da semana (o que vence, o que espera o cliente).
- **RN-34** · Ao publicar uma entrega, liberar uma pendência ou mandar lembrete, a equipe tem o botão
  **"Avisar no WhatsApp"**: abre o WhatsApp dela com o número do cliente e a mensagem pronta com o
  link da etapa. Nada é enviado pelo portal; a equipe confere e envia.

## Aprovações e registro

- **RN-40** · Aprovar grava uma `Aprovacao` com: usuário, data e hora, IP, navegador, a entrega
  aprovada e um **hash** do conteúdo aprovado (arquivos + link + nota). Não pode ser editada nem apagada.
- **RN-41** · Toda ação relevante grava uma `Atividade` (entrada, envio de acesso, entrega publicada,
  aprovação, pedido de ajuste, correção nossa, download, arquivamento). Somente inserção.
- **RN-42** · Depois de aprovada, a etapa não volta. Uma mudança depois disso é uma nova etapa ou ajuste extra.
- **RN-43** · Uma etapa pode ter uma **confirmação antes de aprovar** (`confirmar_antes_de_aprovar`),
  um texto que o cliente precisa marcar para o botão Aprovar funcionar. Ex.: no "Site no link de
  teste": "Naveguei pelo site no celular e no computador."

## Arquivos

- **RN-50** · Tipos aceitos: PDF, PNG, JPG, WEBP, SVG (só da equipe), ZIP, DOCX, XLSX, PPTX, MP4.
  Limite de **50 MB** por arquivo. Para vídeos maiores, o cliente manda link.
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
- **RN-66** · Cópias de segurança antigas expiram em até 30 dias; depois disso nada do que foi
  arquivado resta no servidor.
