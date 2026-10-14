# Aprovação simples

> **Substituído em 14/10/2026** pelo fluxo da equipe em `14-fluxo-equipe.md`. Mantido como histórico.

Autorizada pela Rose em 13/10/2026, após a proposta do fluxo Ver → Aprovar / Pedir ajustes.
Implementação local, sem commit nem push.

## Equipe

Ao montar o trabalho, a etapa nasce como **Só a equipe**. **Cliente aprova** prepara uma entrega privada.
Abrir **Preparar entrega**, anexar até 10 arquivos (50 MB cada), incluir até 5 links HTTPS com rótulos,
e uma orientação curta. Ao **Enviar para aprovação**, a versão fica imutável, o trabalho é liberado,
a etapa fica visível e aguardando o cliente, e um aviso por e-mail aponta para a entrega exata.
Envio requer ao menos um arquivo ou link. PDFs/imagens podem ser abertos; demais arquivos baixados.
Sem scripts/HTML nos arquivos exibidos. Tudo no disco privado, com autorização e cabeçalhos seguros.

Etapas legadas visíveis continuam como estavam. Etapas internas não podem ser expostas pelo menu
de visibilidade. Trocar o propósito depois de uma entrega publicada é bloqueado.
Publicar a primeira versão inicia a etapa automaticamente; republicar exige ajustes em andamento.
Após aprovação, a etapa não reabre. Uma nova etapa atende necessidades posteriores.

## Cliente

O link abre a versão enviada, retomando esse destino após o login. A tela apresenta título, orientação,
arquivos/links e dois caminhos: **Aprovar entrega** (confirmação curta) ou **Pedir ajustes**.
Se a etapa exige confirmação específica (como testar no celular), ela é guardada na versão e exigida
no servidor. Aprovação registra usuário, data/hora, IP, navegador e SHA-256 do conteúdo da entrega.
Links registram somente a URL: para preservar o visual, a equipe pode anexar um PDF junto do link.

Observações: um campo livre, dica de indicar a página/parte e até 5 imagens opcionais (PNG/JPG/WEBP).
Texto salvo automaticamente como rascunho, com confirmação e alternativa Salvar rascunho.
Anexos são enviados com as observações; não fazem parte do rascunho automático.
Só o envio confirmado consome uma revisão, se aplicável. A tela avisa o número e saldo restantes.
Quando esgotadas, solicita ajuste extra para orçamento; a equipe inicia ou cancela explicitamente.
Correção nossa devolve a revisão uma única vez, com motivo e histórico.

## Integridade

Trava na frente, depois na etapa, para publicar, responder e salvar rascunhos. A versão-base e o saldo
confirmado são conferidos no servidor. Aba antiga não responde versão nova; repetição não cria outro
registro. Rascunhos usam revisão otimista para impedir que uma aba sobrescreva silenciosamente outra.
Versões publicadas, arquivos e aprovações não têm edição/exclusão no fluxo. O cliente vê o histórico
das entregas do seu trabalho; IP, navegador e hash ficam na equipe, fora do caminho principal do cliente.
E-mails só avisam e abrem o portal, sem arquivos ou conteúdo das observações.

## Verificação prevista

Publicar PDF/link → cliente abre → rascunho → ajuste → nova versão → aprovação.
Testar acesso cruzado, etapas internas, arquivos privados, uploads inválidos, links inseguros,
versão/saldo desatualizados, repetição, revisão devolvida e extra. Conferir no celular e computador.

## Verificação realizada

Em 13/10/2026 (Claude, continuando o trabalho do Codex): telas, endereços e testes concluídos.
162 testes passando (10 novos em `tests/Feature/EntregasTest.php`, incluindo cliente A × cliente B),
Pint e PHPStan sem erros, build feito, migração aplicada no banco local após cópia de segurança.
Ciclo testado no navegador com cliente fictício "Cliente Teste (Claude)": equipe envia PDF + link,
cliente abre no celular, rascunho salvo sozinho, pedido de ajuste com imagem, versão 2, aprovação
com confirmação. Sem erros de tela, sem rolagem lateral. Pendente: avaliação e aprovação da Rose.

## Ajuste de 13/10/2026: tudo no card, sem tela intermediária

A Rose não encontrou onde o cliente aprovava: o envio ficava numa tela separada ("Preparar entrega").
Agora, inspirado no Basecamp e no Filestage:

- **Equipe:** etapa "Cliente aprova" mostra no próprio card **Solicitar aprovação** (link, arquivos,
  observações) e **Enviar ao cliente**. O link escrito na descrição da etapa já vem preenchido.
  Não há "Começar etapa" nesse tipo. O card mostra "Aguardando o cliente", o texto dos ajustes
  solicitados (com **Enviar nova versão**) ou "Aprovado pelo cliente em …". O histórico fica em
  "Ver histórico".
- **Cliente:** a caixa **Aprovação pendente** aparece no início e na etapa, com o link/arquivos, as
  observações e dois botões: **Aprovar** e **Solicitar ajustes** (campo de texto e imagens opcionais).
  O login continua como está.
- **Tom:** textos sóbrios, sem exclamação nem frases informais.
