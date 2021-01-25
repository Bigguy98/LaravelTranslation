<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SqliteLang extends Model
{    
	// public function getUserByUsername() {
  //   $this.
  // }
  protected $connection = 'sqlite';

  public function getTableNames() {
    $names = DB::connection('sqlite')->select("SELECT name FROM sqlite_master WHERE type='table' AND name <> 'sqlite_stat3' AND name <> 'sqlite_stat1'");
    return $names;
  }

  public function createLanguage($title) 
  {
    try {
      $table = DB::connection('sqlite')->select("CREATE TABLE ".$title." AS SELECT * FROM English");
      return true;
    }catch(Exception $e) {
      echo $e;
      return NULL;
    }
  }

  public function getTranslation($data) 
  {
    $translates = [];
    $tables = $data->permission;
    $permission = [];
    foreach($tables as $language) 
    {
      if($language->view && $language->lang_title != $data->lang) 
      {
        array_push($permission, (object)array('language' => $language->lang_title));
      }
    }

    foreach($permission as $language) 
    {
      $title = $language->language;
      $query = "SELECT `".$data->col."` FROM ".$title." WHERE id=".$data->id.";";
      $row = DB::connection('sqlite')->select($query);
      $row1 = (array) $row[0];
      $obj = (object)[];
      if(count($row1) == 0) $obj[$title] = array('translate' => 'n/a');
      else $obj->$title = array('translate' => $row1[$data->col]);

      array_push($translates, $obj);
    }

    return $translates;
  }

  public function getClearData($langTitle) 
  {
    $data = [];
    for($i=0;$i<count($langTitle);$i++)
    {
      $query = "SELECT '".$langTitle[$i]->lang_title."' as lang, * FROM ".$langTitle[$i]->lang_title.";";
      // echo $query;exit;
      $all = DB::connection('sqlite')->select($query);
      if(!empty($all)) {
        array_push($data, array('language'=> $all[0]->lang, 'data' => $all));
      }
    }

    return $data;
  }

  function updateDate($data)
  {
    $tmp = $data;
    $id = $data->id;
    $lang = $data->lang;
    $values = [];
    unset($tmp->id);
    unset($tmp->lang);

    foreach($tmp as $key => $value)
    {
      array_push($values, '`'.$key.'` = "'.$value.'"');
    }

    $query = "UPDATE `".$lang."` SET ".join(', ', $values)." WHERE id = $id;";
    DB::connection('sqlite')->select($query);
    return true;
  }

  function updateTranslate($data)
  {
    $row = DB::connection('sqlite')->table($data->lang)
      ->where('id', '=', $data->id)
    // var_dump($row);exit;
      ->update([
        $data->col => $data->value
      ]);
    return true;
  }

  public function getData($tables) 
  {
    $tmp = [];
    for($i=0;$i<count($tables);$i++)
    {
      $name = $tables[$i]->name;
      $query = "SELECT $name as lang, * FROM $name";
      $all = DB::connection('sqlite')->select($query);
      array_push($tmp, array('language' => $all[0]->lang, 'data' => $all));
    }

    return $tmp;
  }

  public function getDataByUser($langTitle)
  {
    $tables = $this->getClearData($langTitle);
    if(!empty($tables))
    {
      return $tables;
    }

    return NULL;
  }


}
