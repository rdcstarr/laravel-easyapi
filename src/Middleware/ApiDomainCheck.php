<?php

namespace Rdcstarr\EasyApi\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiDomainCheck
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next)
	{
		$host = $request->getHost();

		if (!Str::startsWith($host, 'api.'))
		{
			abort(404);
		}

		return $next($request);
	}
}
