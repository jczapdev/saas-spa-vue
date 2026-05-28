<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\System;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ActivityLog
 * 
 * @property int $id
 * @property string|null $log_name
 * @property string $description
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property string|null $causer_type
 * @property int|null $causer_id
 * @property string|null $properties
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\System
 */
class ActivityLog extends Model
{
	protected $table = 'activity_logs';

	protected $casts = [
		'subject_id' => 'int',
		'causer_id' => 'int'
	];

	protected $fillable = [
		'log_name',
		'description',
		'subject_type',
		'subject_id',
		'causer_type',
		'causer_id',
		'properties'
	];
}
