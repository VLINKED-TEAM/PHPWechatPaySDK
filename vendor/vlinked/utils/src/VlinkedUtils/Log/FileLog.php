<?php


namespace VlinkedUtils\Log;

/**
 * Class FileLog
 * @method Info
 * @method Log
 * @method Debug
 * @method Error
 * @package VlinkedUtils\Log
 */
class FileLog
{
    protected $config = [
        'time_format' => ' c ',
        'single' => false,
        'file_size' => 2097152,
        'path' => "",
        'apart_level' => [],
        'max_files' => 0,
        'json' => false,
    ];

    /**
     * FileLog constructor.
     * @param $logPath
     */
    public function __construct($logPath)
    {
        $this->config['path'] = $logPath;
    }


    public function save($info)
    {
        $destination = $this->getMasterLogFile();

        $path = dirname($destination);
        !is_dir($path) && mkdir($path, 0755, true);
        if (is_array($info)) {
            $message = json_encode($info, JSON_UNESCAPED_UNICODE);
        } else if (is_object($info)) {
            $message = var_export($info, true);
        } else {
            $message = $info;
        }
        error_log($message . "\n", 3, $destination);
    }


    /**
     * 从 thinkphp那边获取的方法
     * 获取主日志文件名
     * @access public
     * @return string
     */
    protected function getMasterLogFile()
    {
        if ($this->config['single']) {
            $name = is_string($this->config['single']) ? $this->config['single'] : 'single';

            $destination = $this->config['path'] . $name . '.log';
        } else {
            $cli = PHP_SAPI == 'cli' ? '_cli' : '';

            if ($this->config['max_files']) {
                $filename = date('Ymd') . $cli . '.log';
                $files = glob($this->config['path'] . '*.log');

                try {
                    if (count($files) > $this->config['max_files']) {
                        unlink($files[0]);
                    }
                } catch (\Exception $e) {
                }
            } else {
                $filename = date('Ym') . DIRECTORY_SEPARATOR . date('d') . $cli . '.log';
            }

            $destination = $this->config['path'] . $filename;
        }

        return $destination;
    }

}