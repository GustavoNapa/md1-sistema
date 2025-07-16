<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ClientJavascriptValidationTest extends TestCase
{
    /** @test */
    public function phone_mask_format_validates_correct_patterns()
    {
        // Testar padrões de telefone válidos
        $validPhones = [
            '(11) 98765-4321',
            '(21) 99999-9999',
            '(85) 12345-6789',
            '11987654321',
            '21999999999'
        ];

        foreach ($validPhones as $phone) {
            $this->assertTrue($this->isValidPhoneFormat($phone), "Telefone {$phone} deveria ser válido");
        }
    }

    /** @test */
    public function phone_mask_format_rejects_invalid_patterns()
    {
        // Testar padrões de telefone inválidos
        $invalidPhones = [
            'texto apenas',
            'abc123def',
            '123',
            '(11) 1234',
            'abcdefghijk'
        ];

        foreach ($invalidPhones as $phone) {
            $this->assertFalse($this->isValidPhoneFormat($phone), "Telefone {$phone} deveria ser inválido");
        }
    }

    /** @test */
    public function birth_date_validation_logic()
    {
        $today = new \DateTime();
        $yesterday = (clone $today)->modify('-1 day');
        $tomorrow = (clone $today)->modify('+1 day');

        // Data de ontem deve ser válida
        $this->assertTrue($this->isValidBirthDate($yesterday->format('Y-m-d')));
        
        // Data de hoje deve ser válida
        $this->assertTrue($this->isValidBirthDate($today->format('Y-m-d')));
        
        // Data de amanhã deve ser inválida
        $this->assertFalse($this->isValidBirthDate($tomorrow->format('Y-m-d')));
    }

    /** @test */
    public function city_validation_rejects_numbers_only()
    {
        // Cidades válidas
        $validCities = [
            'São Paulo',
            'Rio de Janeiro',
            'Belo Horizonte',
            'São Paulo 2',
            'Cidade123',
            'Nova Lima'
        ];

        foreach ($validCities as $city) {
            $this->assertTrue($this->isValidCityFormat($city), "Cidade {$city} deveria ser válida");
        }

        // Cidades inválidas (apenas números)
        $invalidCities = [
            '123',
            '456789',
            '0001',
            '999'
        ];

        foreach ($invalidCities as $city) {
            $this->assertFalse($this->isValidCityFormat($city), "Cidade {$city} deveria ser inválida");
        }
    }

    /** @test */
    public function state_validation_accepts_valid_ufs()
    {
        $validStates = [
            'SP', 'RJ', 'MG', 'BA', 'PR', 'RS', 'PE', 'CE', 'PA', 'SC',
            'GO', 'PB', 'MA', 'ES', 'PI', 'AL', 'RN', 'MT', 'MS', 'DF',
            'SE', 'AM', 'RO', 'AC', 'AP', 'RR', 'TO'
        ];

        foreach ($validStates as $state) {
            $this->assertTrue($this->isValidStateFormat($state), "Estado {$state} deveria ser válido");
        }
    }

    /** @test */
    public function state_validation_rejects_invalid_ufs()
    {
        $invalidStates = [
            'XX', 'YY', 'ZZ', 'AB', 'CD', 'EF', 'GH'
        ];

        foreach ($invalidStates as $state) {
            $this->assertFalse($this->isValidStateFormat($state), "Estado {$state} deveria ser inválido");
        }
    }

    /** @test */
    public function region_validation_accepts_valid_regions()
    {
        $validRegions = [
            'Norte', 'Nordeste', 'Centro-Oeste', 'Sudeste', 'Sul'
        ];

        foreach ($validRegions as $region) {
            $this->assertTrue($this->isValidRegionFormat($region), "Região {$region} deveria ser válida");
        }
    }

    /** @test */
    public function region_validation_rejects_invalid_regions()
    {
        $invalidRegions = [
            '123', '456789', 'Região Inexistente', 'Centro', 'Oeste'
        ];

        foreach ($invalidRegions as $region) {
            $this->assertFalse($this->isValidRegionFormat($region), "Região {$region} deveria ser inválida");
        }
    }

    /**
     * Simula a validação de telefone do JavaScript
     */
    private function isValidPhoneFormat(string $phone): bool
    {
        // Remove tudo que não é dígito
        $digits = preg_replace('/\D/', '', $phone);
        
        // Deve ter pelo menos 10 dígitos e no máximo 11
        if (strlen($digits) < 10 || strlen($digits) > 11) {
            return false;
        }

        // Verifica se contém apenas números, espaços, parênteses, hífens e sinal de mais
        return preg_match('/^[\d\s\(\)\-\+]+$/', $phone);
    }

    /**
     * Simula a validação de data de nascimento do JavaScript
     */
    private function isValidBirthDate(string $date): bool
    {
        $selectedDate = new \DateTime($date);
        $today = new \DateTime();
        
        return $selectedDate <= $today;
    }

    /**
     * Simula a validação de cidade do JavaScript
     */
    private function isValidCityFormat(string $city): bool
    {
        // Não pode ser apenas números
        return !preg_match('/^\d+$/', trim($city));
    }

    /**
     * Simula a validação de estado do JavaScript
     */
    private function isValidStateFormat(string $state): bool
    {
        $validStates = [
            'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA',
            'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN',
            'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'
        ];

        return in_array($state, $validStates);
    }

    /**
     * Simula a validação de região do JavaScript
     */
    private function isValidRegionFormat(string $region): bool
    {
        $validRegions = ['Norte', 'Nordeste', 'Centro-Oeste', 'Sudeste', 'Sul'];
        
        // Não pode ser apenas números
        if (preg_match('/^\d+$/', trim($region))) {
            return false;
        }

        return in_array($region, $validRegions);
    }
}
