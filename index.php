<?php

require_once 'vendor/autoload.php';

use Controllers\MigrationController;
use Helpers\Logger;

class index
{
    public function __construct()
    {
        $migrationController = new MigrationController();
        $migrationController->migrate();
    }
}

new index();