<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model {

	protected $table = 'User';
	protected $fillable = ['id', 'username', 'password', 'role_id'];
	public $timestamps = false;

	public function createUser($data) {
		$id = $this::insertGetId(['username' => $data->username, 'password' => $data->password, 'role_id' => 2]);

		return $id;
	}

	public function updateUser($data) {
		$affected = $this::where('id', '=', $data->id)
			->update(['username' => $data->username, 'password' => $data->password]);
		return $affected;
	}

	public function getAll() {
		try
		{
			$rows = $this::select('User.id', 'User.username', 'User.password', 'Role.role_name')->join('Role', 'User.role_id', '=', 'Role.id')
				->where('User.role_id', '=', 2)
				->get();
			return $rows;
		} catch (Exception $e) {
			echo $e;
			return [];
		}
	}

	public function users() {
		return $this::select('id', 'role_id')->get();
	}
}
