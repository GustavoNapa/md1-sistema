<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class ScanPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:scan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scans routes and controllers to identify potential permissions.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $permissions = [];

        foreach (Route::getRoutes() as $route) {
            $action = $route->getAction();
            $routeName = $route->getName();

            if ($routeName) {
                // Use route name directly if available
                $permissions[] = $routeName;
            } else if (isset($action['uses'])) {
                if (is_string($action['uses']) && str_contains($action['uses'], '@')) {
                    // Controller@method syntax
                    list($controller, $method) = explode('@', $action['uses']);
                    $controllerName = strtolower(str_replace('Controller', '', class_basename($controller)));
                    $permissions[] = $controllerName . '.' . $method;
                } else if (is_callable($action['uses'])) {
                    // Closure or invokable class
                    $uri = $route->uri();
                    // Exclude routes that are just '/' or have parameters
                    if ($uri === '/' || str_contains($uri, '{')) {
                        continue;
                    }
                    $permissionName = str_replace('/', '.', $uri);
                    $permissions[] = $permissionName;
                }
            }
        }

        $this->info('Permissions scanned:');
        foreach (array_unique($permissions) as $permission) {
            $this->line($permission);
        }

        return Command::SUCCESS;
    }
}


