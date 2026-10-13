<?php

use App\Enums\Papel;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => actingAsEquipe());

function arquivoReal(string $nome, string $conteudo): UploadedFile
{
    $caminho = tempnam(sys_get_temp_dir(), 'ponte');
    file_put_contents($caminho, $conteudo);

    return new UploadedFile($caminho, $nome, null, null, true);
}

function pdfFalso(): UploadedFile
{
    return arquivoReal('contrato.pdf', "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF");
}

it('cadastra cliente e cria o login dele sem senha', function () {
    $this->post(rotaEquipe('clientes.store'), [
        'empresa' => 'Escola de Música Bela Bartók',
        'contato_nome' => 'Maria',
        'email' => 'Maria@Exemplo.com ',
        'telefone' => '(31) 99999-0000',
    ])->assertRedirect();

    $cliente = Cliente::sole();
    $usuario = $cliente->usuario;

    expect($usuario->email)->toBe('maria@exemplo.com')
        ->and($usuario->papel)->toBe(Papel::Cliente)
        ->and($usuario->password)->toBeNull();

    $this->get(rotaEquipe('clientes.show', $cliente))->assertOk()->assertSee('Bela Bartók')->assertSee('Sem acesso');
});

it('SEG-51: telefone, endereço e observações não ficam em texto puro no banco', function () {
    $this->post(rotaEquipe('clientes.store'), [
        'empresa' => 'Empresa',
        'contato_nome' => 'Contato',
        'email' => 'contato@exemplo.com',
        'telefone' => '31999990000',
        'endereco' => 'Rua das Flores, 123',
        'observacoes' => 'Prefere ligação à tarde',
    ]);

    $linha = (array) DB::table('clientes')->first();

    expect($linha['telefone'])->not->toContain('31999990000')
        ->and($linha['endereco'])->not->toContain('Flores')
        ->and($linha['observacoes'])->not->toContain('tarde')
        ->and(Cliente::sole()->endereco)->toBe('Rua das Flores, 123');
});

it('RN-07 / SEG-26: contrato fica cifrado no disco e a equipe baixa o original', function () {
    $pdf = pdfFalso();

    $this->post(rotaEquipe('clientes.store'), [
        'empresa' => 'Empresa',
        'contato_nome' => 'Contato',
        'email' => 'contato@exemplo.com',
        'contrato' => $pdf,
    ])->assertSessionHasNoErrors();

    $cliente = Cliente::sole();
    $noDisco = Storage::disk('privado')->get($cliente->contrato_caminho);

    expect($noDisco)->not->toContain('%PDF');

    $resposta = $this->get(rotaEquipe('clientes.contrato', $cliente))->assertOk();
    expect($resposta->getContent())->toStartWith('%PDF');
    expect($resposta->headers->get('Content-Disposition'))->toContain('attachment');
    expect($resposta->headers->get('Content-Security-Policy'))->toBe('sandbox');

    Storage::disk('privado')->delete($cliente->contrato_caminho);
});

it('RN-07: contrato só aceita PDF de verdade', function () {
    $falso = arquivoReal('contrato.pdf', '<?php echo "oi";');

    $this->post(rotaEquipe('clientes.store'), [
        'empresa' => 'Empresa',
        'contato_nome' => 'Contato',
        'email' => 'contato@exemplo.com',
        'contrato' => $falso,
    ])->assertSessionHasErrors('contrato');

    expect(Cliente::count())->toBe(0);
});

it('SEG-13: nenhuma rota do cliente devolve o contrato', function () {
    $this->post(route('sair'));
    $cliente = Cliente::factory()->comLogin()->create();
    $cliente->forceFill(['contrato_caminho' => 'contratos/teste'])->save();
    actingAsCliente($cliente);

    foreach ([route('inicio'), route('conta')] as $url) {
        $this->get($url)->assertDontSee('contratos/teste')->assertDontSee('Contrato');
    }

    $this->get(rotaEquipe('clientes.contrato', $cliente))->assertNotFound();
    expect($cliente->toArray())->not->toHaveKey('contrato_caminho');
});

it('e-mail de acesso não pode repetir', function () {
    User::factory()->equipe()->create(['email' => 'rose@dobra9.com.br']);

    $this->post(rotaEquipe('clientes.store'), [
        'empresa' => 'Empresa',
        'contato_nome' => 'Contato',
        'email' => 'rose@dobra9.com.br',
    ])->assertSessionHasErrors('email');
});

it('editar o e-mail do cliente atualiza o e-mail de acesso', function () {
    $cliente = Cliente::factory()->comLogin()->create();

    $this->put(rotaEquipe('clientes.update', $cliente), [
        'empresa' => $cliente->empresa,
        'contato_nome' => $cliente->contato_nome,
        'email' => 'novo@exemplo.com',
    ])->assertRedirect();

    expect($cliente->usuario->fresh()->email)->toBe('novo@exemplo.com');
});

it('SEG-12: campos fora do formulário são ignorados', function () {
    $this->post(rotaEquipe('clientes.store'), [
        'empresa' => 'Empresa',
        'contato_nome' => 'Contato',
        'email' => 'contato@exemplo.com',
        'situacao' => 'arquivado',
        'contrato_caminho' => '../../.env',
        'papel' => 'equipe',
    ]);

    $cliente = Cliente::sole();
    expect($cliente->situacao->value)->toBe('ativo')
        ->and($cliente->contrato_caminho)->toBeNull()
        ->and($cliente->usuario->papel)->toBe(Papel::Cliente);
});

it('RN-08: desativar encerra as sessões e o convite do cliente', function () {
    $cliente = Cliente::factory()->comLogin()->create();
    DB::table('sessions')->insert(['id' => 'sessao-do-cliente', 'user_id' => $cliente->usuario->id, 'payload' => '', 'last_activity' => time()]);

    $this->post(rotaEquipe('clientes.desativar', $cliente))->assertRedirect();

    expect($cliente->fresh()->estaAtivo())->toBeFalse()
        ->and($cliente->usuario->fresh()->ativo)->toBeFalse()
        ->and(DB::table('sessions')->where('id', 'sessao-do-cliente')->exists())->toBeFalse();

    $this->post(rotaEquipe('clientes.reativar', $cliente));
    expect($cliente->fresh()->estaAtivo())->toBeTrue();
});

it('lista de clientes mostra a situação do acesso', function () {
    Cliente::factory()->comLogin()->create(['empresa' => 'Empresa A']);
    Cliente::factory()->comLogin(senhaCriada: false)->create(['empresa' => 'Empresa B']);

    $this->get(rotaEquipe('clientes.index'))->assertOk()->assertSeeInOrder(['Empresa A', 'Senha criada', 'Empresa B', 'Sem acesso']);
});

it('a ficha não mostra quando o cliente entrou pela última vez', function () {
    $cliente = Cliente::factory()->comLogin()->create();
    $cliente->usuario->forceFill(['ultimo_acesso_em' => now()->subHour()])->save();

    $this->get(rotaEquipe('clientes.show', $cliente))->assertOk()->assertDontSee('Último acesso');
});
