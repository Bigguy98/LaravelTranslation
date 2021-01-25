<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Language;
use App\Role;
use App\Permission;
use App\SqliteLang;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    //
    public function login(Request $request) {
        $user = $this->getUserByUsername($request->username);
        if(!empty($user) && $user['password'] == $request->password) {

            $user = $this->putSessionUser($request, $user);

            return response()->json(json_encode($user));
        }

        return false;
    }

    public function languageByUser(Request $request, $userId) {
        $user = $this->getUserById($userId);
        $sqlite = new SqliteLang();
        if(!empty($user)) {
            $user = $this->putSessionUser($request, $user);
            $language = new Language();
            $result = $language->getLanguage($user);
            if(empty($result)) {
                return false;
            }

            $data = $sqlite->getDataByUser($result);
            if(empty($data)) return "Something wrong with sqlite Languages";

            return $this->makeResponse($data);
        }

        return "User Don't exist";
    }

    public function isAuthenticate(Request $request)
    {
        if($request->session()->has('currentUser')) {
            return true;
        }

        return false;
    }

    public function language(Request $request)
    {
        $title = $request->title;
        $language = new Language();
        $userPermission = new Permission();
        $id = $language->addLang($title);
        if(is_null($id)) return 'Something wrong with sqlite Languages';
        $result = $userPermission->addUserPermission($id);
        if(!$result) return 'Something wrong with sqlite createUserPermission';

        return response()->json($result);

    }

    public function languageList() {
        $sqlite = new SqliteLang();
        $data = $sqlite->getTableNames();
        if(empty($data)) return 'Something wrong with sqlite languages';
        return $this->makeResponse($data);
    }

    public function getUser() {
        $user = new User();
        $users = $user->getAll();
        if(empty($users)) return 'Something wrong with users';

        $obj = array();

        foreach($users as $user)
        {
            $permission = $this->getPermission($user->id);
            $user->permission = $permission;
            array_push($obj, $user);
        };
        return $this->makeResponse($obj);
    }

    public function addUser(Request $req) 
    {
        $username = $req->username;
        $password = $req->password;

        $res = $this->getUserByUsername($username);
        if(empty($res))
        {
            $userId = User::insertGetId([
                'username' => $username,
                'password' => $password,
                'role_id' => 2
            ]);
            
            $languages = Language::all();
            if(!empty($languages)) {
                $obj = (object)[];
                $obj->languages = $languages;
                $obj->userId = $userId;

                $perms = new Permission();
                if($perms->createUserPermission($obj)) {
                    return $this->makeResponse(['result' => true]);
                }
            }
        }

        return $this->makeErrorResponse('Something went wrong!');
    }

    public function deleteUser(Request $req)
    {
        try{
            $id = $req->userId;
            $user = User::find($id);
            $user->delete();
            return $this->makeSuccessResponse();
        }catch(Exception $e){
            return $this->makeErrorResponse($e->message);
        }
    }

    public function updateUser(Request $req)
    {
        $user = new User();
        $aff1 = $user->updateUser($req);

        $permission = new Permission();

        $aff2 = $permission->updatePermission($req);
        if($aff1 || $aff2) return $this->makeSuccessResponse();

        return $this->makeErrorResponse('Something wrong');
    }

    public function currentUser(Request $request) {
        $username = $request->username;
        if(!empty($username)) {
            $user = $this->getUserByUsername($username);
            if(!empty($user)) {
                $user = $this->putSessionUser($request, $user);

                return $this->makeResponse($user);
            }
        }

        return $this->makeErrorResponse('Error');
    }

    public function refreshDB(Request $req)
    {
        try{
            $role = $req->role;
            if($role)
            {
                $language = new Language();
                $sqlite = new SqliteLang();
                $user = new User();
                $permission = new Permission();

                if($language->initDrop())
                {
                    $langs = $sqlite->getTableNames();
                    $ids = $language->initLang($langs);
                    $users = $user->users();
                    $res = $permission->initUP($users, $ids);
                    return $this->makeSuccessResponse();
                }
            }
        }catch(Exception $e)
        {
            return $this->makeErrorResponse($e->message);
        }
        
    }


    function popOver(Request $req)
    {
        $currentUser = (object)[];
        if($req->session()->has('currentUser'))
        {
            $currentUser = $req->session()->get('currentUser');
        }
        $sqlite = new SqliteLang();
        $tables = $sqlite->getTableNames();
        $table = [];
        foreach($tables as $title)
        {
            if($req->lang != $title->name) array_push($table, $title->name);
        }

        $req->table = $table;
        if(!empty($currentUser)) $req->permission = $currentUser->permission;

        $translations = $sqlite->getTranslation($req);
        if(!empty($translations)) return $this->makeResponse($translations);
        return $this->makeErrorResponse('Someting went wrong');
    }

    function saveCollors(Request $req)
    {
        $data = (object)$req->data;
        $sqlite = new SqliteLang();
        if($sqlite->updateDate($data))
        {
            return $this->makeSuccessResponse();
        }

        return $this->makeErrorResponse('Something wrong');
    }

    function updateTranslate(Request $req)
    {
        $data = (object)$req->data;
        $sqlite = new SqliteLang();
        if($sqlite->updateTranslate($data))
        {
            return $this->makeSuccessResponse();
        }

        return $this->makeErrorResponse("Something wrong");
    }

    public function putSessionUser($request, $user) {
        unset($user['password']);
        $permissions = $this->getPermission($user->id);
        $obj = array();
        foreach($permissions as $val) {
            $obj[$val->lang_title] = array('view' => $val->view, 'edit' => $val->edit, 'id' => $val->language_id);
        }

        $user['permission'] = $permissions;
        $user['newPermission'] = $obj;

        $request->session()->put('currentUser', $user);

        return $user;
    }

    private function getUserByUsername($username) {
        $user = User::where('username', $username)->first();
        return $user;
    }

    function getUserById($id) {
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

    function makeResponse($data) {
        return response()->json(json_encode($data));
    }

    function makeErrorResponse($msg) {
        return response()->json(['error' => $msg], 500);
    }

    function makeSuccessResponse() {
        return response()->json(['result' => true], 200);
    }
}
