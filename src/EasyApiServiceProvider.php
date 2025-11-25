<?php

namespace Rdcstarr\EasyApi;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Rdcstarr\EasyApi\Commands\EasyApiCommand;
use Rdcstarr\EasyApi\Middleware\EasyApiMiddleware;
use Rdcstarr\EasyApi\Middleware\NoCacheMiddleware;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EasyApiServiceProvider extends PackageServiceProvider
{
	/**
	 * Cached middleware arrays for better performance
	 */
	private static array $middlewareGroups = [
		'api' => [EasyApiMiddleware::class, NoCacheMiddleware::class],
		'web' => ['web'],
	];

	/*
	 * This class is a Package Service Provider
	 *
	 * More info: https://github.com/spatie/laravel-package-tools
	 */
	public function configurePackage(Package $package): void
	{
		$package
			->name('laravel-easyapi')
			->discoversMigrations()
			->runsMigrations()
			->hasCommands([
				EasyApiCommand::class,
			])
			->hasInstallCommand(function (InstallCommand $command)
			{
				$command
					->publishMigrations()
					->askToRunMigrations();
			});
	}

	public function register(): void
	{
		parent::register();

		$this->app->singleton('easyApi', EasyApiService::class);
	}

	public function boot(): void
	{
		parent::boot();

		RouteServiceProvider::loadRoutesUsing(function ()
		{
			$this->api();
			$this->web();
		});
	}

	protected function api(): void
	{
		Route::domain('api.' . $this->domain())
			->middleware(self::$middlewareGroups['api'])
			->withoutMiddleware('web')
			->name('api.')
			->group(base_path('routes/api.php'));
	}

	protected function web(): void
	{
		Route::domain($this->domain())
			->middleware(self::$middlewareGroups['web'])
			->group(base_path('routes/web.php'));
	}

	protected function domain(): string
	{
		return once(fn() => Str::of(parse_url(config('app.url'), PHP_URL_HOST))->lower()->replaceStart('www.', '')->replaceStart('api.', '')->toString());
	}
}
