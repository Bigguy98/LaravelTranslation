<?php
namespace App\Models;

use App\Models\SqliteLang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Language extends Model {

	protected $table = 'Languages';
	protected $fillable = ['id', 'lang_title'];
	public $timestamps = false;

	public function addLang($title) {
		$obj = array(
			'table' => 'Languages',
			'title' => $title,
		);
		$id = $this::insertGetId(['lang_title' => $title]);
		if ($id) {
			$resLite = SqliteLang::createLanguage($title);
			if ($resLite) {
				return $id;
			}

		}
		return NULL;
	}

	public function initLang($langs) {
		$insertIds = [];
		foreach ($langs as $language) {
			$id = $this::insertGetId(['lang_title' => $language->name]);
			array_push($insertIds, $id);
		}

		return $insertIds;
	}

	public static function getLanguageId($title){
		$record = Language::select('id')->where('lang_title', $title)->first();
		return isset($record->id) ? $record->id : null;
	}
}
