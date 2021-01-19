<?php

namespace application;
include('ErrorHandler.php');
defined("APP_PATH") || define("APP_PATH", realpath(dirname(__FILE__)));
defined("ROOT_PATH") || define("ROOT_PATH", APP_PATH . "/..");
defined("CONF_PATH") || define("CONF_PATH", ROOT_PATH . "/config");
defined("LOG_PATH") || define("LOG_PATH", ROOT_PATH . "/logs");
defined("APP_ENV") || define("APP_ENV", getenv("APP_ENV") ? : "dev");
include('Loader.php');
/**
 * Class Core
 * @package application
 */
 class Core
{
    /**
     * @var Loader
     */
    private $loader;

    /**
     * Core constructor.
     */
    public function __construct()
    {
        $errorHandler = new ErrorHandler();
        $errorHandler->init();

        Logger::getInstance()->info("Core instance created");
    }

    /**
     * @return Loader
     */
    public function getLoader()
    {
        Logger::getInstance()->info("Core::getLoader");
        if (!isset($this->loader)) {
            $this->createLoader();
        }

        return $this->loader;
    }

    private function createLoader()
    {
        Logger::getInstance()->info("Core::createLoader");
        $this->loader = new Loader();
    }
}
