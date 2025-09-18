<?php

namespace Rdcstarr\EasyApi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiLog extends Model
{
	protected $fillable = [
		'api_id',
		'endpoint',
		'ip_address',
		'user_agent',
	];

	protected $casts = [
		'created_at' => 'datetime',
		'updated_at' => 'datetime',
	];

	public function api(): BelongsTo
	{
		return $this->belongsTo(Api::class, 'api_id');
	}
}
