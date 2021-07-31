<?php
namespace App\Models;

use Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SqliteLang extends Model {

	protected $connection = 'sqlite';
    private static $exclude = ['dlg_hlp_text'];

	public static function getTableNames() {
		$names = DB::connection('sqlite')->select("SELECT name FROM sqlite_master WHERE type='table' AND name <> 'sqlite_stat3' AND name <> 'sqlite_stat1'");
		return $names;
	}

	public static function createLanguage($title) {
		try {
			DB::connection('sqlite')->select("CREATE TABLE `". $title . "` (`id` INTEGER, `key` TEXT UNIQUE, `translation` TEXT, PRIMARY KEY(id))");
			DB::connection('sqlite')->select("INSERT INTO `". $title . "` SELECT * FROM English");
			return true;
		} catch (Exception $e) {
			echo $e;
			return NULL;
		}
	}

	public static function getTranslation($data) {
		$translates = [];
		$permission = self::getTableNames();
		foreach ($permission as $language) {
			$title = $language->name;
			$query = "SELECT `" . $data->col . "` FROM " . $title . " WHERE `key`='" . $data->key . "';";
			$row = DB::connection('sqlite')->select($query);
			$row1 = (array) $row[0];
			$obj = (object) [];
			if (count($row1) == 0) {
				$obj[$title] = array(
					'translate' => 'n/a',
				);
			} else {
				$obj->$title = array(
					'translate' => $row1[$data->col],
				);
			}

			array_push($translates, $obj);
		}
		return $translates;
	}

	public static function updateTranslation($data) {
		$language = trim(str_replace('@iqualif.com', '', Auth::user()->email));
	
		if(in_array($data->key, self::$exclude)){
			$translation = $data->translation;
		}else{
			$translation = strip_tags(clean($data->translation));
		}

		$row = DB::connection('sqlite')->table($language)
			->where('key', '=', $data->key)
			->update(['translation' => $translation]);
		return true;
	}

	public static function updateTranslationAdmin($data) {
		if(in_array($data->key, self::$exclude)){
			$translation = $data->value;
		}else{
			$translation = strip_tags(clean($data->value));
		}

		$row = DB::connection('sqlite')->table($data->lang)
			->where('key', '=', $data->key)
			->update(['translation' => $translation]);
		return true;
	}

	public static function getDataByUser($langTitle,$hiddenRows,$mode) {
		$data = [];
		$extension = '';
		if($mode == 'user'){
			if(!empty($hiddenRows)){
				$extension = "WHERE `key` NOT IN (";
				foreach ($hiddenRows as $item) {
					$extension .= "'" . $item . "',";
				}
				$extension .= ")";
				$extension = str_replace("',)", "')", $extension);
			}
		}
		
		for ($k = 0; $k < count($langTitle); $k++) {
			$query = "SELECT '" . $langTitle[$k] . "' as lang, * FROM " . $langTitle[$k] . " " .$extension. " ;";
			$all = DB::connection('sqlite')->select($query);

			$total = 0;
			$visible = 0;

			if (!empty($all)) {
			
				for ($i=0; $i < count($all); $i++) { 
					if(in_array($all[$i]->key, $hiddenRows)){
						$all[$i]->visible = 0;
					}else{
						$all[$i]->visible = 1;
						$visible = $visible + str_word_count($all[$i]->translation);
					}
					$total = $total + str_word_count($all[$i]->translation);
				}

				if($mode == 'user'){
					$languages = [];

					foreach ($all as $key => $value) {
						$languages[$value->key] = [
							'translation' => $value->translation,
							'visible' => $value->visible
						];
					}

					if(!isset($data[$langTitle[$k]])){
						$data[$langTitle[$k]] = $languages;
					}
				} else {
					array_push($data, array(
						'language' => $all[0]->lang,
						'total' => $total,
						'visible' => $visible,
						'data' => $all,
					));

				}
			}
		}
		return $data;
	}

	public static function dropTable($table){
		Schema::connection('sqlite')->dropIfExists($table);
	}

	public static function renameTable($old_table, $new_table){
		Schema::connection('sqlite')->rename($old_table, $new_table);
	}

	public static function insertKey($table,$key){
		$current = DB::connection('sqlite')->table($table)->where('key',$key)->first();
		if(empty($current)){
			DB::connection('sqlite')->table($table)->insert(
			    ['key' => $key, 'translation' => '']
			);
		}                                           
	}

}
