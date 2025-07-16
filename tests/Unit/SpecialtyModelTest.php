<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Specialty;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SpecialtyModelTest extends \Tests\TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_specialty_with_valid_data()
    {
        $specialty = Specialty::create([
            'name' => 'Cardiologia',
            'cfm_code' => 'CAR'
        ]);

        $this->assertInstanceOf(Specialty::class, $specialty);
        $this->assertEquals('Cardiologia', $specialty->name);
        $this->assertEquals('CAR', $specialty->cfm_code);
        $this->assertTrue($specialty->fresh()->is_active); // Refresh from database to get default value
    }

    /** @test */
    public function scope_active_returns_only_active_specialties()
    {
        Specialty::create(['name' => 'Ativa', 'cfm_code' => 'ATI', 'is_active' => true]);
        Specialty::create(['name' => 'Inativa', 'cfm_code' => 'INA', 'is_active' => false]);

        $activeSpecialties = Specialty::active()->get();

        $this->assertCount(1, $activeSpecialties);
        $this->assertEquals('Ativa', $activeSpecialties->first()->name);
    }

    /** @test */
    public function scope_order_by_name_sorts_alphabetically()
    {
        Specialty::create(['name' => 'Zootecnia', 'cfm_code' => 'ZOO']);
        Specialty::create(['name' => 'Anestesiologia', 'cfm_code' => 'ANE']);
        Specialty::create(['name' => 'Medicina Nuclear', 'cfm_code' => 'MN']);

        $specialties = Specialty::orderByName()->get();

        $this->assertEquals('Anestesiologia', $specialties->first()->name);
        $this->assertEquals('Zootecnia', $specialties->last()->name);
    }

    /** @test */
    public function specialty_has_correct_fillable_fields()
    {
        $specialty = new Specialty();
        $fillable = $specialty->getFillable();

        $expectedFillable = ['name', 'cfm_code', 'description', 'is_active'];
        
        $this->assertEquals($expectedFillable, $fillable);
    }

    /** @test */
    public function specialty_has_correct_casts()
    {
        $specialty = new Specialty();
        $casts = $specialty->getCasts();

        $this->assertArrayHasKey('is_active', $casts);
        $this->assertEquals('boolean', $casts['is_active']);
    }
}
