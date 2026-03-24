<?php

namespace Johnnywebs\LaravelPackageCreator\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

#[Signature('make:package {name}')]
#[Description('Create a basic boilerplate for john')]
class MakePackage extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $moduleName = Str::studly($name);

        $basePath = base_path("packages/{$moduleName}");

        if (File::exists($basePath)) {
            $this->error("Module already exists!");
            return;
        }

        $directories = [
            "{$basePath}/Components",
            "{$basePath}/Http/Controllers",
            "{$basePath}/Http/Middleware",
            "{$basePath}/Http/Requests",
            "{$basePath}/Models",
            "{$basePath}/Providers",
            "{$basePath}/Resources/assets/css",
            "{$basePath}/Resources/assets/js",
            "{$basePath}/Resources/views",
            "{$basePath}/Routes",
        ];

        foreach ($directories as $dir) {
            File::makeDirectory($dir, 0755, true);
        }

		//Service Provider
		File::put(
			"{$basePath}/Providers/{$moduleName}ServiceProvider.php",
			<<<PHP
			<?php

			namespace {$moduleName}\\Providers;

			use Illuminate\Support\ServiceProvider;

			class {$moduleName}ServiceProvider extends ServiceProvider
			{
				public function register(): void
				{
					//
				}

				public function boot(): void
				{
					$this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
					$this->loadViewsFrom(__DIR__ . '/../Resources/views', '{$moduleName}');
					$this->loadMigrationsFrom(__DIR__ . '/../Database/migrations');
				}
			}
			PHP
		);

		//Routes
		File::put(
			"{$basePath}/Routes/web.php",
			<<<PHP
			<?php

			use Illuminate\Support\Facades\Route;

			Route::get('/', function () {
				return '{$moduleName} module working';
			});
			PHP
		);
		
		//Controllers
		File::put(
			"{$basePath}/Http/Controllers/{$moduleName}Controller.php",
			<<<PHP
			<?php

			namespace {$moduleName}\Http\Controllers;

			use Illuminate\Http\Request;
			use Illuminate\Routing\Controller;

			class {$moduleName}Controller extends Controller
			{
				public function index()
				{
					return response()->json([
						'message' => '{$moduleName} module working'
					]);
				}
			}
			PHP
		);

		$this->info("Package {$moduleName} created successfully!");
    }
}
