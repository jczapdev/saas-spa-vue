<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\System;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Setting
 * 
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\System
 */
class Setting extends Model
{
	protected $table = 'settings';

	protected $fillable = [
		'key',
		'value'
	];
}
