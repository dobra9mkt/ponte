<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class CabecalhosSeguranca
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        foreach ($this->cabecalhos($request) as $nome => $valor) {
            if (! $response->headers->has($nome)) {
                $response->headers->set($nome, $valor);
            }
        }

        $response->headers->remove('X-Powered-By');

        return $response;
    }

    /** @return array<string, string> */
    private function cabecalhos(Request $request): array
    {
        return [
            'Content-Security-Policy' => $this->csp($request),
            'Strict-Transport-Security' => 'max-age=63072000; includeSubDomains',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=(), browsing-topics=()',
            'Cross-Origin-Opener-Policy' => 'same-origin',
            'Cross-Origin-Resource-Policy' => 'same-origin',
            'X-Permitted-Cross-Domain-Policies' => 'none',
            'X-Robots-Tag' => 'noindex, nofollow',
        ];
    }

    private function csp(Request $request): string
    {
        $self = "'self'";
        $connect = $self;

        if (app()->isLocal() && Vite::isRunningHot()) {
            $hot = rtrim((string) file_get_contents(public_path('hot')));
            $self .= ' '.$hot;
            $connect = $self.' '.preg_replace('/^http/', 'ws', $hot);
        }

        $diretivas = [
            "default-src {$self}",
            "script-src {$self}",
            "style-src {$self}",
            "img-src {$self} data:",
            "font-src {$self}",
            "connect-src {$connect}",
            "form-action 'self'",
            "base-uri 'none'",
            "object-src 'none'",
            "frame-ancestors 'none'",
        ];

        if ($request->isSecure() || app()->isProduction()) {
            $diretivas[] = 'upgrade-insecure-requests';
        }

        return implode('; ', $diretivas);
    }
}
