<?php

namespace App\Imports;

use App\Models\Client;
use App\Models\Vendor;
use App\Models\Inscription;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class ClientsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Buscar ou criar vendedor
        $vendor = null;
        if (!empty($row['vendedor'])) {
            $vendor = Vendor::firstOrCreate(
                ['nome' => $row['vendedor']],
                ['ativo' => true]
            );
        }

        // Buscar ou criar cliente
        $client = Client::firstOrCreate(
            ['cpf' => $this->formatCpf($row['cpf'] ?? '')],
            [
                'nome' => $row['nome'] ?? '',
                'email' => $row['e_mail'] ?? '',
                'data_nascimento' => $this->parseDate($row['data_nasc'] ?? ''),
                'especialidade' => $row['especialidade'] ?? '',
                'cidade_atendimento' => $row['cidade_atendimento'] ?? '',
                'uf' => $row['uf'] ?? '',
                'regiao' => $row['regiao'] ?? '',
                'instagram' => $row['instagram'] ?? '',
                'telefone' => $row['telefone'] ?? '',
                'ativo' => ($row['ativo'] ?? 'SIM') === 'SIM'
            ]
        );

        // Criar inscrição se não existir
        if ($client && !empty($row['turma'])) {
            Inscription::firstOrCreate(
                [
                    'client_id' => $client->id,
                    'turma' => $row['turma']
                ],
                [
                    'vendor_id' => $vendor ? $vendor->id : null,
                    'produto' => 'CRMBLACK', // Assumindo que é o produto principal
                    'status' => $this->parseStatus($row['status'] ?? ''),
                    'classificacao' => $row['classificacao'] ?? '',
                    'medboss' => ($row['medboss'] ?? 'NÃO') === 'SIM',
                    'crmb' => $row['crmb'] ?? '',
                    'data_inicio' => $this->parseDate($row['inicio'] ?? ''),
                    'data_termino_original' => $this->parseDate($row['termino_original'] ?? ''),
                    'data_termino_real' => $this->parseDate($row['termino_real'] ?? ''),
                    'data_liberacao_plataforma' => $this->parseDate($row['liberacao_plataforma_data'] ?? ''),
                    'semana_calendario' => $this->parseInt($row['semana_calendario_27_semanas'] ?? ''),
                    'semana_real' => $this->parseInt($row['semana_real'] ?? ''),
                    'valor_pago' => $this->parseDecimal($row['valor_pago'] ?? ''),
                    'forma_pagamento' => $row['pagto'] ?? '',
                    'obs_comercial' => $row['obs_comercial'] ?? '',
                    'obs_geral' => $row['obs'] ?? '',
                ]
            );
        }

        return $client;
    }

    public function rules(): array
    {
        return [
            'nome' => 'required|string',
            'cpf' => 'required|string',
            'e_mail' => 'required|email',
        ];
    }

    private function formatCpf($cpf)
    {
        return preg_replace('/[^0-9]/', '', $cpf);
    }

    private function parseDate($date)
    {
        if (empty($date)) return null;
        
        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseInt($value)
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private function parseDecimal($value)
    {
        if (empty($value)) return null;
        
        // Remove caracteres não numéricos exceto vírgula e ponto
        $value = preg_replace('/[^0-9,.]/', '', $value);
        $value = str_replace(',', '.', $value);
        
        return is_numeric($value) ? (float) $value : null;
    }

    private function parseStatus($status)
    {
        $status = strtolower(trim($status));
        
        switch ($status) {
            case 'ativo':
            case 'em andamento':
                return 'ativo';
            case 'pausado':
                return 'pausado';
            case 'cancelado':
                return 'cancelado';
            case 'concluido':
            case 'finalizado':
                return 'concluido';
            default:
                return 'ativo';
        }
    }
}
