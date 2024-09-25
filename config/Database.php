<?php

namespace config;

use Helpers\Logger;
use Helpers\EnvLoader;

class Database {
    private $base;
    private $logger;
    private $_env;

    /**
     * @throws \Exception
     */
    public function __construct() {
        $this->logger = new Logger('database.log');
        $env = new EnvLoader('.env');
        $this->_env = $env->getEnv();
        $this->connect();
    }

    /**
     * @throws \Exception
     */
    private function connect() {
        try {
            $dsn = "mysql:host=" . $this->_env['MYSQL_HOST'] . ":" . $this->_env['MYSQL_PORT'] . ";dbname=" . $this->_env['MYSQL_DBNAME'] . ";charset=utf8";
            $username = $this->_env['MYSQL_USERNAME'];
            $password = $this->_env['MYSQL_PASS'];
            $this->base = new \PDO($dsn, $username, $password);
            $this->base->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->logger->info("Database connection established.");
        } catch (\PDOException $exception) {
            $this->logger->error("Database connection failed: " . $exception->getMessage());
            throw new \Exception("Database connection error.");
        }
    }

    /**
     * @throws \Exception
     */
    public function execSql($sql)
    {
        try {
            if ($this->base === null) {
                throw new \Exception("No database connection.");
            }
            $stmt = $this->base->query($sql);
            $this->logger->info("Successfully executed SQL query. SQL: $sql");
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\Exception $exception) {
            $this->logger->warning("Could not execute the query: " . $exception->getMessage());
            throw $exception;
        }
    }

}

