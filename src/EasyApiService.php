<?php

namespace Rdcstarr\EasyApi;

use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Rdcstarr\EasyApi\Models\Api;
use RuntimeException;

class EasyApiService
{
	/**
	 * Create a new API key.
	 *
	 * @param int $maxAttempts
	 * @return \Rdcstarr\EasyApi\Models\Api
	 * @throws RuntimeException
	 */
	public function createKey(int $maxAttempts = 5)
	{
		for ($attempt = 1; $attempt <= $maxAttempts; $attempt++)
		{
			$key = hash('sha256', 'easyapi_' . Str::ulid() . '_' . Str::random(16));

			try
			{
				return Api::create(['key' => $key]);
			}
			catch (QueryException $e)
			{
				if (!Str::contains($e->getMessage(), ['Duplicate', 'UNIQUE', '23000']))
				{
					throw $e;
				}

				if ($attempt === $maxAttempts)
				{
					throw new RuntimeException("Could not generate a unique API key after $maxAttempts attempts.");
				}
			}
		}

		throw new RuntimeException("Could not generate a unique API key after $maxAttempts attempts.");
	}

	/**
	 * Validate if an API key exists.
	 *
	 * @param string $key
	 * @return bool
	 */
	public function validateKey(string $key): bool
	{
		return Api::where('key', $key)->exists();
	}

	/**
	 * Delete an API key.
	 *
	 * @param string $key
	 * @return bool
	 * @throws RuntimeException
	 */
	public function deleteKey(string $key): void
	{
		$api = Api::where('key', $key)->first();

		if (!$api)
		{
			throw new RuntimeException('API key not found.');
		}

		$api->delete();
	}
}
