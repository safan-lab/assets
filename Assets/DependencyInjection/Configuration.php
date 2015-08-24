<?php
/**
 * This file is part of the Safan package.
 *
 * (c) Harut Grigoryan <ceo@safanlab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Assets\DependencyInjection;

use Safan\GlobalExceptions\ParamsNotFoundException;

class Configuration
{
    /**
     * @var string
     */
    private $path = '';

    /**
     * @param $params
     * @throws \Safan\GlobalExceptions\ParamsNotFoundException
     */
    public function buildConfig($params){
        if(!isset($params['path']))
            throw new ParamsNotFoundException('Path parameter is not found form AssetManager');

        $this->path = $params['path'];
    }

    /**
     * @return string
     */
    public function getPath(){
        return $this->path;
    }
}