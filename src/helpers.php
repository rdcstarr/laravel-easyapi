<?php

if (!function_exists('easyApi'))
{
	/**
	 * Get the EasyApi manager instance.
	 *
	 * @return \Rdcstarr\EasyApi\EasyApiManager
	 */
	function easyApi(): \Rdcstarr\EasyApi\EasyApiManager
	{
		return app('easyApi');
	}
}

if (!function_exists('api_route'))
{
	/**
	 * Generate URL for API routes with correct domain.
	 *
	 * @param  string  $name
	 * @param  mixed  $parameters
	 * @param  bool  $absolute
	 * @return string
	 */
	function api_route($name, $parameters = [], $absolute = true)
	{
		// Ensure the route name has the 'api.' prefix
		$routeName = \Illuminate\Support\Str::startsWith($name, 'api.') ? $name : "api.$name";
		return route($routeName, $parameters, $absolute);
	}
}

if (!function_exists('web_route'))
{
	/**
	 * Generate URL for web routes with correct domain (removes api. subdomain).
	 *
	 * @param  string  $name
	 * @param  mixed  $parameters
	 * @param  bool  $absolute
	 * @return string
	 */
	function web_route($name, $parameters = [], $absolute = true)
	{
		// Ensure the route name doesn't have the 'api.' prefix
		$routeName = \Illuminate\Support\Str::startsWith($name, 'api.')
			? \Illuminate\Support\Str::after($name, 'api.')
			: $name;

		$url = route($routeName, $parameters, $absolute);

		// If absolute URL and we're on API subdomain, convert to web domain
		if ($absolute && request()->getHost() && \Illuminate\Support\Str::startsWith(request()->getHost(), 'api.'))
		{
			$webHost = \Illuminate\Support\Str::after(request()->getHost(), 'api.');
			$url     = str_replace("://api.$webHost", "://$webHost", $url);
		}

		return $url;
	}
}
