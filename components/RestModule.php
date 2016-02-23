<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link http://vistart.name/
 * @copyright Copyright (c) 2016 vistart
 * @license http://vistart.name/license/
 */

namespace vistart\Models\components;

use Yii;
use yii\web\Response;
use vistart\Models\traits\RestModuleTrait;

/**
 * This abstract module is used for building RESTful API Module.
 * Usage:
 * 1. You should redefine the `$controllerNamespace`
 *
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
abstract class RestModule extends \yii\base\Module
{
    use RestModuleTrait;

    public function init()
    {
        parent::init();
        Yii::$app->response->on(Response::EVENT_BEFORE_SEND, [$this, 'responseBeforeSend']);
    }
}
