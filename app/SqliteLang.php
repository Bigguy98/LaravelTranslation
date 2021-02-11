<?php
namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SqliteLang extends Model {

	protected $connection = 'sqlite';

	public function getTableNames() {
		$names = DB::connection('sqlite')->select("SELECT name FROM sqlite_master WHERE type='table' AND name <> 'sqlite_stat3' AND name <> 'sqlite_stat1'");
		return $names;
	}

	public function createLanguage($title) {
		try
		{
			$table = DB::connection('sqlite')->select("CREATE TABLE " . $title . " AS SELECT * FROM English");
			return true;
		} catch (Exception $e) {
			echo $e;
			return NULL;
		}
	}

	public function getTranslation($data, $user_id) {
		$translates = [];
		$tables = $data->permission;
		$permission = [];

		foreach ($tables as $language) {
			if ($user_id != 1) {
				if ($language->view == 1 && $language->lang_title != $data->lang) {
					array_push($permission, (object) array(
						'language' => $language->lang_title,
					));
				} else {
					if ($language->view == 1 && $language->edit == 1 && $language->lang_title != $data->lang) {
						array_push($permission, (object) array(
							'language' => $language->lang_title,
						));
					} else if ($language->view == 1 && $language->edit == 1 && 'English' == $data->lang) {
						array_push($permission, (object) array(
							'language' => $language->lang_title,
						));
					}
				}
			}
			if ($user_id == 1) {
				if ($language->lang_title != $data->lang) {
					array_push($permission, (object) array(
						'language' => $language->lang_title,
					));
				}
			}
		}

		foreach ($permission as $language) {
			$title = $language->language;
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

	public function updateDate($data) {
		$tmp = $data;
		$id = $data->id;
		$lang = $data->lang;
		$values = [];
		unset($tmp->id);
		unset($tmp->lang);

		foreach ($tmp as $key => $value) {
			array_push($values, '`' . $key . '` = "' . $value . '"');
		}

		$query = "UPDATE `" . $lang . "` SET " . join(', ', $values) . " WHERE id = $id;";
		DB::connection('sqlite')->select($query);
		return true;
	}

	public function updateTranslation($data) {
		$language = trim(str_replace('@iqualif.com', '', Auth::user()->email));

		$row = DB::connection('sqlite')->table($language)
			->where('key', '=', $data->key)
			->update(['translation' => $data->translation]);
		return true;
	}

	public function getData($tables) {
		$tmp = [];
		for ($i = 0; $i < count($tables); $i++) {
			$name = $tables[$i]->name;
			$query = "SELECT $name as lang, * FROM $name";
			$all = DB::connection('sqlite')->select($query);
			array_push($tmp, array(
				'language' => $all[0]->lang,
				'data' => $all,
			));
		}

		return $tmp;
	}

	public function getDataByUser($langTitle,$hiddenRows,$user_id) {
		$data = [];
		$extension = '';
		if ($user_id != 1) {
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
			}
		}

		return $data;
	}

}
