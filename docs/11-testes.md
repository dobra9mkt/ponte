# 11 · Testes

Pest, com banco SQLite em memória. Cada teste cita a regra (RN-xx) ou o item de segurança (SEG-xx)
que protege. Nenhuma fase é aprovada com teste falhando.

## Fase 2 refeita · 14/10/2026

Testes previstos em [`14-fluxo-equipe.md`](14-fluxo-equipe.md) ("Verificação prevista"), cada um
citando RN-06, RN-10 a RN-14, RN-70 a RN-76, RN-80 e RN-81. Prazos e lembretes com data simulada
(`travel`) e `Mail::fake()`. Arquivos: `ServicosPadraoTest`, `FasesTest`, `AprovacoesTest`, `PrazosTest`,
`ConsultaTest`. Os testes do fluxo simples foram removidos. Suíte em 14/10/2026: 159 testes, 617 verificações.

## Simplificação da fase 2 · 13/10/2026

`FluxoSimplesTest` verifica criação sem catálogo, rascunhos privados (incluindo URLs diretas),
liberação com etapa visível e sem e-mail, datas opcionais, recorrência sem data e preservação dos
valores no formulário correto após erro. `FrentesTest` foi ajustado às novas RN-10 e RN-13.
`AreaEquipeTest` inclui a rota de liberação. Suíte: 152 testes / 554 verificações.
Navegador com dados fictícios: criar, editar, liberar, iniciar e concluir; cliente em 390px,
equipe em 360px e computador. Sem erros de JavaScript/CSP ou rolagem horizontal.

## Ajudantes (fase 1)

- Fábricas: `User::factory()->equipe()`, `User::factory()->cliente($cliente)`, `Cliente`, `Frente`
  (com `->apartirDoModelo('rebranding')`), `Etapa` (com estados), `Entrega`, `PedidoAjuste`.
- `actingAsEquipe()` (já com 2FA confirmado) e `actingAsCliente($cliente)`.
- `rotaEquipe('equipe.painel')` que monta o caminho secreto.
- Conjunto de dados `rotasDoCliente` para o teste de acesso cruzado rodar em todas de uma vez.

## Fase 1

- [ ] SEG-01: cliente e visitante recebem 404 em qualquer rota da equipe
- [ ] SEG-02: equipe sem 2FA confirmado só acessa a configuração do 2FA
- [ ] SEG-05: 6ª tentativa de login em 1 minuto é bloqueada
- [ ] SEG-06: mesma resposta para e-mail existente e inexistente (login, senha, convite)
- [ ] SEG-07: ID da sessão muda após login
- [ ] SEG-08 / RN-03: convite só funciona uma vez, vence em 72 h, reenvio invalida o anterior, banco guarda só o hash
- [ ] RN-02: cadastrar cliente não envia e-mail (`Mail::fake()`); "Enviar acesso" envia
- [ ] SEG-51: telefone, endereço e observações não aparecem em texto puro no banco
- [ ] RN-07 / SEG-13 / SEG-26: contrato cifrado no disco; nenhuma rota do cliente devolve o contrato
- [ ] SEG-14: `Atividade` não pode ser alterada nem apagada
- [ ] SEG-31: toda resposta tem os cabeçalhos de segurança
- [ ] SEG-12: enviar `papel=equipe` no formulário de conta não muda o papel

## Fase 2

- [x] SEG-10 / SEG-11: cliente A recebe 404 em todas as rotas de frente/etapa de B
- [x] RN-11: editar etapas da frente não altera o modelo, e vice-versa
- [x] RN-12: etapa com aprovação não pode ser removida
- [x] RN-13: datas em dias úteis pulam fins de semana e feriados; empurrar datas move as seguintes
- [x] Etapa oculta não aparece para o cliente e devolve 404

## Fase 3

- [ ] Transições: cada transição fora de [`02-fluxos.md`](02-fluxos.md) lança erro
- [x] RN-71: até 3 revisões por fase; ajustes na 3ª viram a versão final, que só pode ser autorizada
- [ ] RN-21a: salvar rascunho não envia e-mail; enviar apaga o rascunho; cliente A não lê rascunho de B
- [ ] RN-43: aprovar sem marcar a confirmação é recusado
- [ ] RN-34: o link do WhatsApp só aparece na área da equipe e leva o número e a mensagem certos
- [ ] RN-40 / SEG-14: aprovação grava hash, IP e navegador e não pode ser alterada
- [ ] RN-42: etapa aprovada não aceita novo pedido de ajuste
- [ ] SEG-22 / SEG-25: `.php` disfarçado de `.jpg` e SVG do cliente são recusados
- [ ] SEG-24: download tem `attachment`, `nosniff` e `sandbox`
- [ ] SEG-11: cliente A não baixa arquivo de B

## Fase 4

- [ ] RN-50: arquivo de 60 MB recusado; tipos fora da lista recusados
- [ ] RN-52: "Arquivos" mostra só aprovados e finais
- [ ] Briefing: rascunho salva; depois de enviado não muda

## Fase 5

- [ ] RN-31: lembrete no 2º dia útil e no vencimento, nunca mais de dois (`travel` no tempo)
- [ ] RN-30: etapa com prazo de 5 dias úteis só vence no 5º dia útil
- [ ] RN-61: aviso de arquivamento 30 dias após concluir
- [ ] RN-62 / RN-63: pacote contém arquivos e PDF; remoção só depois da confirmação com o nome; depois dela não resta arquivo nem linha da frente além de `arquivamentos`
- [ ] RN-64: cliente não acessa o pacote
