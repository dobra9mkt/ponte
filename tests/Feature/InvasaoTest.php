<?php

use App\Enums\SituacaoCard;
use App\Models\Aprovacao;
use App\Models\Cliente;
use App\Models\PedidoAjuste;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use PragmaRX\Google2FA\Google2FA;

const XSS_SCRIPT = '<script>alert(1)</script>';
const XSS_IMG = '"><img src=x onerror=alert(1)>';

beforeEach(function () {
    Storage::fake('privado');
    Mail::fake();
    $this->travelTo('2026-11-13 10:00');
    $this->clienteA = Cliente::factory()->comLogin()->create();
    $this->clienteB = Cliente::factory()->comLogin()->create();
    $this->frenteA = servicoDoCliente($this->clienteA);
    $this->faseA = $this->frenteA->etapas()->where('titulo', 'Aprovação do cliente')->sole();
});

function pngDeVerdade(string $nome = 'foto.png'): UploadedFile
{
    return UploadedFile::fake()->image($nome, 10, 10);
}

/**
 * O UploadedFile::fake() informa o tipo pela extensão; este arquivo passa pela detecção real
 * do conteúdo (finfo), como acontece num envio de verdade.
 */
function arquivoComConteudo(string $nome, string $conteudo): UploadedFile
{
    $caminho = tempnam(sys_get_temp_dir(), 'invasao');
    file_put_contents($caminho, $conteudo);

    return new UploadedFile($caminho, $nome, null, null, true);
}

it('invasão: cliente B não abre, responde nem baixa nada do cliente A trocando o ID na URL', function () {
    $card = cardMostrado($this->faseA, ['arquivos' => [UploadedFile::fake()->create('layout.pdf', 10, 'application/pdf')]]);
    actingAsCliente($this->clienteA);
    $this->post(route('entregas.ajustes', $card), ['texto' => 'Trocar a cor', 'imagens' => [pngDeVerdade()]])->assertSessionHasNoErrors();
    $imagemDoPedido = PedidoAjuste::query()->sole()->arquivos()->sole();

    actingAsCliente($this->clienteB);
    $this->get(route('frentes.ver', $this->frenteA))->assertNotFound();
    $this->get(route('entregas.ver', $card))->assertNotFound();
    $this->get(route('arquivos.ver', $card->arquivos->first()))->assertNotFound();
    $this->get(route('arquivos.ver', $imagemDoPedido))->assertNotFound();
    $this->post(route('entregas.aprovar', $card), ['confirmar' => '1'])->assertNotFound();
    $this->post(route('entregas.ajustes', $card), ['texto' => 'invasor'])->assertNotFound();
    $this->putJson(route('entregas.rascunho', $card), ['texto' => 'invasor', 'revisao' => 0])->assertNotFound();

    expect(PedidoAjuste::query()->count())->toBe(1)->and(Aprovacao::query()->count())->toBe(0);
});

it('invasão: card de fase oculta depois de mostrado some para o cliente e não pode ser respondido', function () {
    $card = cardMostrado($this->faseA);
    $this->faseA->forceFill(['oculta' => true])->save();
    actingAsCliente($this->clienteA);

    $this->get(route('entregas.ver', $card))->assertNotFound();
    $this->post(route('entregas.aprovar', $card), ['confirmar' => '1'])->assertNotFound();
    expect($card->fresh()->situacao)->toBe(SituacaoCard::AguardandoCliente);
});

it('invasão: card de serviço na lixeira devolve 404, não erro do servidor', function () {
    $card = cardMostrado($this->faseA, ['arquivos' => [UploadedFile::fake()->create('layout.pdf', 10, 'application/pdf')]]);
    $this->delete(rotaEquipe('frentes.destroy', [$this->clienteA, $this->frenteA]))->assertRedirect();
    actingAsCliente($this->clienteA);

    $this->get(route('entregas.ver', $card))->assertNotFound();
    $this->get(route('arquivos.ver', $card->arquivos->first()))->assertNotFound();
    $this->post(route('entregas.aprovar', $card), ['confirmar' => '1'])->assertNotFound();
});

it('invasão: card de fase na lixeira devolve 404, não erro do servidor', function () {
    $card = cardMostrado($this->faseA);
    $this->delete(rotaEquipe('etapas.destroy', $this->faseA))->assertRedirect();
    actingAsCliente($this->clienteA);

    $this->get(route('entregas.ver', $card))->assertNotFound();
    $this->post(route('entregas.aprovar', $card), ['confirmar' => '1'])->assertNotFound();
});

it('invasão: código malicioso nos textos aparece como texto para o cliente e para a equipe', function () {
    $this->clienteA->forceFill(['empresa' => XSS_SCRIPT])->save();
    $this->frenteA->forceFill(['nome' => XSS_IMG, 'nome_cliente' => XSS_IMG])->save();
    $this->faseA->forceFill(['titulo' => XSS_SCRIPT])->save();
    $card = cardMostrado($this->faseA, [
        'nota' => XSS_SCRIPT.XSS_IMG,
        'texto_aprovar' => XSS_IMG,
        'links' => [['rotulo' => XSS_IMG, 'url' => 'https://exemplo.com.br/?q=%22%3E%3Cscript%3E']],
        'arquivos' => [UploadedFile::fake()->create('<img src=x onerror=alert(1)>.pdf', 10, 'application/pdf')],
    ]);
    actingAsCliente($this->clienteA);
    $this->post(route('entregas.ajustes', $card), ['texto' => XSS_SCRIPT.XSS_IMG])->assertSessionHasNoErrors();

    $this->get(route('frentes.ver', $this->frenteA))->assertOk()->assertDontSee('<script>alert', false)->assertDontSee('<img src=x', false);
    $this->get(route('entregas.ver', $card))->assertOk()->assertDontSee('<script>alert', false)->assertDontSee('<img src=x', false);

    actingAsEquipe();
    foreach ([
        rotaEquipe('frentes.ver', [$this->clienteA, $this->frenteA]),
        rotaEquipe('clientes.show', $this->clienteA),
        rotaEquipe('clientes.index'),
        rotaEquipe('consulta.ver', $this->clienteA),
        rotaEquipe('painel'),
    ] as $tela) {
        $this->get($tela)->assertOk()->assertDontSee('<script>alert', false)->assertDontSee('<img src=x', false);
    }
});

it('invasão: links com javascript:, data: ou http: são recusados', function (string $url) {
    $card = cardEmRascunho($this->faseA);

    $this->put(rotaEquipe('aprovacoes.update', $card), ['nota' => 'x', 'links' => [['rotulo' => 'A', 'url' => $url]]])
        ->assertSessionHasErrors('links.0.url');
})->with(['javascript:alert(1)', 'JaVaScRiPt:alert(1)//https://x', 'data:text/html,<script>alert(1)</script>', 'http://inseguro.com', '//evil.com']);

it('invasão: cliente não envia PHP disfarçado, SVG com script, HTML nem arquivo gigante', function (UploadedFile $arquivo) {
    $card = cardMostrado($this->faseA);
    actingAsCliente($this->clienteA);

    $this->post(route('entregas.ajustes', $card), ['texto' => 'Ajuste', 'imagens' => [$arquivo]])
        ->assertSessionHasErrors();

    expect(PedidoAjuste::query()->count())->toBe(0)
        ->and(Storage::disk('privado')->allFiles('entregas'))->toBeEmpty();
})->with([
    'php como jpg' => fn () => arquivoComConteudo('foto.jpg', '<?php system($_GET["c"]); ?>'),
    'php com cabeçalho de gif' => fn () => arquivoComConteudo('foto.gif', "GIF89a<?php system(\$_GET['c']); ?>"),
    'svg com script' => fn () => arquivoComConteudo('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'),
    'html como png' => fn () => arquivoComConteudo('foto.png', '<html><script>alert(1)</script></html>'),
    '60 MB' => fn () => pngDeVerdade()->size(61440),
]);

it('invasão: nome de arquivo com ../ não sai da pasta e vira só o nome', function (string $nome) {
    $card = cardMostrado($this->faseA);
    actingAsCliente($this->clienteA);

    $this->post(route('entregas.ajustes', $card), ['texto' => 'Ajuste', 'imagens' => [pngDeVerdade($nome)]]);

    foreach (PedidoAjuste::query()->with('arquivos')->get()->pluck('arquivos')->flatten() as $arquivo) {
        expect($arquivo->caminho)->toMatch('/^entregas\/[0-9a-f-]{36}\.png$/')
            ->and($arquivo->nome_original)->not->toContain('..')->not->toContain('/');
    }
    expect(Storage::disk('privado')->allFiles())->each->toStartWith('entregas/');
})->with(['../../.env.png', '..\\..\\index.png', '../../../public/shell.png']);

it('invasão: imagem válida do cliente é guardada com nome aleatório, fora da pasta pública', function () {
    $card = cardMostrado($this->faseA);
    actingAsCliente($this->clienteA);

    $this->post(route('entregas.ajustes', $card), ['texto' => 'Ajuste', 'imagens' => [pngDeVerdade('minha foto.png')]])->assertSessionHasNoErrors();
    $arquivo = PedidoAjuste::query()->sole()->arquivos()->sole();

    expect($arquivo->caminho)->toMatch('/^entregas\/[0-9a-f-]{36}\.png$/')
        ->and($arquivo->nome_original)->toBe('minha foto.png');
    Storage::disk('privado')->assertExists($arquivo->caminho);
    expect(file_exists(public_path($arquivo->caminho)))->toBeFalse();
});

it('invasão: HTML e SVG enviados pela equipe são sempre baixados, nunca abertos no navegador', function (string $nome, string $conteudo) {
    $card = cardMostrado($this->faseA, ['arquivos' => [arquivoComConteudo($nome, $conteudo)]]);
    $arquivo = $card->arquivos->first();
    actingAsCliente($this->clienteA);

    $resposta = $this->get(route('arquivos.ver', $arquivo))->assertOk();

    expect($resposta->headers->get('Content-Disposition'))->toStartWith('attachment')
        ->and($resposta->headers->get('Content-Security-Policy'))->toContain('sandbox')
        ->and($resposta->headers->get('X-Content-Type-Options'))->toBe('nosniff')
        ->and($arquivo->caminho)->not->toEndWith('.html')->not->toEndWith('.php');
})->with([
    'html' => ['pagina.html', '<html><body><script>alert(document.cookie)</script></body></html>'],
    'svg' => ['logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'],
    'php' => ['shell.php', '<?php system($_GET["c"]); ?>'],
]);

it('invasão: imagem aberta como prévia vem com nosniff e sandbox', function () {
    $card = cardMostrado($this->faseA, ['arquivos' => [pngDeVerdade()]]);
    actingAsCliente($this->clienteA);

    $resposta = $this->get(route('arquivos.ver', $card->arquivos->first()))->assertOk();

    expect($resposta->headers->get('Content-Type'))->toBe('image/png')
        ->and($resposta->headers->get('Content-Security-Policy'))->toContain('sandbox')
        ->and($resposta->headers->get('X-Content-Type-Options'))->toBe('nosniff');
});

it('invasão: campos escondidos enviados no ajuste e na aprovação são ignorados', function () {
    $card = cardMostrado($this->faseA);
    $outraEquipe = User::factory()->equipe()->create();
    actingAsCliente($this->clienteA);

    $this->post(route('entregas.ajustes', $card), [
        'texto' => 'Ajuste', 'entrega_id' => 999, 'frente_id' => 999, 'enviado_por' => $outraEquipe->id, 'tipo' => 'extra', 'ip' => '1.2.3.4',
    ])->assertSessionHasNoErrors();

    expect(PedidoAjuste::query()->sole())
        ->entrega_id->toBe($card->id)->frente_id->toBe($this->frenteA->id)
        ->enviado_por->toBe($this->clienteA->usuario->id)->tipo->toBe('revisao')->ip->not->toBe('1.2.3.4');
});

it('invasão: aprovar o mesmo card duas vezes não cria duas aprovações', function () {
    $card = cardMostrado($this->faseA);
    actingAsCliente($this->clienteA);

    $this->post(route('entregas.aprovar', $card), ['confirmar' => '1', 'user_id' => 999, 'hash_conteudo' => 'falso'])->assertRedirect();
    $this->post(route('entregas.aprovar', $card), ['confirmar' => '1']);
    $this->post(route('entregas.ajustes', $card), ['texto' => 'depois de aprovado']);

    expect(Aprovacao::query()->sole())->user_id->toBe($this->clienteA->usuario->id)->hash_conteudo->toBe($card->hash_conteudo)
        ->and(PedidoAjuste::query()->count())->toBe(0);
});

it('invasão: pessoa da equipe desativada com a sessão aberta perde o acesso na hora', function () {
    $usuario = actingAsEquipe();
    $usuario->forceFill(['ativo' => false])->save();

    $this->get(rotaEquipe('painel'))->assertNotFound();
    $this->get(rotaEquipe('clientes.show', $this->clienteA))->assertNotFound();
});

it('invasão: chutar o código do 2FA trocando de IP não escapa do bloqueio', function () {
    $usuario = User::factory()->equipe()->create();
    $this->post(rotaEquipe('entrar'), ['email' => $usuario->email, 'password' => UserFactory::SENHA]);

    foreach (['10.0.0.1', '10.0.0.2', '10.0.0.3'] as $ip) {
        foreach (range(1, 4) as $tentativa) {
            $this->withServerVariables(['REMOTE_ADDR' => $ip])->post(rotaEquipe('2fa'), ['codigo' => '000000']);
        }
    }

    $codigo = (new Google2FA)->getCurrentOtp(UserFactory::SEGREDO_2FA);
    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.4'])->post(rotaEquipe('2fa'), ['codigo' => $codigo])
        ->assertSessionHasErrors('codigo');
    $this->assertGuest();
});

it('SEG-05: 10 erros do código em uma hora bloqueiam a pessoa por até uma hora, mesmo devagar', function () {
    $usuario = User::factory()->equipe()->create();
    $codigo = fn () => (new Google2FA)->getCurrentOtp(UserFactory::SEGREDO_2FA);

    foreach (range(1, 10) as $tentativa) {
        $this->post(rotaEquipe('entrar'), ['email' => $usuario->email, 'password' => UserFactory::SENHA]);
        $this->post(rotaEquipe('2fa'), ['codigo' => '000000']);
        $this->travel(2)->minutes();
    }

    $this->post(rotaEquipe('entrar'), ['email' => $usuario->email, 'password' => UserFactory::SENHA]);
    $this->post(rotaEquipe('2fa'), ['codigo' => $codigo()])
        ->assertSessionHasErrors(['codigo' => __('ponte.equipe.codigo_bloqueado', ['minutos' => 40])]);
    $this->assertGuest();

    $this->travel(40)->minutes();
    $this->post(rotaEquipe('entrar'), ['email' => $usuario->email, 'password' => UserFactory::SENHA]);
    $this->post(rotaEquipe('2fa'), ['codigo' => $codigo()])->assertRedirect(rotaEquipe('painel'));
    $this->assertAuthenticatedAs($usuario);
});

it('invasão: o mesmo código do 2FA não serve duas vezes', function () {
    $usuario = User::factory()->equipe()->create();
    $codigo = (new Google2FA)->getCurrentOtp(UserFactory::SEGREDO_2FA);

    $this->post(rotaEquipe('entrar'), ['email' => $usuario->email, 'password' => UserFactory::SENHA]);
    $this->post(rotaEquipe('2fa'), ['codigo' => $codigo])->assertRedirect(rotaEquipe('painel'));
    $this->post(route('sair'));

    $this->post(rotaEquipe('entrar'), ['email' => $usuario->email, 'password' => UserFactory::SENHA]);
    $this->post(rotaEquipe('2fa'), ['codigo' => $codigo])->assertSessionHasErrors('codigo');
    $this->assertGuest();
});

it('invasão: link de redefinição de senha não serve para trocar a senha de outra pessoa', function () {
    $vitima = $this->clienteB->usuario;
    $token = Password::broker()->createToken($this->clienteA->usuario);

    $this->post(route('senha.salvar'), [
        'token' => $token, 'email' => $vitima->email, 'password' => 'NovaSenhaForte#2026', 'password_confirmation' => 'NovaSenhaForte#2026',
    ])->assertSessionHasErrors('email');

    expect(Hash::check(UserFactory::SENHA, $vitima->fresh()->password))->toBeTrue();
});

it('invasão: depois de entrar como cliente, ninguém vira equipe pela conta', function () {
    actingAsCliente($this->clienteA);

    $this->put(route('conta.senha'), [
        'senha_atual' => UserFactory::SENHA, 'password' => 'NovaSenhaForte#2026', 'password_confirmation' => 'NovaSenhaForte#2026',
        'papel' => 'equipe', 'cliente_id' => $this->clienteB->id, 'ativo' => true, 'two_factor_secret' => 'x',
    ])->assertSessionHasNoErrors();

    expect($this->clienteA->usuario->fresh())->papel->value->toBe('cliente')->cliente_id->toBe($this->clienteA->id)->two_factor_secret->toBeNull();
});

it('SEG-23: cliente não envia mais de 10 pedidos com arquivo por minuto', function () {
    $card = cardMostrado($this->faseA);
    actingAsCliente($this->clienteA);

    foreach (range(1, 10) as $tentativa) {
        $this->post(route('entregas.ajustes', $card), ['texto' => 'Ajuste '.$tentativa])->assertRedirect();
    }

    $this->post(route('entregas.ajustes', $card), ['texto' => 'Ajuste 11'])->assertTooManyRequests();
});

it('SEG-23: equipe não salva mais de 10 cards com arquivo por minuto', function () {
    $card = cardEmRascunho($this->faseA);
    actingAsEquipe();

    foreach (range(1, 10) as $tentativa) {
        $this->put(rotaEquipe('aprovacoes.update', $card), ['nota' => 'Versão '.$tentativa])->assertSessionHasNoErrors();
    }

    $this->put(rotaEquipe('aprovacoes.update', $card), ['nota' => 'Versão 11'])->assertTooManyRequests();
});
