<?php

namespace App\Servicos;

use App\Models\Arquivo;
use App\Models\Entrega;
use App\Models\PedidoAjuste;
use App\Rules\ArquivoPermitido;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ArquivosEntrega
{
    /**
     * @param  list<UploadedFile>  $uploads
     * @return list<array{nome_original: string, caminho: string, mime: string, tamanho: int, sha256: string}>
     */
    public function preparar(array $uploads): array
    {
        $arquivos = [];
        try {
            foreach ($uploads as $upload) {
                $mime = (string) $upload->getMimeType();
                $extensao = ArquivoPermitido::TIPOS[$mime] ?? 'bin';
                $caminho = $upload->storeAs('entregas', Str::uuid().'.'.$extensao, 'privado');
                $arquivos[] = [
                    'nome_original' => mb_substr($upload->getClientOriginalName(), 0, 255),
                    'caminho' => (string) $caminho, 'mime' => $mime,
                    'tamanho' => (int) $upload->getSize(),
                    'sha256' => (string) hash_file('sha256', $upload->getRealPath()),
                ];
            }

            return $arquivos;
        } catch (\Throwable $erro) {
            $this->descartar($arquivos);
            throw $erro;
        }
    }

    /** @param list<array<string, mixed>> $arquivos */
    public function registrar(Entrega|PedidoAjuste $dono, array $arquivos): void
    {
        foreach ($arquivos as $dados) {
            $arquivo = new Arquivo;
            $arquivo->forceFill($dados + [
                $dono instanceof Entrega ? 'entrega_id' : 'pedido_ajuste_id' => $dono->id,
                'enviado_por' => auth()->id(),
            ])->save();
        }
    }

    /** @param list<array<string, mixed>> $arquivos */
    public function descartar(array $arquivos): void
    {
        foreach ($arquivos as $arquivo) {
            Storage::disk('privado')->delete($arquivo['caminho']);
        }
    }

    public function conferir(Entrega $entrega): void
    {
        foreach ($entrega->arquivos as $arquivo) {
            $disco = Storage::disk('privado');
            if (! $disco->exists($arquivo->caminho) || ! hash_equals($arquivo->sha256, (string) hash_file('sha256', $disco->path($arquivo->caminho)))) {
                throw ValidationException::withMessages(['entrega' => __('entregas.integridade')]);
            }
        }
    }
}
