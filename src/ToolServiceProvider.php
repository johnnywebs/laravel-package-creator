<?php

namespace Johnnywebs\LaravelPackageCreator;

use Illuminate\Support\ServiceProvider;
use Johnnywebs\LaravelPackageCreator\Commands\MakePackage;

class ToolServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerPackageProviders();
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakePackage::class,
            ]);
        }
    }
	
	/**
     * Auto-register all package service providers.
     */
    protected function registerPackageProviders(): void
    {
        $path = base_path('packages');

        if (!is_dir($path)) {
            return;
        }

        foreach (glob($path . '/*/Providers/*ServiceProvider.php') as $file) {
            $class = $this->getClassFromFile($file);

            if ($class && class_exists($class)) {
                $this->app->register($class);
            }
        }
    }

    /**
     * Extract full class name from file.
     */
    protected function getClassFromFile(string $file): ?string
    {
        $contents = file_get_contents($file);

        if (!$contents) {
            return null;
        }

        preg_match('/namespace\s+(.+?);/', $contents, $namespaceMatch);
        preg_match('/class\s+(\w+)/', $contents, $classMatch);

        if (!isset($namespaceMatch[1]) || !isset($classMatch[1])) {
            return null;
        }

        return $namespaceMatch[1] . '\\' . $classMatch[1];
    }
}