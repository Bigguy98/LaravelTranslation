<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SqliteLang;
use App\HiddenRow;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $sqlite = new SqliteLang();

        $language = trim(str_replace('@iqualif.com', '', $user->email));
        $hiddenRows = $this->listOfHiddenRows();    
        $languages = ['English', $language];
        $data = $sqlite->getDataByUser($languages,$hiddenRows,$user->id);

        return view('home', ['data'=>$data,'language'=>$language]);
    }

    private function listOfHiddenRows(){
        $hiddenRows = [];

        $rows = HiddenRow::get(['key']);
           $keys = collect($rows)->map(function ($item, $key) {
                return $item->key;
        })->toArray();

        foreach ($rows as $key => $item) {
            $hiddenRows[] = $item->key;
        }   

        return $hiddenRows;
    }

    public function updateTranslation(Request $request) {

        $sqlite = new SqliteLang();
        if ($sqlite->updateTranslation($request)) {
            return '';
        }

        return $this->makeErrorResponse("Something wrong");
    }
}
