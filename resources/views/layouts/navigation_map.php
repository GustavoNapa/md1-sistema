<?php

return [
    'Administração' => [
        [
            'name' => 'Usuários',
            'route' => 'users.index',
            'permission' => 'users.index',
            'icon' => 'fas fa-users',
        ],
        [
            'name' => 'Cargos',
            'route' => 'roles.index',
            'permission' => 'roles.index',
            'icon' => 'fas fa-user-tag',
        ],
        [
            'name' => 'Permissões',
            'route' => 'permissions.index',
            'permission' => 'permissions.index',
            'icon' => 'fas fa-shield-alt',
        ],
        [
            'name' => 'Funcionalidades',
            'route' => 'feature-flags.index',
            'permission' => 'feature-flags.index',
            'icon' => 'fas fa-toggle-on',
        ],
        [
            'name' => 'Logs de Webhooks',
            'route' => 'webhook-logs.index',
            'permission' => 'webhook-logs.index',
            'icon' => 'fas fa-exchange-alt',
        ],
        [
            'name' => 'Integrações',
            'route' => 'integrations.index',
            'permission' => 'integrations.index',
            'icon' => 'fas fa-plug',
        ],
    ],
    'Gestão de Clientes e Inscrições' => [
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


