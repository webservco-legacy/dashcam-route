<?php
namespace WebServCo\Multimedia;

class Subtitles
{

    const SRT_STATE_SUBNUMBER = 0;

    const SRT_STATE_TIME = 1;

    const SRT_STATE_TEXT = 2;

    const SRT_STATE_BLANK = 3;

    private static $_msSubs;

    private static $_msState;

    private static $_msSubNum;

    private static $_msSubText;

    private static $_msSubTime;

    /*
     * Parse srt subtitles file.
     * 
     * From https://stackoverflow.com/questions/11659118/parsing-srt-files
     */
    public static function parseSrt($filePath)
    {
        $lines = file($filePath);
        
        self::$_msSubs = [];
        self::$_msState = self::SRT_STATE_SUBNUMBER;
        self::$_msSubNum = 0;
        self::$_msSubText = '';
        self::$_msSubTime = '';
        
        foreach ($lines as $line) {
            switch (self::$_msState) {
                case self::SRT_STATE_SUBNUMBER:
                    self::$_msSubNum = trim($line);
                    self::$_msState = self::SRT_STATE_TIME;
                    break;
                
                case self::SRT_STATE_TIME:
                    self::$_msSubTime = trim($line);
                    self::$_msState = self::SRT_STATE_TEXT;
                    break;
                
                case self::SRT_STATE_TEXT:
                    if (trim($line) == '') {
                        $sub = new \stdClass();
                        $sub->number = self::$_msSubNum;
                        list ($sub->startTime, $sub->stopTime) = explode(' --> ', self::$_msSubTime);
                        $sub->text = self::$_msSubText;
                        self::$_msSubText = '';
                        self::$_msState = self::SRT_STATE_SUBNUMBER;
                        
                        self::$_msSubs[] = $sub;
                    } else {
                        self::$_msSubText .= $line;
                    }
                    break;
            }
        }
        
        if (self::$_msState == self::SRT_STATE_TEXT) {
            // if file was missing the trailing newlines, we'll be in this
            // state here. Append the last read text and add the last sub.
            $sub->text = self::$_msSubText;
            self::$_msSubs[] = $sub;
        }
        
        return self::$_msSubs;
    }
}
?>