<?php

namespace Controllers;

use Config\Database;

class MigrationController
{
    private $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function migrate(){
        $path = 'migrations';
        $files = scandir('migrations');
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $filePath = $path . DIRECTORY_SEPARATOR . $file;
            if (is_file($filePath)) {
                $content = file_get_contents($filePath);
                $this->database->execSql($content);
            }
        }
    }
}
