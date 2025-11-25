<?php

namespace Rdcstarr\EasyApi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Rdcstarr\EasyApi\EasyApiService
 */
class EasyApi extends Facade
{
	protected static function getFacadeAccessor(): string
	{
		return \Rdcstarr\EasyApi\EasyApiService::class;
	}
}
