# 00 · Visão geral

O Ponte é o portal da Dobra 9 onde o cliente acompanha o projeto, envia o que falta e aprova cada
entrega, com 3 revisões por frente. **A segurança dos dados vem antes de qualquer outra escolha.**

## Para quem

| Quem | Entra por | Faz o quê |
|---|---|---|
| Cliente (1 login por cliente) | `ponte.dobra9.com.br/entrar` | Acompanha, envia materiais, aprova ou pede ajuste, escolhe propostas, baixa entregas finais |
| Equipe: Rose e Elias | Caminho secreto (`/{PONTE_CAMINHO_EQUIPE}`), com 2FA | Cadastra clientes e serviços, organiza as fases, mostra cards de aprovação ao cliente, acompanha prazos, arquiva |

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
| Serviço | Uma parte contratada: Sistemas sob medida, Aplicativos, Criação de sites, Segurança, Servidores, Tráfego pago, ou outro criado pela equipe | `Frente` |
| Fluxo padrão | Lista padrão de fases de um serviço, carregada ao cadastrá-lo | `Modelo`, `ModeloEtapa` |
| Fase | Um passo dentro do serviço (ex.: Layout), com responsável e datas | `Etapa` |
| Card de aprovação | Uma rodada (1ª, 2ª, 3ª revisão) mostrada ao cliente: o que revisar, anexos e links | `Entrega` |
| Opção | Uma das propostas numa etapa de escolha | `Opcao` |
| Revisão | Um pacote de ajustes que o cliente envia de uma vez | `PedidoAjuste` |
| Aprovação | O registro imutável de que o cliente aprovou | `Aprovacao` |
| Atividade | Linha do registro de tudo o que acontece | `Atividade` |

> Nas telas, usamos **serviço** e **fase**. No código, serviço = frente e fase = etapa.
> Fluxo da equipe (14/10/2026) em [`14-fluxo-equipe.md`](14-fluxo-equipe.md).

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
| Fases | Carregadas do fluxo padrão do serviço e editáveis em cada cliente, sem alterar o padrão |
| Aprovação | Card por rodada em qualquer fase; prazo de 5 dias úteis; lembrete na véspera; alerta de vencido à equipe |

## Fora desta versão

Gestão de contratos (o contrato só fica anexado como referência), cobrança, aplicativo nas lojas,
chat em tempo real, vários usuários por cliente, comentários marcados em cima da imagem.

## Cliente piloto

Escola de Música Bela Bartók: começa com Rebranding + Site, com o site no ar até 18/12/2026.
Detalhes em [`../dados/piloto-bela-bartok.yaml`](../dados/piloto-bela-bartok.yaml).
