<?php

namespace Bsexception\Dev\src\exception;

class BsException
{
    private $error_file_name = '';
    private $fatal_file_name = '';
    private $log_path = '';
    private $init_error_reporting_level = '';

    public function __construct()
    {
        $this->init_config();

        if (1 == $this->init_error_reporting_level) {
            $this->init_error_reporting_level();
        }

        # 不造成中断的 error | warning
        set_error_handler(array($this, 'error_handler'));

        # 造成中断的 fatal error
        register_shutdown_function(array($this, 'fatal_handler'));
    }

    public function init_config()
    {
        defined('APP_ENV') || define('APP_ENV', 'prod');

        defined('ROOT_PATH') || define('ROOT_PATH', dirname(__FILE__) . '/../');

        defined('REQUEST_UNIQUE_ID') || define('REQUEST_UNIQUE_ID', uniqid());

        $config = include_once ROOT_PATH . 'config/bs_exc_config.php';

        foreach ($config as $index => $item) {
            $this->$index = $item;
        }
    }

    public function init_error_reporting_level()
    {
        switch (APP_ENV) {
            case 'dev':
            case 'pre':
                error_reporting(-1);
                ini_set('display_errors', 1);
                break;
            case 'prod':
                ini_set('display_errors', 0);
                if (version_compare(PHP_VERSION, '5.3', '>=')) {
                    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
                } else {
                    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
                }
                break;

            default:
                header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
                echo '项目环境定义错误';
                exit(1);
        }
    }


    public function error_handler($severity, $message, $filepath, $line, $type = 'error')
    {
        if (!$severity) {
            return;
        }

        $this->log_message($type, 'Severity: ' . $severity . '  --> ' . $message . ' ' . $filepath . ' ' . $line);
    }

    public function fatal_handler()
    {
        $error = error_get_last();

        $errno = $error['type'];
        $err_file = $error['file'];
        $err_line = $error['line'];
        $err_str = $error['message'];

        $this->error_handler($errno, $err_str, $err_file, $err_line, 'fatal');
    }

    public function log_message($type, $message)
    {
        $dir = $this->error_file_name;

        if ('fatal' == $type) {
            $dir = $this->fatal_file_name;
        }

        $this->inert_log($message, $dir);
    }

    public function inert_log($info, $type = '', $timeType = 'day')
    {
        $basePath = ROOT_PATH. $this->log_path;

        if ($type) {
            $basePath .= $type . DIRECTORY_SEPARATOR;
        }

        $txt = '[' . date('H:i:s') . '] ' . $info . "\n";
        $txt = 'RUID:[' . @REQUEST_UNIQUE_ID . ']|' . $txt;

        if ('day' == $timeType) {
            $date = date('Ymd');
        } else {
            $basePath = $basePath . date('Ymd') . '/';
            $date = date('H');
        }

        if (!is_dir($basePath)) {
            mkdir($basePath, 0777, true);
            chmod($basePath, 0777);
        }

        file_put_contents($basePath . $date . '.log', $txt, FILE_APPEND);
    }
}





