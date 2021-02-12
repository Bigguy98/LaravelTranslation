<?php

namespace App\Http\Controllers;

use Hash;
use App\Language;
use App\Permission;
use App\Role;
use App\SqliteLang;
use App\User;
use App\HiddenRow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller {

	public function adminLogin(Request $request) {
		$user = $this->getUserByname($request->name);
		if (!empty($user) && $user->password == $request->password) {

			$user = $this->putSessionUser($request, $user);

			return response()->json(json_encode($user));
		}

		return $this->makeResponse("");
	}

	public function languageByUser(Request $request, $userId) {
		$user = $this->getUserById($userId);
		$sqlite = new SqliteLang();
		if (!empty($user)) {
			$user = $this->putSessionUser($request, $user);
			$language = new Language();

			$result = $language->getLanguage($user);

			if (empty($result)) {
				return $this->makeResponse("");
			}

			$hiddenRows = json_decode($this->listOfHiddenRows());

			$data = $sqlite->getDataByUser($result,$hiddenRows,$user->id);
			if (empty($data)) {
				return $this->makeErrorResponse('Something went wrong with sqlite languages');
			}

			return $this->makeResponse($data);
		}

		return $this->makeErrorResponse("User does not exists");
	}

	public function listOfHiddenRows(){
		$hiddenRows = [];

		$rows = HiddenRow::get(['key']);
		   $keys = collect($rows)->map(function ($item, $key) {
                return $item->key;
        })->toArray();

		foreach ($rows as $key => $item) {
			$hiddenRows[] = $item->key;
		}   

		return json_encode($hiddenRows);
	}

	public function isAuthenticate(Request $request) {
		if ($request->session()->has('currentUser')) {
			return $this->makeSuccessResponse();
		}

		return $this->makeErrorResponse('No authenticated');
	}

	public function language(Request $request) {
		$title = $request->title;
		$language = new Language();
		$userPermission = new Permission();
		$id = $language->addLang($title);
		if (is_null($id)) {
			return 'Something went wrong with sqlite languages';
		}

		$result = $userPermission->addUserPermission($id);
		if (!$result) {
			return 'Something went wrong with sqlite createUserPermission';
		}

		return response()->json($result);

	}

	public function languageList() {
		$sqlite = new SqliteLang();
		$data = $sqlite->getTableNames();
		if (empty($data)) {
			return 'Something went wrong with sqlite languages';
		}
		return $this->makeResponse($data);
	}

	public function getUser() {
		$users = User::get();
		if (empty($users)) {
			return 'Something went wrong with users';
		}
		return $this->makeResponse($users);
	}

	public function addUser(Request $request) {

		$user = User::create([
		    'name' => $request->name,
			'password' => Hash::make($request->password),
			'email' => $request->email
		]);

		if ($user->save()) {
			return $this->makeResponse(['result' => true]);
		}

		return $this->makeErrorResponse('Something went wrong with user create');
	}

	public function deleteUser(Request $req) {
		try {
			$id = $req->userId;
			$user = User::find($id);
			$user->delete();
			return $this->makeSuccessResponse();
		} catch (Exception $e) {
			return $this->makeErrorResponse($e->message);
		}
	}

	public function updateUser(Request $request) {
		$user = User::find($request->id);
		$user->name = $request->name;
		$user->email = $request->email;
		if ($user->save()) {
			return $this->makeSuccessResponse();
		}
		return $this->makeErrorResponse('Something went wrong with user update');
	}

	public function currentUser(Request $request) {
		$name = $request->name;
		if (!empty($name)) {
			$user = $this->getUserByname($name);
			if (!empty($user)) {
				$user = $this->putSessionUser($request, $user);

				return $this->makeResponse($user);
			}
		}

		return $this->makeErrorResponse('Failed to retrieve current user');
	}

	public function refreshDB(Request $req) {
		try {
			$role = $req->role;
			if ($role) {
				$language = new Language();
				$sqlite = new SqliteLang();
				$user = new User();
				$permission = new Permission();

				if ($language->initDrop()) {
					$langs = $sqlite->getTableNames();
					$ids = $language->initLang($langs);
					$users = $user->users();
					$res = $permission->initUP($users, $ids);
					return $this->makeSuccessResponse();
				}
			}
		} catch (Exception $e) {
			return $this->makeErrorResponse($e->message);
		}

	}

	public function popOver(Request $req) {

		$currentUser = (object) [];
		if ($req->session()->has('currentUser')) {
			$currentUser = $req->session()->get('currentUser');
		}
		$sqlite = new SqliteLang();
		$tables = $sqlite->getTableNames();
		$table = [];
		foreach ($tables as $title) {
			if ($req->lang != $title->name) {
				array_push($table, $title->name);
			}
		}

		$req->table = $table;
		if (!empty($currentUser)) {
			$req->permission = $currentUser->permission;
		}

		$translations = $sqlite->getTranslation($req, $currentUser->role_id);

		if (!empty($translations)) {
			return $this->makeResponse($translations);
		}

		return $this->makeErrorResponse('Someting went wrong with English version retrieving');
	}

	public function updateTranslate(Request $req) {
		$data = (object) $req->data;
		$sqlite = new SqliteLang();
		if ($sqlite->updateTranslate($data)) {
			return $this->makeSuccessResponse();
		}

		return $this->makeErrorResponse("Something wrong");
	}

	public function putSessionUser($request, $user) {
		unset($user->password);
		$permissions = $this->getPermission($user->id);
		$obj = array();
		foreach ($permissions as $val) {
			$obj[$val->lang_title] = array('view' => $val->view, 'edit' => $val->edit, 'id' => $val->language_id);
		}

		$user->permission = $permissions;
		$user->newPermission = $obj;

		$request->session()->put('currentUser', $user);

		return $user;
	}

	private function getUserByname($name) {
		$user = DB::table('User')->where('name', $name)->first();
		return $user;
	}

	private function getUserById($id) {
		return User::find($id);
	}

	public function getPermission($uid) {
		return DB::table('UserPermission')
			->select('UserPermission.language_id', 'Languages.lang_title', 'UserPermission.edit', 'UserPermission.view')
			->join('Languages', 'Languages.id', '=', 'UserPermission.language_id')
			->where('UserPermission.user_id', '=', $uid)
			->orderBy('UserPermission.language_id')
			->get();
	}

	public function hideRow(Request $request) {
		HiddenRow::firstOrCreate(['key' => $request->key]);
		return $this->makeSuccessResponse();
	}

	public function showRow(Request $request) {
		HiddenRow::where('key', $request->key)->delete();
		return $this->makeSuccessResponse();
	}

	private function makeResponse($data) {
		return response()->json(json_encode($data));
	}

	private function makeErrorResponse($msg) {
		return response()->json(['error' => $msg], 500);
	}

	private function makeSuccessResponse() {
		return response()->json(['result' => true], 200);
	}
}
