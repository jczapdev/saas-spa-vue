<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\System;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Feature
 * 
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Plan[] $plans
 *
 * @package App\Models\System
 */
class Feature extends Model
{
	protected $table = 'features';

	protected $fillable = [
		'name',
		'code',
		'description'
	];

	public function plans()
	{
		return $this->belongsToMany(Plan::class)
					->withPivot('value')
					->withTimestamps();
	}
}
