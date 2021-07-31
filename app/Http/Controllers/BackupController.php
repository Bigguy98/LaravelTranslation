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

    /**
     * Performs db commit and make merge request
     *
     * @return +/- about operation execution
        shell_exec("cd /var/www/static && sudo -u ubuntu git pull");
     */
    public function commit(){
        shell_exec("cd /var/www/static && sudo -u ubuntu git checkout master");
        shell_exec("cd /var/www/static && sudo -u ubuntu git checkout -b 'from-".date("Y-m-d")."'");
        shell_exec("sudo -u ubuntu cp /var/www/sqlite-laravel/database/sqlite/language.sqlite /var/www/static/src/main/resources/md/r/l.roa");
        shell_exec("sudo -u ubuntu sqlite3 /var/www/static/src/main/resources/md/r/l.roa \"VACUUM;\"");
        shell_exec("cd /var/www/static && sudo -u ubuntu git add .");
        shell_exec("cd /var/www/static && sudo -u ubuntu git commit -m 'language-db-update-from-".date("Y-m-d")."'");
        shell_exec("cd /var/www/static && sudo -u ubuntu git push --set-upstream origin from-".date("Y-m-d")." -o  merge_request.create -o merge_request.target=master -o merge_request.remove_source_branch -o merge_request.title=New-DB-version");
        echo 'ok';
    }
}
