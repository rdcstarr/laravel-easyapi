<?php

namespace Rdcstarr\EasyApi\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Api extends Model
{
	protected $table = 'api';

	protected $fillable = [
		'key',
		'access_count',
	];

	protected $casts = [
		'access_count' => 'integer',
		'created_at'   => 'datetime',
		'updated_at'   => 'datetime',
	];

	public function logs(): HasMany
	{
		return $this->hasMany(ApiLog::class, 'api_id');
	}

	/**
	 * Get masked key for display purposes.
	 */
	public function maskedKey(): Attribute
	{
		return Attribute::make(
			get: fn() => substr($this->key, 0, 12) . '...' . substr($this->key, -8),
		);
	}
}
