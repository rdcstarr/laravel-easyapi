<?php

namespace Rdcstarr\EasyApi;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Rdcstarr\EasyApi\Commands\EasyApiCommand;
use Rdcstarr\EasyApi\Middleware\EasyApiMiddleware;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EasyApiServiceProvider extends PackageServiceProvider
{
	public function configurePackage(Package $package): void
	{
		/*
		 * This class is a Package Service Provider
		 *
		 * More info: https://github.com/spatie/laravel-package-tools
		 */
		$package->name('easyapi')
			->hasCommand(EasyApiCommand::class);
	}

	public function register(): void
	{
		parent::register();

		$this->app->singleton('easyApi', EasyApiManager::class);
	}

	public function boot(): void
	{
		parent::boot();

		// Load migrations
		$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

		// Publish migrations
		$this->publishes([
			__DIR__ . '/../database/migrations' => database_path('migrations'),
		], 'migrations');

		// Register routes
		RouteServiceProvider::loadRoutesUsing(function ()
		{
			collect($this->getRoutesToLoad())
				->each(fn($route) => $this->{(string) $route}());
		});
	}

	protected function getRoutesToLoad(): array
	{
		return match (true)
		{
			app()->runningInConsole() => ['api', 'web'],
			Str::startsWith(request()->host(), 'api.') => ['api'],
			default => ['web']
		};
	}

	protected function api(): void
	{
		Route::middleware(EasyApiMiddleware::class)
			->withoutMiddleware('web')
			->name('api.')
			->group(base_path('routes/api.php'));
	}

	protected function web(): void
	{
		Route::middleware('web')
			->group(base_path('routes/web.php'));
	}

}
