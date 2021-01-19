<?php

namespace application;

class Config
{
    /**
     * @var object
     */
    protected $conf;

    /**
     * @var self
     */
    protected static $inst;

    /**
     * Config constructor.
     */
    private function __construct()
    {
        $conf = parse_ini_file(CONF_PATH . "/conf_" . APP_ENV . ".ini", true);
        $conf = array_map(function ($section) {
            return (object)$section;
        }, $conf);

        $this->conf = (object)$conf;
    }

    /**
     * @return Config
     */
    public static function getInstance()
    {
        if (!isset(self::$inst)) {
            self::$inst = new self();
        }

        return self::$inst;
    }

    /**
     * @return object
     */
    public function getConfig()
    {
        return $this->conf;
    }
}
