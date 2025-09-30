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
