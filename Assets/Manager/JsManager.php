<?php
/**
 * This file is part of the Safan package.
 *
 * (c) Harut Grigoryan <ceo@safanlab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Assets\Manager;

use Safan\GlobalExceptions\FileNotFoundException;
use Safan\Safan;

class JsManager
{
    /**
     * @var string
     */
    private $yuiVersion = '2.4.8';

    /**
     * @var string
     */
    private $cacheDir = '/assets/js';

    /**
     * @var array
     */
    public $js = array();

    /**
     * Check dir for css files
     */
    public function __construct(){
        $jsDir = APP_BASE_PATH . DS . 'resource' . $this->cacheDir;

        if(!is_dir($jsDir)){
            mkdir($jsDir, 0777, true);
            chmod($jsDir, 0777);
        }
    }

    /**
     * @return object
     */
    public function getJs(){
        return $this->js;
    }

    /**
     * @param $files
     * @param string $cacheFileName
     * @return bool|void
     * @throws \Safan\GlobalExceptions\FileNotFoundException
     */
    public function compressFiles($files, $cacheFileName = ''){
        if(empty($files))
            return false;

        $buffer = '';
        foreach ($files as $jsFile) {
            if(!file_exists($jsFile))
                throw new FileNotFoundException('Asset js file not found');

            $buffer .= "\n" . file_get_contents($jsFile);
        }

        if($cacheFileName == '')
            $cacheFileName = md5(implode('_', $files)) . '.js';

        // Write everything out
        $filePath = APP_BASE_PATH . DS . 'resource' . $this->cacheDir . DS . $cacheFileName;
        $fp       = fopen($filePath, 'w');

        if(!$this->fwriteStream($fp, $buffer))
            return false;

        fclose($fp);

        return $this->minify($filePath);
    }

    /**
     * @param $fp
     * @param $string
     * @return int
     */
    private function fwriteStream($fp, $string) {
        for ($written = 0; $written < strlen($string); $written += $fwrite) {
            $fwrite = fwrite($fp, substr($string, $written));

            if ($fwrite === false)
                return $written;
        }

        return $written;
    }

    /**
     *
     */
    private function minify($filePath){
        shell_exec('java -jar '. dirname(__FILE__) . DS . 'yui' . DS .'yuicompressor-'. $this->yuiVersion .'.jar --type=js '. $filePath .' -o ' . $filePath);
    }

    /**
     * @param $files
     * @return bool
     */
    public function checkCustomAssets($files){
        if(empty($files))
            return false;

        $fileName = md5(implode('_', $files)) . '.js';
        $jsFile = APP_BASE_PATH . DS . 'resource' . $this->cacheDir . DS . $fileName;

        if(!file_exists($jsFile))
            $this->compressFiles($files);

        $cacheFileUri = Safan::handler()->baseUrl . $this->cacheDir . '/' . $fileName;

        return $this->getCacheFile($cacheFileUri);
    }

    /**
     * @param $fileUrl
     * @return string
     */
    public function getCacheFile($fileUrl){
        return '<script type="text/javascript" src="'. $fileUrl .'"></script>';
    }
}