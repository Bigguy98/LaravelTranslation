<?php

namespace App\Console\Commands;

use DB;
use App\Models\SqliteLang;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;


class SyncDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Makes a sync of our SQLite database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $keys = DB::connection('sqlite')->table('English')->get();
        $tables = SqliteLang::getTableNames();
        foreach ($tables as $table) {
            foreach ($keys as $key) {
                $current = DB::connection('sqlite')->table($table->name)->where('key',$key->key)->first();
                if(empty($current)){
                    DB::connection('sqlite')->table($table->name)->insert(
                        ['key' => $key->key, 'translation' => '']
                    );
                }  
            }  
        }
    }
}
