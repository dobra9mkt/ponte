<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $titulo }}</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#26384d;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:32px 16px;">
<tr><td align="center">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">
<tr><td style="background:#071e3d;border-radius:16px 16px 0 0;padding:24px 32px;">
<span style="font-size:20px;font-weight:700;color:#ffffff;letter-spacing:.5px;">Dobra <span style="color:#60a5fa;">9</span></span>
</td></tr>
<tr><td style="background:#ffffff;border-radius:0 0 16px 16px;padding:32px;">
<h1 style="margin:0 0 16px;font-size:20px;line-height:1.4;color:#071e3d;">{{ $titulo }}</h1>
<p style="margin:0 0 28px;font-size:16px;line-height:1.65;">{{ $texto }}</p>
<table role="presentation" cellpadding="0" cellspacing="0"><tr><td style="border-radius:10px;background:#2563eb;">
<a href="{{ $url }}" style="display:inline-block;padding:14px 28px;font-size:16px;font-weight:700;color:#ffffff;text-decoration:none;">{{ $botao }}</a>
</td></tr></table>
<p style="margin:28px 0 0;font-size:13px;line-height:1.6;color:#4f5f74;">Se o botão não funcionar, copie este endereço no navegador:<br><span style="word-break:break-all;">{{ $url }}</span></p>
</td></tr>
<tr><td style="padding:20px 8px;font-size:12px;line-height:1.6;color:#4f5f74;text-align:center;">{{ __('ponte.emails.rodape') }}</td></tr>
</table>
</td></tr>
</table>
</body>
</html>
