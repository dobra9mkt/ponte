# 00 · Visão geral

O Ponte é o portal da Dobra 9 onde o cliente acompanha o projeto, envia o que falta e aprova cada
entrega, com 3 revisões por frente. **A segurança dos dados vem antes de qualquer outra escolha.**

## Para quem

| Quem | Entra por | Faz o quê |
|---|---|---|
| Cliente (1 login por cliente) | `ponte.dobra9.com.br/entrar` | Acompanha, envia materiais, aprova ou pede ajuste, escolhe propostas, baixa entregas finais |
| Equipe: Rose e Elias | Caminho secreto (`/{PONTE_CAMINHO_EQUIPE}`), com 2FA | Cadastra clientes, adiciona serviços, publica entregas, responde ajustes, vê o calendário, arquiva |

Rose e Elias têm o mesmo acesso completo; não há níveis de permissão entre eles.

## Princípios

1. **Simples para o cliente.** Entende a tela sem explicação e aprova em dois cliques.
2. **Claro sobre responsabilidades.** O portal mostra quando o projeto espera pela Dobra 9 e quando espera pelo cliente.
3. **Seguro por padrão.** Cada cliente vê só o que é dele; nenhuma senha de cliente é guardada; menos dados guardados, menos dados para vazar.

## Vocabulário

| Termo | Significado | No código |
|---|---|---|
| Cliente | A empresa atendida (ex.: Escola de Música Bela Bartók) | `Cliente` |
| Projeto | O conjunto de frentes de um cliente (não é tabela própria) | — |
| Frente / Serviço | Uma parte contratada, com 3 revisões próprias: Rebranding, Site, Página de vendas, Sistema | `Frente` |
| Modelo | Lista padrão de etapas de um tipo de frente | `Modelo`, `ModeloEtapa` |
| Etapa | Um passo dentro da frente (ex.: Layout) | `Etapa` |
| Entrega | Uma versão enviada para o cliente ver (arquivo e/ou link) | `Entrega` |
| Opção | Uma das propostas numa etapa de escolha | `Opcao` |
| Revisão | Um pacote de ajustes que o cliente envia de uma vez | `PedidoAjuste` |
| Aprovação | O registro imutável de que o cliente aprovou | `Aprovacao` |
| Atividade | Linha do registro de tudo o que acontece | `Atividade` |

> Na tela da equipe, o botão diz **"Adicionar serviço"**; no código e nos documentos, serviço = frente.

## Decisões tomadas

Registro completo com datas em [`decisoes.md`](decisoes.md). Resumo:

| Assunto | Decisão |
|---|---|
| Endereço | ponte.dobra9.com.br (calendario.dobra9.com.br é de colaboradores e não é usado) |
| Tecnologia | Laravel na Hostinger que a Dobra 9 já usa |
| Escopo | Só o processo de trabalho. Cobrança e gestão de contratos ficam fora |
| Revisões | 3 por frente; depois, ajuste extra cobrado à parte |
| Rebranding | Briefing → direção visual → 2 propostas com recomendação → 3 revisões → manual e arquivos |
| Usuários | 1 login por cliente |
| Entrada do cliente | E-mail e senha, com "lembrar de mim" |
| Convite | Nunca automático: a equipe clica em "Enviar acesso" |
| Contrato | Pode ser anexado ao cadastro; visível só para a equipe |
| E-mails | Remetente contato@dobra9.com.br |
| Visual | Identidade da Dobra 9, sem símbolo próprio para o Ponte |
| Arquivamento | 30 dias após concluir, a equipe baixa o pacote e o portal apaga os dados |
| Etapas | Editáveis em cada projeto, sem alterar o modelo original |

## Fora desta versão

Gestão de contratos (o contrato só fica anexado como referência), cobrança, aplicativo nas lojas,
chat em tempo real, vários usuários por cliente, comentários marcados em cima da imagem.

## Cliente piloto

Escola de Música Bela Bartók: começa com Rebranding + Site, com o site no ar até 18/12/2026.
Detalhes em [`../dados/piloto-bela-bartok.yaml`](../dados/piloto-bela-bartok.yaml).
