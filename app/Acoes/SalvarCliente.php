<?php

namespace App\Acoes;

use App\Enums\Papel;
use App\Models\Atividade;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SalvarCliente
{
    /**
     * @param  array<string, mixed>  $dados
     */
    public function __invoke(array $dados, ?Cliente $cliente = null, ?UploadedFile $contrato = null): Cliente
    {
        $novo = $cliente === null;
        $cliente ??= new Cliente;
        $contratoAntigo = $cliente->contrato_caminho;

        DB::transaction(function () use ($cliente, $dados, $contrato) {
            $cliente->fill(collect($dados)->except('contrato')->all());

            if ($contrato) {
                $cliente->contrato_caminho = $this->guardarContrato($contrato);
            }

            $cliente->save();

            $usuario = $cliente->usuario ?? new User;
            $usuario->fill(['name' => $cliente->contato_nome, 'email' => $cliente->email]);
            $usuario->forceFill(['papel' => Papel::Cliente, 'cliente_id' => $cliente->id])->save();
        });

        if ($contrato && $contratoAntigo) {
            Storage::disk('privado')->delete($contratoAntigo);
        }

        Atividade::registrar($novo ? 'cliente.cadastrado' : 'cliente.alterado', $cliente, array_filter([
            'contrato' => $contrato ? 'anexado' : null,
        ]));

        return $cliente;
    }

    private function guardarContrato(UploadedFile $arquivo): string
    {
        $caminho = 'contratos/'.Str::uuid()->toString();

        Storage::disk('privado')->put($caminho, Crypt::encryptString(base64_encode($arquivo->getContent())));

        return $caminho;
    }

    public static function lerContrato(Cliente $cliente): string
    {
        return base64_decode(Crypt::decryptString(Storage::disk('privado')->get((string) $cliente->contrato_caminho)));
    }
}
