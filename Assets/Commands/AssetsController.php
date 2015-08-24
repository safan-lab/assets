<?php
/**
 * This file is part of the Safan package.
 *
 * (c) Harut Grigoryan <ceo@safanlab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Assets\Commands;

use Safan\CliManager\CliManager;
use Safan\Safan;

class AssetsController
{
    /**
     * Install Assets
     */
    public function installAction(){
        // Get all modules
        $modules = Safan::handler()->getModules();
        // get Asset manager
        $assets = Safan::handler()->getObjectManager()->get('assets');
        // clear all assets
        $this->clearAction();

        foreach($modules as $moduleName => $path){
            $moduleAssetsPath = APP_BASE_PATH . DS . $path . DS . 'Resources' . DS . 'public';
            $assets->getCompressor()->generate($moduleAssetsPath, $moduleName);
        }
    }

    /**
     * Clear Assets
     */
    public function clearAction(){
        // get Asset manager
        $assets = Safan::handler()->getObjectManager()->get('assets');
        // get assets path
        $path = $assets->getCompressor()->getAssetsPath();

        if(is_dir($path))
            shell_exec('rm -rf ' . $path . DS . '*');
        else
            return CliManager::getErrorMessage('Assets path is not exist');
    }
}