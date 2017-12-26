<?php
namespace WebServCo\Domain;

use WebServCo\Settings\Config;

class Database
{

    private static $_msMysqli;

    private static $_msInsertType = 'INSERT';

    private static function _connect()
    {
        if (self::$_msMysqli) {
            return FALSE;
        }
        
        self::$_msMysqli = new \mysqli(Config::DB_HOST, Config::DB_USERNAME, Config::DB_PASSWD, Config::DB_DBNAME);
        if (self::$_msMysqli->connect_error) {
            die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
        }
        self::$_msMysqli->set_charset('utf8mb4');
        
        return TRUE;
    }
    
    // XXX UNUSED
    private static function _close()
    {
        if (! self::$_msMysqli) {
            return FALSE;
        }
        
        self::$_msMysqli->close();
        
        return TRUE;
    }

    public static function getHostInfo()
    {
        self::_connect();
        
        Log::addMessage(__METHOD__);
        
        return self::$_msMysqli->host_info;
    }

    public static function setInsertType($insertType)
    {
        if (in_array($insertType, [
            'INSERT',
            'INSERT IGNORE',
            'REPLACE'
        ])) {
            self::$_msInsertType = $insertType;
            return TRUE;
        }
        return FALSE;
    }

    public static function insert($table, $data, $returnId = TRUE, $multiple = FALSE, $dataAppend = array())
    {
        self::_connect();
        
        if (Config::DEBUG_OUTPUT) {
            Log::addMessage(__METHOD__);
        }
        
        if ($multiple) {
            $keys = array_keys(call_user_func_array('array_merge', $data));
            // fill any missing keys with empty data
            $key_pair = array_combine($keys, array_fill(0, count($keys), NULL));
            $data = array_map(function ($e) use ($key_pair) {
                return array_merge($key_pair, $e);
            }, $data);
        } else {
            $keys = array_keys($multiple ? $data[0] : $data);
        }
        
        if (! empty($dataAppend)) {
            $keys = array_merge($keys, array_keys($dataAppend));
        }
        
        $q = self::$_msInsertType . " INTO {$table} (";
        $q .= implode(', ', array_map(function ($v) {
            return "`{$v}`";
        }, $keys));
        $q .= ") VALUES";
        
        if ($multiple) {
            $values = array();
            foreach ($data as $item) {
                if (! empty($dataAppend)) {
                    foreach ($dataAppend as $k => $v) {
                        $item[$k] = $v;
                    }
                }
                $values[] = " ('" . implode("', '", array_map(function ($v) {
                    return self::$_msMysqli->escape_string($v);
                }, $item)) . "')";
            }
            $q .= implode(', ', $values);
        } else {
            if (! empty($dataAppend)) {
                foreach ($dataAppend as $k => $v) {
                    $data[$k] = $v;
                }
            }
            $q .= " ('";
            $q .= implode("', '", array_map(function ($v) {
                return self::$_msMysqli->escape_string($v);
            }, $data));
            $q .= "')";
        }
        
        if (Config::DEBUG_OUTPUT) {
            Log::addMessage($q);
        }
        
        if (! $result = self::$_msMysqli->query($q)) {
            if (! Config::DEBUG_OUTPUT) { // already logged in debug mode
                Log::addMessage($q);
            }
            Log::addMessage('MySQL error: ' . self::$_msMysqli->error);
            return FALSE;
        }
        // http://php.net/manual/en/mysqli.get-warnings.php#93021
        if (self::$_msMysqli->warning_count) {
            $result = FALSE;
            if (! Config::DEBUG_OUTPUT) { // already logged in debug mode
                Log::addMessage($q);
            }
            $e = self::$_msMysqli->get_warnings();
            do {
                Log::addMessage("MySQL warning: {$e->errno}: {$e->message}");
            } while ($e->next());
        }
        return $returnId ? self::$_msMysqli->insert_id : $result;
    }
}
?>