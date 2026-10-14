# 05 · Rotas e permissões

Duas áreas no mesmo Laravel, separadas por middleware. Nomes de rota em português.

## Área do cliente — `ponte.dobra9.com.br`

Middleware: `web`, `auth`, `papel:cliente`, `ativo`. Modelos ligados à rota **sempre** passam por policy
(e a consulta parte de `auth()->user()->cliente`).

| Método | Caminho | Nome | Tela | Policy |
|---|---|---|---|---|
| GET/POST | `/entrar` | `entrar` | C1 | visitante (throttle `login`) |
| POST | `/sair` | `sair` | — | autenticado |
| GET/POST | `/esqueci-a-senha` | `senha.pedir` | — | visitante (throttle) |
| GET/POST | `/nova-senha/{token}` | `senha.redefinir` | — | visitante |
| GET/POST | `/convite/{token}` | `convite` | C2 | visitante (throttle) |
| GET/POST | `/boas-vindas` | `boas-vindas` | C3 | cliente |
| GET | `/` | `inicio` | C4 | cliente |
| GET | `/servicos/{frente}` | `frentes.ver` | C5 | `FrentePolicy@view` |
| GET | `/etapas/{etapa}` | `etapas.ver` | C6 | `EtapaPolicy@view` |
| POST | `/etapas/{etapa}/aprovar` | `etapas.aprovar` | — | `EtapaPolicy@aprovar` |
| POST | `/etapas/{etapa}/escolher/{opcao}` | `etapas.escolher` | — | `EtapaPolicy@escolher` |
| GET/POST | `/etapas/{etapa}/ajuste` | `etapas.ajuste` | C7 | `EtapaPolicy@pedirAjuste` |
| PUT | `/etapas/{etapa}/ajuste/rascunho` | `etapas.ajuste.rascunho` | C7 (RN-21a) | `EtapaPolicy@pedirAjuste` |
| POST | `/etapas/{etapa}/ajuste-extra` | `etapas.ajuste-extra` | — | `EtapaPolicy@pedirAjusteExtra` |
| POST | `/etapas/{etapa}/enviar` | `etapas.enviar` | — | `EtapaPolicy@enviarMaterial` |
| POST | `/etapas/{etapa}/formulario` | `etapas.formulario` | — | `EtapaPolicy@responderFormulario` |
| GET | `/arquivos` | `arquivos` | C8 | cliente |
| GET | `/arquivos/{arquivo}/baixar` | `arquivos.baixar` | — | `ArquivoPolicy@download` |
| GET/POST | `/conta` | `conta` | C9 | cliente |
| GET | `/conta/contrato` | `conta.contrato` | C9 · baixa o próprio contrato (RN-07) | cliente |
| GET | `/privacidade` | `privacidade` | — | público |

## Área da equipe — `ponte.dobra9.com.br/{PONTE_CAMINHO_EQUIPE}`

`PONTE_CAMINHO_EQUIPE` vem do `.env` (ex.: 24 caracteres aleatórios). Middleware: `web`, `auth`,
`papel:equipe` (**responde 404** para qualquer outro), `2fa.obrigatorio`. Nome das rotas com prefixo `equipe.`.

| Caminho (após o prefixo) | Nome | Tela |
|---|---|---|
| `/entrar`, `/dois-fatores` | `equipe.entrar`, `equipe.2fa` | E1, E2 (throttle; login de cliente aqui = erro genérico) |
| `/` | `equipe.painel` | E3 |
| `/calendario` | `equipe.calendario` | E4 |
| `/clientes`, `/clientes/novo`, `/clientes/{cliente}` | `equipe.clientes.*` | E5, E6 |
| `POST /clientes/{cliente}/enviar-acesso` | `equipe.clientes.enviar-acesso` | RN-02 |
| `GET /clientes/{cliente}/contrato` | `equipe.clientes.contrato` | download do contrato (RN-07) |
| `/clientes/{cliente}/servicos/novo` | `equipe.frentes.criar` | E7 |
| `/servicos/{frente}` | `equipe.frentes.ver` | E8 |
| `/etapas/{etapa}` + ações (`iniciar`, `publicar`, `confirmar`, `complemento`, `concluir`) | `equipe.etapas.*` | E9 |
| `POST /pedidos/{pedido}/correcao-nossa` | `equipe.pedidos.correcao` | RN-24 |
| `POST /pedidos/{pedido}/iniciar-extra`, `/cancelar-extra` | `equipe.pedidos.*` | RN-27 |
| `POST /fases/{etapa}/aprovacoes`, `PUT/DELETE /aprovacoes/{entrega}`, `PUT /aprovacoes/{entrega}/prazo` | `equipe.aprovacoes.*` | E9 · cards (RN-70 a RN-76) |
| `/servicos`, `/servicos/{modelo}`, `/servicos-fases/{modeloEtapa}` | `equipe.servicos.*` | E10 · fluxos padrão (RN-10a) |
| `/consulta`, `/consulta/{cliente}` | `equipe.consulta.*` | E13 · aba Clientes, só GET (RN-81) |
| `/arquivamento`, `/arquivamento/{frente}/*` | `equipe.arquivamento.*` | E11 |
| `/conta` | `equipe.conta` | E12 |

## Policies

| Policy | Regra |
|---|---|
| Qualquer | Equipe pode tudo dentro da área da equipe. Cliente **nunca** acessa a área da equipe |
| `FrentePolicy@view` | `frente.cliente_id === user.cliente_id` e frente não arquivada |
| `EtapaPolicy@view` | dona via frente, e `oculta = false` |
| `EtapaPolicy@aprovar` / `escolher` | dona + tipo certo + estado `aguardando_cliente` |
| `EtapaPolicy@pedirAjuste` | dona + estado `aguardando_cliente` + tipo `aprovacao`/`escolha` + revisões disponíveis (ou etapa não gasta revisão) |
| `EtapaPolicy@pedirAjusteExtra` | dona + estado `aguardando_cliente` + revisões esgotadas |
| `EtapaPolicy@enviarMaterial` / `responderFormulario` | dona + tipo `pendencia_cliente` + estado `aguardando_cliente` |
| `ArquivoPolicy@download` | cliente: arquivo pertence a algo dele **e** é visível ao cliente (não é contrato, não é de etapa oculta); equipe: tudo |

Recurso de outro cliente → **404** (não 403), para não confirmar que o ID existe.

## Limites de requisição (throttle)

| Nome | Limite |
|---|---|
| `login` | 5/min por e-mail+IP; 20/min por IP |
| `convite`, `senha` | 5/min por IP |
| `uploads` | 30/min por usuário |
| geral autenticado | 120/min por usuário |
