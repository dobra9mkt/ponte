<?php

namespace App\Models;

use App\Enums\EstadoEtapa;
use App\Enums\SituacaoCliente;
use App\Enums\SituacaoFrente;
use Database\Factories\ClienteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

/**
 * @property SituacaoCliente $situacao
 */
#[Fillable(['empresa', 'contato_nome', 'email', 'email_avisos', 'telefone', 'endereco', 'observacoes'])]
#[Hidden(['contrato_caminho'])]
class Cliente extends Model
{
    /** @use HasFactory<ClienteFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'descartado_em' => 'datetime',
            'telefone' => 'encrypted',
            'endereco' => 'encrypted',
            'observacoes' => 'encrypted',
            'situacao' => SituacaoCliente::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Cliente $cliente) {
            $cliente->cor ??= Arr::random(config()->array('ponte.cores_clientes'));
            $cliente->situacao ??= SituacaoCliente::Ativo;
        });
    }

    /** @return HasOne<User, $this> */
    public function usuario(): HasOne
    {
        return $this->hasOne(User::class);
    }

    /** @return HasMany<Frente, $this> */
    public function frentes(): HasMany
    {
        return $this->hasMany(Frente::class);
    }

    /**
     * Serviços que o cliente acompanha no portal (RN-16).
     *
     * @return HasMany<Frente, $this>
     */
    public function frentesVisiveis(): HasMany
    {
        return $this->frentes()->where('situacao', '!=', SituacaoFrente::Arquivada)->whereNotNull('publicada_em');
    }

    /**
     * Serviços do portal, cada um marcado com `aguardando` quando há aprovação pendente (RN-16b).
     *
     * @return Collection<int, Frente>
     */
    public function frentesNoPortal(): Collection
    {
        return $this->frentesVisiveis()
            ->withExists(['etapasVisiveis as aguardando' => fn ($consulta) => $consulta->where('estado', EstadoEtapa::AguardandoCliente)])
            ->oldest('id')
            ->get();
    }

    public function emailDeAvisos(): string
    {
        return $this->email_avisos ?: $this->email;
    }

    public function estaAtivo(): bool
    {
        return $this->situacao === SituacaoCliente::Ativo;
    }

    public function temContrato(): bool
    {
        return $this->contrato_caminho !== null;
    }

    /** @return array{rotulo: string, tom: string} */
    public function estadoDoAcesso(): array
    {
        $usuario = $this->usuario;

        if ($usuario?->password !== null) {
            return ['rotulo' => 'Senha criada', 'tom' => 'sucesso'];
        }

        $convite = $usuario?->convites->sortByDesc('id')->first();

        return match (true) {
            $convite === null => ['rotulo' => 'Sem acesso', 'tom' => 'atencao'],
            $convite->expira_em->isPast() => ['rotulo' => 'Convite vencido', 'tom' => 'alerta'],
            default => ['rotulo' => 'Convite enviado em '.$convite->created_at->format('d/m'), 'tom' => 'info'],
        };
    }

    public function whatsappUrl(?string $mensagem = null): ?string
    {
        $numero = preg_replace('/\D/', '', (string) $this->telefone);

        if ($numero === '') {
            return null;
        }

        if (strlen($numero) <= 11) {
            $numero = '55'.$numero;
        }

        return 'https://wa.me/'.$numero.($mensagem ? '?text='.rawurlencode($mensagem) : '');
    }
}
