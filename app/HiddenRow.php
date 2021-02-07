<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HiddenRow extends Model
{
	protected $table = 'HiddenRows';
	protected $fillable = ['id', 'key'];
	public $timestamps = false;
}
