# 11 · Testes

Pest, com banco SQLite em memória. Cada teste cita a regra (RN-xx) ou o item de segurança (SEG-xx)
que protege. Nenhuma fase é aprovada com teste falhando.

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

- [ ] SEG-10 / SEG-11: cliente A recebe 404 em todas as rotas de frente/etapa de B
- [ ] RN-11: editar etapas da frente não altera o modelo, e vice-versa
- [ ] RN-12: etapa com aprovação não pode ser removida
- [ ] RN-13: datas em dias úteis pulam fins de semana e feriados; empurrar datas move as seguintes
- [ ] Etapa oculta não aparece para o cliente e devolve 404

## Fase 3

- [ ] Transições: cada transição fora de [`02-fluxos.md`](02-fluxos.md) lança erro
- [ ] RN-20 / RN-25: contador conta só pedidos com `conta_como_revisao`
- [ ] RN-23: aprovar, aprovar direção visual e escolher proposta não gastam revisão
- [ ] RN-24: correção nossa devolve a revisão e grava quem/quando/por quê
- [ ] RN-27: sem revisões, `PedirAjuste` é recusado e só `SolicitarAjusteExtra` é aceito; a etapa só vai para ajustes após `IniciarAjusteExtra`
- [ ] RN-29: dois pedidos simultâneos com 1 revisão restante → só um gasta
- [ ] RN-21a: salvar rascunho não gasta revisão nem envia e-mail; enviar apaga o rascunho; cliente A não lê rascunho de B
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
