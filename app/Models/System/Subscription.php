<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\System;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Subscription
 * 
 * @property int $id
 * @property string $tenant_id
 * @property int $plan_id
 * @property string|null $stripe_id
 * @property string|null $stripe_status
 * @property string|null $stripe_price
 * @property int|null $quantity
 * @property Carbon|null $trial_ends_at
 * @property Carbon|null $ends_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Tenant $tenant
 * @property Plan $plan
 *
 * @package App\Models\System
 */
class Subscription extends Model
{
	protected $table = 'subscriptions';

	protected $casts = [
		'plan_id' => 'int',
		'quantity' => 'int',
		'trial_ends_at' => 'datetime',
		'ends_at' => 'datetime'
	];

	protected $fillable = [
		'tenant_id',
		'plan_id',
		'stripe_id',
		'stripe_status',
		'stripe_price',
		'quantity',
		'trial_ends_at',
		'ends_at'
	];

	public function tenant()
	{
		return $this->belongsTo(Tenant::class);
	}

	public function plan()
	{
		return $this->belongsTo(Plan::class);
	}
}
