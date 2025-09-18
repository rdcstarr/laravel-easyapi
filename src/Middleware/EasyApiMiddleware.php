<?php

namespace Rdcstarr\EasyApi\Middleware;

use Closure;
use Illuminate\Http\Request;
use Rdcstarr\EasyApi\Models\Api;
use Rdcstarr\EasyApi\Models\ApiLog;

class EasyApiMiddleware
{
	public function handle(Request $request, Closure $next)
	{
		/* $bearerToken = $request->bearerToken();

		if (!$bearerToken)
		{
			return response()->json([
				'message' => 'Bearer token is required',
			], 401);
		}

		$api = $this->isValidToken($bearerToken);

		if (!$api)
		{
			return response()->json([
				'message' => 'Invalid bearer token',
			], 401);
		}

		// Log the API access
		$this->logApiAccess($api, $request);

		// Increment access count
		$api->increment('access_count'); */

		return $next($request);
	}

	private function isValidToken(string $token): ?Api
	{
		// Simple optimized query without caching
		return Api::where('key', $token)
			->select(['id', 'key', 'access_count']) // Only select needed columns
			->first();
	}

	private function logApiAccess(Api $api, Request $request): void
	{
		// Use queue for async logging to improve performance when available
		$logData = [
			'api_id'     => $api->id,
			'endpoint'   => $request->fullUrl(),
			'ip_address' => $request->ip(),
			'user_agent' => $request->userAgent(),
		];

		if (config('queue.default') !== 'sync')
		{
			// Queue the log creation for better performance
			dispatch(fn() => ApiLog::create($logData))->onQueue('api-logs');
		}
		else
		{
			// Fallback to synchronous creation
			ApiLog::create($logData);
		}
	}
}
