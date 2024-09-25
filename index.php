<?php

require_once 'vendor/autoload.php';

use Routes\Api;
use Config\Database;
use Controllers\MigrationController;
use Controllers\TeacherController;
use Controllers\StudentController;

class Index
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        $database = new Database();

        $migrationController = new MigrationController($database);
        $migrationController->migrate();
//
//        $teacherController = new TeacherController($database);
//        $studentController = new StudentController($database);
//
//        $api = new Api($teacherController, $studentController);
    }
}

new Index();
