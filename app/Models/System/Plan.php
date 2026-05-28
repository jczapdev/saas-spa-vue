<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\System;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Plan
 * 
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property float $price
 * @property string $currency
 * @property int $duration_in_days
 * @property bool $is_free
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|Tenant[] $tenants
 * @property Collection|Subscription[] $subscriptions
 * @property Collection|Feature[] $features
 *
 * @package App\Models\System
 */
class Plan extends Model
{
	use SoftDeletes;
	protected $table = 'plans';

	protected $casts = [
		'price' => 'float',
		'duration_in_days' => 'int',
		'is_free' => 'bool',
		'is_active' => 'bool'
	];

	protected $fillable = [
		'name',
		'slug',
		'description',
		'price',
		'currency',
		'duration_in_days',
		'is_free',
		'is_active'
	];

	public function tenants()
	{
		return $this->hasMany(Tenant::class);
	}

	public function subscriptions()
	{
		return $this->hasMany(Subscription::class);
	}

	public function features()
	{
		return $this->belongsToMany(Feature::class)
					->withPivot('value')
					->withTimestamps();
	}
}
