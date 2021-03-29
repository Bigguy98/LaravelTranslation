<?php

namespace App\Console\Commands;

use DB;
use App\SqliteLang;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;


class RemoveTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tags:remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Makes a sync of our SQLite database';

    /**
     * Excluded from cleaning keys.
     *
     * @var array
     */
    private static $exclude = ['dlg_hlp_text'];

    /**
     *
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        $keys = DB::connection('sqlite')->table('English')->get();
        $tables = SqliteLang::getTableNames();
        foreach ($tables as $table) {
            foreach ($keys as $key) {
                if(!in_array($key->key, self::$exclude)){
                    $value = DB::connection('sqlite')->table($table->name)->where('key',$key->key)->first();
                    $translation = strip_tags($value->translation);
                    $translation = str_replace('&lt;br&gt;', '', $translation);
                    DB::connection('sqlite')->table($table->name)->where('key',$key->key)->update(['translation' => $translation]);
                }   
            }  
        }
    }
}
