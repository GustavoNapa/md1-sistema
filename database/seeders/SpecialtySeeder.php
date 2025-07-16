<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Specialty;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = [
            ['name' => 'Acupuntura', 'cfm_code' => 'ACU'],
            ['name' => 'Alergia e Imunologia', 'cfm_code' => 'ALI'],
            ['name' => 'Anestesiologia', 'cfm_code' => 'ANE'],
            ['name' => 'Angiologia', 'cfm_code' => 'ANG'],
            ['name' => 'Cardiologia', 'cfm_code' => 'CAR'],
            ['name' => 'Cirurgia Cardiovascular', 'cfm_code' => 'CCV'],
            ['name' => 'Cirurgia da Mão', 'cfm_code' => 'CMA'],
            ['name' => 'Cirurgia de Cabeça e Pescoço', 'cfm_code' => 'CCP'],
            ['name' => 'Cirurgia do Aparelho Digestivo', 'cfm_code' => 'CAD'],
            ['name' => 'Cirurgia Geral', 'cfm_code' => 'CGE'],
            ['name' => 'Cirurgia Oncológica', 'cfm_code' => 'CON'],
            ['name' => 'Cirurgia Pediátrica', 'cfm_code' => 'CPE'],
            ['name' => 'Cirurgia Plástica', 'cfm_code' => 'CPL'],
            ['name' => 'Cirurgia Torácica', 'cfm_code' => 'CTO'],
            ['name' => 'Cirurgia Vascular', 'cfm_code' => 'CVA'],
            ['name' => 'Clínica Médica', 'cfm_code' => 'CLM'],
            ['name' => 'Coloproctologia', 'cfm_code' => 'COL'],
            ['name' => 'Dermatologia', 'cfm_code' => 'DER'],
            ['name' => 'Endocrinologia e Metabologia', 'cfm_code' => 'END'],
            ['name' => 'Endoscopia', 'cfm_code' => 'ENS'],
            ['name' => 'Gastroenterologia', 'cfm_code' => 'GAS'],
            ['name' => 'Genética Médica', 'cfm_code' => 'GEN'],
            ['name' => 'Geriatria', 'cfm_code' => 'GER'],
            ['name' => 'Ginecologia e Obstetrícia', 'cfm_code' => 'GIN'],
            ['name' => 'Hematologia e Hemoterapia', 'cfm_code' => 'HEM'],
            ['name' => 'Homeopatia', 'cfm_code' => 'HOM'],
            ['name' => 'Infectologia', 'cfm_code' => 'INF'],
            ['name' => 'Mastologia', 'cfm_code' => 'MAS'],
            ['name' => 'Medicina de Emergência', 'cfm_code' => 'MEM'],
            ['name' => 'Medicina de Família e Comunidade', 'cfm_code' => 'MFC'],
            ['name' => 'Medicina do Trabalho', 'cfm_code' => 'MTR'],
            ['name' => 'Medicina do Tráfego', 'cfm_code' => 'MTF'],
            ['name' => 'Medicina Esportiva', 'cfm_code' => 'MES'],
            ['name' => 'Medicina Física e Reabilitação', 'cfm_code' => 'MFR'],
            ['name' => 'Medicina Intensiva', 'cfm_code' => 'MIN'],
            ['name' => 'Medicina Legal e Perícia Médica', 'cfm_code' => 'MLE'],
            ['name' => 'Medicina Nuclear', 'cfm_code' => 'MNU'],
            ['name' => 'Medicina Preventiva e Social', 'cfm_code' => 'MPS'],
            ['name' => 'Nefrologia', 'cfm_code' => 'NEF'],
            ['name' => 'Neurocirurgia', 'cfm_code' => 'NCR'],
            ['name' => 'Neurologia', 'cfm_code' => 'NEU'],
            ['name' => 'Nutrologia', 'cfm_code' => 'NUT'],
            ['name' => 'Oftalmologia', 'cfm_code' => 'OFT'],
            ['name' => 'Oncologia Clínica', 'cfm_code' => 'OCL'],
            ['name' => 'Ortopedia e Traumatologia', 'cfm_code' => 'ORT'],
            ['name' => 'Otorrinolaringologia', 'cfm_code' => 'OTO'],
            ['name' => 'Patologia', 'cfm_code' => 'PAT'],
            ['name' => 'Patologia Clínica/Medicina Laboratorial', 'cfm_code' => 'PCL'],
            ['name' => 'Pediatria', 'cfm_code' => 'PED'],
            ['name' => 'Pneumologia', 'cfm_code' => 'PNE'],
            ['name' => 'Psiquiatria', 'cfm_code' => 'PSI'],
            ['name' => 'Radiologia e Diagnóstico por Imagem', 'cfm_code' => 'RAD'],
            ['name' => 'Radioterapia', 'cfm_code' => 'RDT'],
            ['name' => 'Reumatologia', 'cfm_code' => 'REU'],
            ['name' => 'Urologia', 'cfm_code' => 'URO'],
            
            // Áreas de atuação mais comuns
            ['name' => 'Medicina Geral', 'cfm_code' => null, 'description' => 'Atendimento médico geral'],
            ['name' => 'Medicina Interna', 'cfm_code' => null, 'description' => 'Medicina interna geral'],
        ];

        foreach ($specialties as $specialty) {
            Specialty::create($specialty);
        }
    }
}
