<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\System;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FeaturePlan
 * 
 * @property int $plan_id
 * @property int $feature_id
 * @property string|null $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Plan $plan
 * @property Feature $feature
 *
 * @package App\Models\System
 */
class FeaturePlan extends Model
{
	protected $table = 'feature_plan';
	public $incrementing = false;

	protected $casts = [
		'plan_id' => 'int',
		'feature_id' => 'int'
	];

	protected $fillable = [
		'value'
	];

	public function plan()
	{
		return $this->belongsTo(Plan::class);
	}

	public function feature()
	{
		return $this->belongsTo(Feature::class);
	}
}
