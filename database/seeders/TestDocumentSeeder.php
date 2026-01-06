<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDocumentSeeder extends Seeder
{
    public function run()
    {
        DB::table('documents')->insert([
            'inscription_id' => 1, // Ajuste para um inscription_id válido
            'nome' => 'Contrato de Teste',
            'title' => 'Contrato Principal - Teste',
            'sign_url' => 'https://app.zapsign.com.br/verificar/a6519169-7340-4da9-8370-f7c5082b5ea6',
            'file_web_view' => 'https://app.zapsign.com.br/verificar/a6519169-7340-4da9-8370-f7c5082b5ea6',
            'token' => 'a6519169-7340-4da9-8370-f7c5082b5ea6',
            'file_type' => 'application/pdf',
            'file_size' => 1024000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
