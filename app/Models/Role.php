<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {

	protected $table = 'Role';
	protected $fillable = ['id', 'role_name'];
	public $timestamps = false;

}
