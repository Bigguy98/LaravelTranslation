<?php
namespace App;

use App\SqliteLang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Language extends Model {

	protected $table = 'Languages';
	protected $fillable = ['id', 'lang_title'];
	public $timestamps = false;

	public function getLanguage($user) {
		$langId = array();

		foreach ($user['permission'] as $key => $permission) {
			if ($permission->view) {
				array_push($langId, $permission->language_id);
			}
		}

		if (count($langId) > 0) {
			return $this->language($langId);
		} else {
			return false;
		}
	}

	private function language($langId) {
		sort($langId);
		return $this::select('lang_title')->whereIn('id', $langId)->get();
	}

	public function addLang($title) {
		$obj = array(
			'table' => 'Languages',
			'title' => $title,
		);
		$id = $this->addLanguage($obj);
		$sqlite = new SqliteLang();
		if ($id) {
			$resLite = $sqlite->createLanguage($title);
			if ($resLite) {
				return $id;
			}

		}
		return NULL;
	}

	public function addLanguage($data) {
		$id = $this::insertGetId(['lang_title' => $data['title']]);
		return $id;
	}

	public function initDrop() {
		$langIds = $this::select('id')->orderBy('id', 'ASC')
			->get();

		if (!empty($langIds)) {
			$arr = [];
			foreach ($langIds as $id) {
				array_push($arr, $id);
			}
			DB::delete('DELETE FROM Languages WHERE id IN ?', $arr);
			return true;
		}

		return false;
	}

	public function initLang($langs) {
		$insertIds = [];
		foreach ($langs as $language) {
			$id = $this::insertGetId(['lang_title' => $language->name]);
			array_push($insertIds, $id);
		}

		return $insertIds;
	}
}