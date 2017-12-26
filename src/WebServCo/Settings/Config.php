<?php
namespace WebServCo\Settings;

class Config {
    
    const 
        STOP_ON_ERRORS = TRUE,
        
        APP_PATH = '/home/radu/web/car/',
        
        LOG_DIR = self::APP_PATH . 'var/log/',
        LOG_OUTPUT = TRUE,
        
        DEBUG_DIR = self::APP_PATH . 'var/debug/',
        DEBUG_OUTPUT = FALSE,
        
        SRT_DIR = self::APP_PATH . 'var/srt/',
        KML_DIR = self::APP_PATH . 'var/kml/',
        
        XML_COMPRESSED = TRUE,
        
        DB_HOST = 'localhost',
        DB_USERNAME = 'dashcam',
        DB_PASSWD = 'REDACTED',
        DB_DBNAME = 'dashcam'
        ;
}
?>