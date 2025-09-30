<?php

namespace Rdcstarr\EasyApi\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
	protected $dontReport = [
		//
	];

	protected $dontFlash = [
		'current_password',
		'password',
		'password_confirmation',
	];

	public function register(): void
	{
		$this->reportable(function (Throwable $e)
		{
			//
		});

		// Handle validation errors for API
		$this->renderable(function (ValidationException $e, $request)
		{
			if ($request->expectsJson())
			{
				return response()->json([
					'message' => $e->getMessage(),
					'errors'  => $e->errors(),
				], 422);
			}
		});

		// Handle 404 errors for API
		$this->renderable(function (NotFoundHttpException $e, $request)
		{
			if ($request->expectsJson())
			{
				return response()->json([
					'message' => 'Resource not found',
				], 404);
			}
		});

		// Handle for all other errors
		$this->renderable(function (Throwable $e, $request)
		{
			if ($request->expectsJson() && !($e instanceof ValidationException))
			{
				$message = app()->environment('production')
					? 'An error occurred while processing your request'
					: $e->getMessage();

				$statusCode = method_exists($e, 'getStatusCode')
					? $e->getStatusCode()
					: 500;

				return response()->json([
					'message' => $message,
				], $statusCode);
			}
		});
	}
}
