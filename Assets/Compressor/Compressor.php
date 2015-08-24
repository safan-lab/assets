<?php
/**
 * This file is part of the Safan package.
 *
 * (c) Harut Grigoryan <ceo@safanlab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Assets\Compressor;

use Assets\DependencyInjection\Configuration;
use Assets\Exceptions\DirectoryException;

class Compressor
{
    /**
     * @var string
     */
    private $assetsPath = '';

    /**
     * @param  Configuration $config
     * @throws \Assets\Exceptions\DirectoryException
     */
    public function __construct(Configuration $config){
        // set paths
        $this->assetsPath = APP_BASE_PATH . DS . 'resource' . DS . $config->getPath();

        if(!is_writable($this->assetsPath))
            throw new DirectoryException('Assets path is not writable');
    }

    /**
     * @return string
     */
    public function getAssetsPath(){
        return $this->assetsPath;
    }

    /**
     * Generate files for asset files
     */
    public function generate($path, $moduleName, $assetSubPath = ''){
        // get assets path
        $assetPath = $this->getAssetsPath();

        if(is_dir($path)){
            $dh  = opendir($path);
            while (false !== ($filename = readdir($dh))) {
                if($filename != '.' && $filename != '..'){
                    $currentPath = $path . DS . $filename;
                    $newAsset    = $assetPath . DS . strtolower(str_replace('\\', '/', $moduleName)) . DS . $assetSubPath . DS . $filename;
                    $newAsset    = str_replace('//', '/', $newAsset);

                    if(is_file($currentPath) && file_exists($currentPath)){
                        copy($currentPath, $newAsset);
                        chmod($newAsset, 0777);
                    }
                    else if(is_dir($currentPath)){
                        // create dir if not exist
                        if(!is_dir($newAsset)){
                            mkdir($newAsset, 0777, true);
                            chmod($newAsset, 0777);
                        }

                        $this->generate($currentPath, $moduleName, $assetSubPath . DS . $filename);
                    }
                }
            }
        }
    }
}