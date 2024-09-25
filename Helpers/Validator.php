<?php

namespace Helpers;

use config\Database;

class Validator
{
    private $errors = [];
    private $logger;
    private $db;

    public function __construct(Logger $logger, Database $db = null)
    {
        $this->logger = $logger;
        if($db){
            $this->db = $db;
        }
    }

    public function required($param, $paramName)
    {
        if (empty($param)) {
            $this->errors['Validation'] = "$paramName is required.";
            $this->logger->error($this->errors['Validation']);
        }
    }

    public function isNumeric($param, $paramName)
    {
        if (!is_numeric($param)) {
            $this->errors['Validation'] = "$paramName must be a valid number.";
            $this->logger->error($this->errors['Validation']);
        }
    }

    public function isInRange($param, $paramName, $min, $max)
    {
        if ($param < $min || $param > $max) {
            $this->errors['Validation'] = "$paramName must be between $min and $max.";
            $this->logger->error($this->errors['Validation']);
        }
    }

    public function isEmpty($query){
        $result = $this->db->execSql($query);
        if(empty($result[0]['id'])){
            $this->errors['Emptiness'] = "Empty query";
            $this->logger->error($this->errors['Emptiness']);
        };
        return empty($result[0]['id']) ? false : true;
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function validate()
    {
        if ($this->hasErrors()) {
            $response = new Responses();
            $response->error($this->getErrors()['Validation'], 400);
        }
    }
}
