<?php

namespace Rdcstarr\EasyApi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Rdcstarr\EasyApi\EasyApiManager
 */
class EasyApi extends Facade
{
	protected static function getFacadeAccessor(): string
	{
		return \Rdcstarr\EasyApi\EasyApiManager::class;
	}
}
