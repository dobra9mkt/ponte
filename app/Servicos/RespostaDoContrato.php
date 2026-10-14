<?php

namespace App\Servicos;

use App\Acoes\SalvarCliente;
use App\Models\Atividade;
use App\Models\Cliente;
use Symfony\Component\HttpFoundation\Response;

/**
 * Entrega o PDF do contrato decifrado, sempre como download e sem cache (RN-07).
 */
class RespostaDoContrato
{
    public function __invoke(Cliente $cliente): Response
    {
        $conteudo = SalvarCliente::lerContrato($cliente);
        Atividade::registrar('contrato.baixado', $cliente);

        return response($conteudo, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="contrato-'.str($cliente->empresa)->slug().'.pdf"',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => 'sandbox',
            'Cache-Control' => 'no-store, private',
        ]);
    }
}
