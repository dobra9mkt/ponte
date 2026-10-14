<?php

namespace App\Models;

use Database\Factories\ModeloFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['nome', 'nome_cliente'])]
class Modelo extends Model
{
    /** @use HasFactory<ModeloFactory> */
    use HasFactory, SoftDeletes;

    /** @return HasMany<ModeloEtapa, $this> */
    public function etapas(): HasMany
    {
        return $this->hasMany(ModeloEtapa::class)->orderBy('ordem');
    }
}
