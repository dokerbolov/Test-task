<?php

namespace Controllers;

use Config\Database;

class MigrationController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @throws \Exception
     */
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
                if($content != ''){
                    $this->db->execSql($content);
                }
            }
        }
    }
}
