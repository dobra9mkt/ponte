# 10 · Identidade visual

O Ponte usa a identidade da Dobra 9 como está, sem símbolo próprio. Os valores abaixo são os mesmos
do site da Dobra 9. Cópias dentro desta pasta, em [`../recursos/marca/`](../recursos/marca/): logos (SVG),
brand book (HTML), paleta (PNG) e o CSS do site como referência (`estilo-site-referencia.css`).

## Cores

| Token | Hex | Uso no Ponte |
|---|---|---|
| `navy` | `#071e3d` | Topo, títulos, fundo da tela de login |
| `navy-2` | `#0b2a52` | Topo da área da equipe |
| `tec` | `#0f4c81` | Links, ícones |
| `azul` | `#2563eb` | Botão principal (Aprovar, Entrar, Publicar) |
| `azul-hover` | `#1d4ed8` | Botão principal ao passar o mouse |
| `azul-claro` | `#e6f0ff` | Etapa atual na linha do tempo, destaque do contador |
| `azul-vivo` | `#60a5fa` | Detalhes sobre fundo escuro |
| `off` | `#f1f5f9` | Fundo das páginas |
| `texto` | `#26384d` | Texto corrido |
| `mudo` | `#4f5f74` | Texto secundário, datas |
| `linha` | `#d5e1ee` | Bordas, divisórias, etapas futuras |
| `teal` | `#0e9488` | **Aprovado / concluído** |
| `teal-vivo` | `#3cc6b7` | Aprovado sobre fundo escuro |
| `ouro` | `#b68a16` | **Precisa de você / aguardando cliente** |
| `ouro-claro` | `#e2b53f` | Faixa "Precisa de você" |
| `branco` | `#ffffff` | Cartões |

Cores de estado (sempre com texto ou ícone junto, nunca só a cor):

| Estado | Cor | Ícone |
|---|---|---|
| Precisa de você / aguardando cliente | `ouro` | ● |
| Em produção / ajustes em andamento | `azul` | ◐ |
| Aprovada / concluída | `teal` | ✓ |
| A fazer | `linha` | ○ |
| Ajuste extra / prazo vencido | `#b42318` (vermelho de alerta, só para isto) | ! |

## Tipografia

| Uso | Fonte | Peso |
|---|---|---|
| Títulos, nome das frentes, botões | Montserrat Variable | 600–700 |
| Texto, formulários, tabelas | Inter Variable | 400–500 |

Fontes servidas pelo próprio portal (pacotes `@fontsource-variable/montserrat` e
`@fontsource-variable/inter`, como no site), por causa da CSP `font-src 'self'`. Texto base 17 px
(1.0625rem), altura de linha 1.65, como no site.

## Forma

- Raio: 10 px (campos, botões), 16 px (cartões)
- Sombra dos cartões: `0 1px 2px rgb(7 30 61 / 0.06), 0 8px 24px rgb(7 30 61 / 0.06)`
- Margem lateral no celular: 16 px; largura máxima do conteúdo do cliente: 720 px; da equipe: 1280 px
- Área de toque mínima: 44 × 44 px

## Tailwind

No CSS de entrada (`resources/css/app.css`, Tailwind v4):

```css
@import "tailwindcss";
@import "@fontsource-variable/montserrat";
@import "@fontsource-variable/inter";

@theme {
  --color-navy: #071e3d;
  --color-navy-2: #0b2a52;
  --color-tec: #0f4c81;
  --color-azul: #2563eb;
  --color-azul-hover: #1d4ed8;
  --color-azul-claro: #e6f0ff;
  --color-azul-vivo: #60a5fa;
  --color-off: #f1f5f9;
  --color-texto: #26384d;
  --color-mudo: #4f5f74;
  --color-linha: #d5e1ee;
  --color-teal: #0e9488;
  --color-teal-vivo: #3cc6b7;
  --color-ouro: #b68a16;
  --color-ouro-claro: #e2b53f;
  --color-alerta: #b42318;
  --font-titulo: "Montserrat Variable", "Montserrat", system-ui, sans-serif;
  --font-texto: "Inter Variable", "Inter", system-ui, sans-serif;
  --radius-campo: 10px;
  --radius-cartao: 16px;
}
```

## Componentes Blade (fase 1)

| Componente | Uso |
|---|---|
| `<x-botao>` | variantes `principal` (azul), `secundario` (contorno), `perigo` (alerta) |
| `<x-cartao>` | caixa branca com raio 16 e sombra |
| `<x-campo>` | rótulo + campo + erro, acessível |
| `<x-aviso>` | mensagens de sucesso/erro/informação |
| `<x-contador-revisoes :usadas :total>` | bolinhas ● ○ + "N de 3 revisões usadas" (RN-25) |
| `<x-estado-etapa :estado>` | etiqueta com cor e ícone da tabela de estados |
| `<x-linha-do-tempo>` | lista vertical de etapas (C5) |

## Acessibilidade

Contraste mínimo AA (os pares acima foram escolhidos para isso no site), foco visível em tudo,
formulários com rótulo, mensagens de erro ligadas ao campo, respeito a `prefers-reduced-motion`.
