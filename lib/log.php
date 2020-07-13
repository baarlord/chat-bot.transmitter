<?php

namespace lib;

class Log
{
    /**
     * @param string $domain - client domain
     * @return string
     */
    private static function buildLogFileFullPathByDomain($domain)
    {
        return Log::buildLogDirFullPathByDomain($domain) . date('Y-m-d') . '.log';
    }

    /**
     * @param string $domain - client domain
     * @return string
     */
    private static function buildLogDirFullPathByDomain($domain)
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/chat_bot_transmitter/logs/' . $domain . '/';
    }

    /**
     * Write to log.
     * @param mixed $message - data.
     * @param string $domain - client domain.
     */
    public static function add($message, $domain)
    {
        $infoDebug = debug_backtrace();
        $infoDebug = $infoDebug[0];

        $log = '------------------------' . "\r\n";
        $log .= date('Y-m-d H:i:s') . "\r\n";
        $log .= $infoDebug['file'] . ':' . $infoDebug['line'] . "\r\n";
        $log .= print_r($message, true) . "\r\n";
        $log .= '------------------------' . "\r\n";

        self::AppendLog($log, $domain);
    }

    /**
     * Append a message to the log
     * @param string $message - data to log.
     * @param string $domain - client domain.
     */
    private static function appendLog($message, $domain)
    {
        $mode = 'ab';

        try {
            if (!file_exists(Log::buildLogDirFullPathByDomain($domain))) {
                mkdir(Log::buildLogDirFullPathByDomain($domain), 0777, true);
            }
            $fp = fopen(Log::buildLogFileFullPathByDomain($domain), $mode);
            fwrite($fp, $message);
            fclose($fp);
        } catch (Exception $exception) {
            echo '<pre>';
            print_r($exception->getMessage());
            echo '</pre>';
        }
    }

    /**
     * Deleting old files
     * @param int $days - number of days. All files older than today by this number are subject to deletion.
     * @param string $domain - client domain.
     */
    public static function deleteOldFiles($days, $domain)
    {
        if ($days < 0) {
            return;
        }

        if (empty($domain)) {
            return;
        }

        $fileNames = array();
        for ($index = 0; $index <= $days; $index++) {
            $fileNames[] = date('Y-m-d', strtotime('-' . $index . ' day')) . '.log';
        }

        if ($handler = opendir(Log::buildLogDirFullPathByDomain($domain))) {
            while (false !== ($entry = readdir($handler))) {
                $fileName = $entry;
                if ($fileName == '.' or $fileName == '..') {
                    continue;
                }

                if (!in_array($fileName, $fileNames)) {
                    unlink(Log::buildLogDirFullPathByDomain($domain) . $entry);
                }
            }
        }
    }
}