<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BackupController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Returns the list of backups
     *
     * @return JSON encoded array
     */
    public function index()
    {
    	$array = [];
        $directory = public_path('sqlite/backup/');;
		$files = array_diff(scandir($directory), array('..', '.','.DS_Store','.gitkeep'));

        arsort($files);

		foreach ($files as $key => $file) {
			$array[] = [
				'name' => $file,
				'path' => asset("sqlite/backup/$file")
			];	
		}

        return json_encode($array);
    }
}
