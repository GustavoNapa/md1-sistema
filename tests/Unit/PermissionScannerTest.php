<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

class PermissionScannerTest extends TestCase
{
    /** @test */
    public function it_can_scan_routes_and_identify_permissions()
    {
        // Execute the Artisan command and capture its output
        Artisan::call("permission:scan");
        $output = Artisan::output();

        // Convert output to an array of permissions
        $permissions = array_filter(explode("\n", $output));
        $permissions = array_map("trim", $permissions);
        
        // Remove the header line
        array_shift($permissions);

        // Assert that expected permissions are found
        $this->assertContains("home", $permissions);
        $this->assertContains("clients.index", $permissions);
        $this->assertContains("clients.create", $permissions);
        $this->assertContains("clients.store", $permissions);
        $this->assertContains("clients.show", $permissions);
        $this->assertContains("clients.edit", $permissions);
        $this->assertContains("clients.update", $permissions);
        $this->assertContains("clients.destroy", $permissions);
        $this->assertContains("products.index", $permissions);
        $this->assertContains("products.create", $permissions);
        $this->assertContains("products.store", $permissions);
        $this->assertContains("products.show", $permissions);
        $this->assertContains("products.edit", $permissions);
        $this->assertContains("products.update", $permissions);
        $this->assertContains("products.destroy", $permissions);
        $this->assertContains("inscriptions.index", $permissions);
        $this->assertContains("inscriptions.create", $permissions);
        $this->assertContains("inscriptions.store", $permissions);
        $this->assertContains("inscriptions.show", $permissions);
        $this->assertContains("inscriptions.edit", $permissions);
        $this->assertContains("inscriptions.update", $permissions);
        $this->assertContains("inscriptions.destroy", $permissions);
        $this->assertContains("inscriptions.kanban-data", $permissions);
        $this->assertContains("feature-flags.index", $permissions);
        $this->assertContains("feature-flags.create", $permissions);
        $this->assertContains("feature-flags.store", $permissions);
        $this->assertContains("feature-flags.show", $permissions);
        $this->assertContains("feature-flags.edit", $permissions);
        $this->assertContains("feature-flags.update", $permissions);
        $this->assertContains("feature-flags.destroy", $permissions);
        $this->assertContains("feature-flags.toggle", $permissions);
    }
}


