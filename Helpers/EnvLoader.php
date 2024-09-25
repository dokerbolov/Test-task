<?php

namespace Helpers;

class EnvLoader
{
    private $_ENV = [];
    public function __construct($path)
    {
        if (!file_exists($path)) {
            throw new \RuntimeException(".env file not found at: " . $path);
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            list($name, $value) = explode('=', $line, 2);
            $value = trim($value, '"\'');
            if (!array_key_exists($name, $_ENV)) {
                $_ENV[$name] = $value;
            }
            $this->_ENV[$name] = $value;
        }
    }

    public function getEnv()
    {
        return $this->_ENV;
    }
}
