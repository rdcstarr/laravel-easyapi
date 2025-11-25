<?php

if (!function_exists('easyApi'))
{
	/**
	 * Get the EasyApi service instance.
	 *
	 * @return \Rdcstarr\EasyApi\EasyApiService
	 */
	function easyApi(): \Rdcstarr\EasyApi\EasyApiService
	{
		return app('easyApi');
	}
}
