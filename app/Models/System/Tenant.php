<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\System;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Tenant
 * 
 * @property string $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $data
 * @property int|null $plan_id
 * @property string $owner_name
 * @property string $owner_email
 * @property string $owner_password
 * @property string $status
 * @property Carbon|null $subscription_ends_at
 * @property Carbon|null $trial_ends_at
 * @property bool $is_active
 * @property string $name
 * @property string $tenancy_db_name
 * @property Carbon|null $canceled_at
 * 
 * @property Plan|null $plan
 * @property Collection|Domain[] $domains
 * @property Collection|Subscription[] $subscriptions
 *
 * @package App\Models\System
 */
class Tenant extends Model
{
	protected $table = 'tenants';
	public $incrementing = false;

	protected $casts = [
		'plan_id' => 'int',
		'subscription_ends_at' => 'datetime',
		'trial_ends_at' => 'datetime',
		'is_active' => 'bool',
		'canceled_at' => 'datetime'
	];

	protected $hidden = [
		'owner_password'
	];

	protected $fillable = [
		'data',
		'plan_id',
		'owner_name',
		'owner_email',
		'owner_password',
		'status',
		'subscription_ends_at',
		'trial_ends_at',
		'is_active',
		'name',
		'tenancy_db_name',
		'canceled_at'
	];

	public function plan()
	{
		return $this->belongsTo(Plan::class);
	}

	public function domains()
	{
		return $this->hasMany(Domain::class);
	}

	public function subscriptions()
	{
		return $this->hasMany(Subscription::class);
	}
}
