<?php
/**
 * This file is part of the Safan package.
 *
 * (c) Harut Grigoryan <ceo@safanlab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Assets;

use Assets\Compressor\Compressor;
use Assets\DependencyInjection\Configuration;
use Assets\Manager\CssManager;
use Assets\Manager\JsManager;
use Safan\GlobalExceptions\FileNotFoundException;
use Safan\GlobalExceptions\ParamsNotFoundException;
use Safan\Safan;

class Assets
{
    /**
     * @var string
     */
    private $assetsUri = '';

    /**
     * @var Configuration $config
     */
    private $config;

    /**
     * @var Compressor $compressor
     */
    private $compressor;

    /**
     * @var CssManager $cssManager
     */
    private $cssManager;

    /**
     * @var JsManager $jsManager
     */
    private $jsManager;

    /**
     * Initialize Assets
     *
     * @param  $params
     * @throws FileNotFoundException
     */
    public function init($params){
        // build config parameters
        $config = new Configuration();
        $config->buildConfig($params);

        // check driver
        $this->assetsUri  = Safan::handler()->baseUrl . '/' . $config->getPath();
        $this->compressor = new Compressor($config);
        $this->cssManager = new CssManager();
        $this->jsManager  = new JsManager();
        $this->config     = $config;

        // set to object manager
        $om = Safan::handler()->getObjectManager();
        $om->setObject('assets', $this);
    }

    /**
     * @return Configuration
     */
    public function getConfig(){
        return $this->config;
    }

    /**
     * Get mapping file data
     */
    public function getCompressor(){
        return $this->compressor;
    }

    /**
     * @param $filePath
     * @param array $attributes
     * @return string
     * @throws \Safan\GlobalExceptions\ParamsNotFoundException
     * @throws \Safan\GlobalExceptions\FileNotFoundException
     */
    public function __invoke($filePath, $attributes = array()){
        $asset = explode(':', $filePath);
        if(sizeof($asset) !== 2)
            throw new FileNotFoundException('Css asset name is not correct');

        $moduleName = $asset[0];
        $filePath = $asset[1];
        $modules = Safan::handler()->getModules();

        if(!isset($modules[$moduleName]))
            throw new FileNotFoundException('Asset module is not define');

        $fullPath = APP_BASE_PATH . DS . $modules[$moduleName] . DS . 'Resources' . DS . 'public' . DS . $filePath;

        if(!file_exists($fullPath))
            throw new FileNotFoundException('Asset file is not define');

        // get file extension
        $extension = end(explode('.', $fullPath));
        $assetLink = $this->assetsUri . '/' . strtolower($moduleName) . '/' . $filePath;

        if($extension == 'css'){
            $htmlAttributes = '';
            foreach($attributes as $key => $attr)
                $htmlAttributes .= ' ' . $key . '="' . $attr . '"';

            // check rel and type
            if(!isset($attributes['rel']))
                $htmlAttributes .= ' rel="stylesheet"';

            if(!isset($attributes['type']))
                $htmlAttributes .= ' type="text/css"';

            return '<link href="'. $assetLink .'" '. $htmlAttributes .' />';
        }
        else if($extension == 'js'){
            $htmlAttributes = '';
            foreach($attributes as $key => $attr)
                $htmlAttributes .= ' ' . $key . '="' . $attr . '"';

            // check type
            if(!isset($attributes['type']))
                $htmlAttributes .= ' type="text/javascript"';

            return '<script ' . $htmlAttributes . ' src="'. $assetLink .'"></script>';
        }
        else
            throw new ParamsNotFoundException('Unknown asset type');
    }

    /**
     * Minify files
     *
     * @param $assetFiles
     * @param $assetType
     * @return bool
     */
    public function minify($assetFiles, $assetType){
        // convert and return file paths
        $assetFiles = $this->translator($assetFiles);

        if($assetType == 'css')
            return $this->cssManager->checkCustomAssets($assetFiles);
        elseif($assetType == 'js')
            return $this->jsManager->checkCustomAssets($assetFiles);
    }

    /**
     * Translate path
     *
     * @param $files
     * @return array
     * @throws \Safan\GlobalExceptions\FileNotFoundException
     */
    private function translator($files){
        // empty array for return
        $fileArray = array();
        // get modules
        $modules = Safan::handler()->getModules();

        foreach($files as $filePath){
            $asset = explode(':', $filePath);
            if(sizeof($asset) !== 2)
                throw new FileNotFoundException('Css asset name is not correct');

            $moduleName = $asset[0];
            $filePath   = $asset[1];

            if(!isset($modules[$moduleName]))
                throw new FileNotFoundException('Asset module is not define');

            $fullPath = APP_BASE_PATH . DS . $modules[$moduleName] . DS . 'Resources' . DS . 'public' . DS . $filePath;

            if(!file_exists($fullPath))
                throw new FileNotFoundException('Asset file is not define');

            $fileArray[] = $fullPath;
        }

        return $fileArray;
    }

    /**
     * @return object|CssManager
     */
    public function getCssManager(){
        return $this->cssManager;
    }

    /**
     * @return object|JsManager
     */
    public function getJsManager(){
        return $this->jsManager;
    }
}
