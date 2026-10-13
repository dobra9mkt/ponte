<?php

return [

    'caminho_equipe' => env('PONTE_CAMINHO_EQUIPE'),

    'emails_equipe' => array_filter(array_map('trim', explode(',', (string) env('PONTE_EMAILS_EQUIPE', '')))),

    'whatsapp_dobra9' => env('PONTE_WHATSAPP_DOBRA9'),

    'convite_validade_horas' => 72,

    'lembrar_de_mim_dias' => 30,

    'alerta_falhas_login_por_hora' => 20,

    'contrato_tamanho_max_kb' => 20480,

    'cores_clientes' => ['#0f4c81', '#0e9488', '#b68a16', '#7c3aed', '#db2777', '#ea580c', '#0891b2', '#4d7c0f'],

];
