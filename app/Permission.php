<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Permission extends Model
{

  protected $table = 'UserPermission';
  protected $fillable = ['id', 'user_id','language_id','view', 'edit'];
  public $timestamps = false;

  public function addUserPermission($language_id) 
  {
    $users = DB::table('User')->select('id')
              ->where('role_id', '=', 2)
              ->orderBy('id', 'ASC')
              ->get();
    foreach($users as $user)
    {
      $this::insert([
        'user_id' => $user->id,
        'language_id' => $language_id
      ]);
    }

    return 1;
  }

  public function createUserPermission($obj)
  {
    foreach($obj->languages as $language)
    {
      $this::insert([
        'user_id' => $obj->userId,
        'language_id' => $language->id
      ]);
    }

    return 1;
  }

  public function updatePermission($data)
  {
    try{
      // $permission = $data->permission;
      foreach($data->permission as $permission)
      {
          $this::where('language_id', '=', $permission['language_id'])
                ->where('user_id', '=', $data->id)
                ->update([
                  'edit' => $permission['edit'],
                  'view' => $permission['view']
                ]);
      }
      return true;
    }catch(Exception $e) {
      echo $e;
      return false;
    }
  }

  function initUp($users, $ids)
  {
    foreach($users as $user)
    {
      $edit = $view = $user->role_id == 2 ? 0:1;
      foreach($ids as $id)
      {
        $this::insert([
          'user_id'=>$user->id,
          'language_id'=>$id,
          'view'=>$view,
          'edit'=>$edit
        ]);
      }
    }

    return true;
  }
}
