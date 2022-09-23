<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;


class DumpDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Makes a dump of our SQLite database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $time = date('YmdHis', time());
        $path = database_path('sqlite/language.sqlite');

        $parts = explode('/',$path);
        $name = end($parts);
        $name_parts = explode('.', $name);
        $extension = end($name_parts);
        array_pop($name_parts);
        $filename = implode('.', $name_parts);

        $file = file_get_contents($path); 
        $path = public_path('sqlite/backup/'.$filename.$time.'.'.$extension);  
        file_put_contents($path, $file);

    }
}
