<?php

namespace Rdcstarr\EasyApi\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class ApiRequest extends FormRequest
{
	public function expectsJson(): bool
	{
		return true;
	}

	public function wantsJson(): bool
	{
		return true;
	}

	protected function failedValidation(Validator $validator): void
	{
		throw new HttpResponseException(
			response()->json([
				'success' => false,
				'message' => 'The given data was invalid.',
				'errors'  => $validator->errors(),
			], 422)
		);
	}
}
