<?php

namespace Rdcstarr\EasyApi;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Rdcstarr\EasyApi\Commands\EasyApiCommand;
use Rdcstarr\EasyApi\Middleware\ApiDomainCheck;
use Rdcstarr\EasyApi\Middleware\EasyApiMiddleware;
use Rdcstarr\EasyApi\Middleware\WebDomainCheck;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EasyApiServiceProvider extends PackageServiceProvider
{
	/**
	 * Cached domain patterns for better performance
	 */
	private static array $domainPatterns = [
		'api' => '[a-zA-Z0-9.-]+',
		'web' => '(?!api\.)[a-zA-Z0-9.-]+',
	];

	/**
	 * Cached middleware arrays for better performance
	 */
	private static array $middlewareGroups = [
		'api' => [EasyApiMiddleware::class],
		'web' => ['web'],
	];

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

		// Register domain check middleware
		$this->app['router']->aliasMiddleware('api-domain-check', ApiDomainCheck::class);
		$this->app['router']->aliasMiddleware('web-domain-check', WebDomainCheck::class);

		// Load migrations only in console (optimization)
		if (app()->runningInConsole())
		{
			$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

			// Publish migrations only in console (optimization)
			$this->publishes([
				__DIR__ . '/../database/migrations' => database_path('migrations'),
			], 'migrations');
		}

		// Register routes - always register both with different strategies
		RouteServiceProvider::loadRoutesUsing(function ()
		{
			$this->api();
			$this->web();
		});
	}

	protected function api(): void
	{
		Route::middleware(['api-domain-check', ...self::$middlewareGroups['api']])
			->withoutMiddleware('web')
			->prefix('v1')
			->name('api.')
			->group(base_path('routes/api.php'));
	}

	protected function web(): void
	{
		Route::middleware(['web-domain-check', ...self::$middlewareGroups['web']])
			->group(base_path('routes/web.php'));
	}

}
