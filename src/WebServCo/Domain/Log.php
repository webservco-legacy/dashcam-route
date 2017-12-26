<?php
namespace WebServCo\Domain;

use WebServCo\Settings\Config;

class Log
{

    private static $_msLogTimeStart;

    private static $_msLogTimeFinish;

    private static $_msLogFile;

    private static $_msLogData = [];

    private static function _init()
    {
        if (self::$_msLogFile) {
            return FALSE;
        }
        self::$_msLogTimeStart = \DateTime::createFromFormat('U.u', microtime(true))->setTimezone(new \DateTimeZone('Europe/Budapest'));
        self::$_msLogFile = Config::LOG_DIR . self::$_msLogTimeStart->format("Ymd-His-u") . '.log';
        self::addMessage(self::$_msLogTimeStart->format("Y-m-d H:i:s.u"));
        
        return TRUE;
    }

    public static function addMessage($message)
    {
        self::_init();
        self::$_msLogData[] = $message;
        file_put_contents(self::$_msLogFile, $message . PHP_EOL, FILE_APPEND);
        if (Config::LOG_OUTPUT) {
            echo $message . PHP_EOL;
        }
        return TRUE;
    }

    public static function finish($result = TRUE, $message = NULL)
    {
        self::_init();
        if (! empty($message)) {
            self::addMessage($message);
        }
        self::addMessage("Result: " . var_export($result, TRUE));
        self::addMessage("Memory Peak Usage: " . (memory_get_peak_usage(TRUE) / 1024) . ' K');
        self::$_msLogTimeFinish = \DateTime::createFromFormat('U.u', microtime(true))->setTimezone(new \DateTimeZone('Europe/Budapest'));
        $time = self::$_msLogTimeFinish->format("U.u") - self::$_msLogTimeStart->format("U.u");
        self::addMessage(self::$_msLogTimeFinish->format("Y-m-d H:i:s.u"));
        self::addMessage("Running time: {$time} seconds");
        return $result;
    }
}
?>