<?php

namespace config;

use Helpers\Logger;
use PDO;
use PDOException;

class Database
{
    private $db;
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger('database.log');

        $host = '127.0.0.1:8889';
        $db = 'college';
        $user = 'root';
        $pass = 'root';

        try {
            $db = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->logger->info("Successfully connected to db");
        } catch (PDOException $e) {
            $this->logger->warning("Could not connect to the database $db :" . $e->getMessage());
            die("Could not connect to the database $db :" . $e->getMessage());
        }
    }

    public function execSql($sql)
    {
        $result = $this->db->query($sql);

    }
}
