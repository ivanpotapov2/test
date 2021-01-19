<?php

namespace application;

/**
 * Class Logger
 * @package application
 */
class Logger
{
    const LEVEL_DEBUG = "DEBUG";
    const LEVEL_INFO = "INFO";
    const LEVEL_WARN = "WARN";
    const LEVEL_ERROR = "ERROR";

    /**
     * @var self
     */
    private static $inst;
    /**
     * @var string
     */
    private $logFile;

    /**
     * Logger constructor.
     */
    private function __construct()
    {
        $this->logFile = LOG_PATH . "/log.txt";
    }

    /**
     * @return Logger
     */
    public static function getInstance()
    {
        if (!isset(self::$inst)) {
            self::$inst = new self();
        }

        return self::$inst;
    }

    /**
     * @param string $message
     */
    public function debug($message)
    {
        $conf = Config::getInstance()->getConfig();
        if ($conf->logger->log_level === "debug") {
            $this->writeMessage($message, self::LEVEL_DEBUG);
        }
    }

    /**
     * @param string $message
     */
    public function info($message)
    {
        $this->writeMessage($message, self::LEVEL_INFO);
    }

    /**
     * @param string $message
     */
    public function warn($message)
    {
        $this->writeMessage($message, self::LEVEL_WARN);
    }

    /**
     * @param string $message
     * @param string $logLevel
     */
    private function writeMessage($message, $logLevel = self::LEVEL_INFO)
    {
        //log rotation
        if (file_exists($this->logFile)) {
            //3 Mb
            if (filesize($this->logFile) >= 3 * 1048576) {
                rename($this->logFile, LOG_PATH . '/old_log.txt');
            }
        }

        $currDate = date("d-m-Y h:i:s");
        $message = "[$logLevel]" . "[" . $currDate . "]: " . $message . PHP_EOL;
        file_put_contents($this->logFile, $message, FILE_APPEND);
    }
}
