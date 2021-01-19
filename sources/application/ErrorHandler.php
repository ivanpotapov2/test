<?php

namespace application;
include('Logger.php');
/**
 * Class ErrorHandler
 * @package billing\engine\base
 */
class ErrorHandler
{
    /** @var Logger */
    private $logger;

    public function __construct()
    {
        $this->logger = Logger::getInstance();
    }

    /**
     * инициализирует обработчик ошибок в проекте
     */
    public function init()
    {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'onShutdown']);
    }

    /**
     * Обработчик любых ошибок
     * @param $level
     * @param $message
     * @param $file
     * @param $line
     */
    public function handleError($level, $message, $file, $line)
    {
        $msg = $this->formatErrorMessage($level, $file, $line, $message);
        $this->logger->warn($msg);
    }

    /**
     * обработчик пропущенных исключений
     * @param \Throwable $ex
     */
    public function handleException(\Throwable $ex)
    {
        $msg = 'Exception: ' . PHP_EOL . $ex->getMessage() . PHP_EOL .
            'File: ' . $ex->getFile() . ':' . $ex->getLine() . PHP_EOL .
            'Stacktrace: ' . $ex->getTraceAsString();

        $this->logger->warn($msg);
    }

    /**
     * обработчик фатальных ошибок
     * обрабатывает последнюю ошибку перед завершением скрипта
     */
    public function onShutdown()
    {
        $error = error_get_last();

        //нам нужны только фатальные ошибки, остальные уже обработаны в handleError
        if (!is_array($error) || !in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            return;
        }

        $message = $this->formatErrorMessage($error['type'], $error['file'], $error['line'], $error['message']);
        $this->logger->warn($message);
    }

    /**
     * @param int $errno
     * @return string
     */
    private function getErrorTypeName($errno)
    {
        $types = [
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_STRICT => 'E_STRICT',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED '
        ];

        return isset($types[$errno]) ? $types[$errno] : 'Some error';
    }

    /**
     * @param int $errno
     * @param string $errorFile
     * @param int $line
     * @param string $message
     */
    private function formatErrorMessage($errno, $errorFile, $line, $message)
    {
        $type = $this->getErrorTypeName($errno);
        return $type . PHP_EOL .
            'Файл: ' . $errorFile . ':' . $line . PHP_EOL . 'Текст ошибки: ' . $message;
    }
}
