<?php

namespace Helpers;

class Logger
{
    private $logFile;
    private $logLevel;

    const LEVELS = ['INFO', 'WARNING', 'ERROR'];

    public function __construct($file, $level = 'INFO')
    {
        $this->logFile = "logs/" . $file;

        if (!in_array($level, self::LEVELS)) {
            throw new Exception("Invalid log level specified");
        }
        $this->logLevel = $level;

        if (!file_exists($this->logFile)) {
            file_put_contents($this->logFile, '');
        }
    }

    private function writeLog($level, $message)
    {
        $date = date('Y-m-d H:i:s');

        $logEntry = "[{$date}] [{$level}] - {$message}" . PHP_EOL;

        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
    }

    public function info($message)
    {
        if ($this->shouldLog('INFO')) {
            $this->writeLog('INFO', $message);
        }
    }

    public function warning($message)
    {
        if ($this->shouldLog('WARNING')) {
            $this->writeLog('WARNING', $message);
        }
    }

    public function error($message)
    {
        if ($this->shouldLog('ERROR')) {
            $this->writeLog('ERROR', $message);
        }
    }

    private function shouldLog($level)
    {
        return array_search($level, self::LEVELS) >= array_search($this->logLevel, self::LEVELS);
    }
}
?>