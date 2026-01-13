<?php

return [

    'Gestão de Clientes e Inscrições' => [
        [
            'name' => 'Leads',
            'route' => 'leads.index',
            'permission' => 'leads.index',
            'icon' => 'fas fa-user-plus',
        ],
        [
            'name' => 'Clientes',
            'route' => 'clients.index',
            'permission' => 'clients.index',
            'icon' => 'fas fa-user-friends',
        ],
        [
            'name' => 'Produtos',
            'route' => 'products.index',
            'permission' => 'products.index',
            'icon' => 'fas fa-box',
        ],
        [
            'name' => 'Inscrições',
            'route' => 'inscriptions.index',
            'permission' => 'inscriptions.index',
            'icon' => 'fas fa-file-signature',
        ],
        [
            'name' => 'Importação',
            'route' => 'import.index',
            'permission' => 'import.index',
            'icon' => 'fas fa-file-import',
        ],
        [
            'name' => 'Plataforma de pagamento',
            'route' => 'payment_platforms.index',
            'permission' => 'payment_platforms.index',
            'icon' => 'fas fa-credit-card',
        ]
    ],
    'Comunicação' => [
        [
            'name' => 'WhatsApp',
            'route' => 'whatsapp.index',
            'permission' => 'whatsapp.index',
            'icon' => 'fab fa-whatsapp',
        ],
    ],
    // Adicionar outros grupos conforme necessário
];


