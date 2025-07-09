<?php

namespace App\Http\Controllers;

use App\Imports\ClientsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('import.index');
    }

    public function clients(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            Excel::import(new ClientsImport, $request->file('file'));

            return redirect()->route('import.index')
                ->with('success', 'Clientes importados com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('import.index')
                ->with('error', 'Erro ao importar clientes: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'nome',
            'cpf',
            'e_mail',
            'data_nasc',
            'especialidade',
            'cidade_atendimento',
            'uf',
            'regiao',
            'instagram',
            'telefone',
            'vendedor',
            'ativo',
            'status',
            'classificacao',
            'medboss',
            'crmb',
            'inicio',
            'termino_original',
            'termino_real',
            'liberacao_plataforma_data',
            'semana_calendario_27_semanas',
            'semana_real',
            'valor_pago',
            'pagto',
            'obs_comercial',
            'obs',
            'turma'
        ];

        $filename = 'template_importacao_clientes.csv';
        
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fputcsv($handle, $headers);
        
        // Linha de exemplo
        $example = [
            'Dr. João Silva',
            '12345678901',
            'joao@exemplo.com',
            '1980-01-15',
            'Cardiologia',
            'São Paulo',
            'SP',
            'Sudeste',
            '@drjoao',
            '(11) 99999-9999',
            'Vendedor Exemplo',
            'SIM',
            'ativo',
            'Premium',
            'SIM',
            'CRM123456',
            '2024-01-01',
            '2024-12-31',
            '',
            '2024-01-01',
            '1',
            '1',
            '5000.00',
            'Cartão',
            'Cliente VIP',
            'Observações gerais',
            'Turma 2024.1'
        ];
        
        fputcsv($handle, $example);
        
        fclose($handle);
        exit;
    }
}
