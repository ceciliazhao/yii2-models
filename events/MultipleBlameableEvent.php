<?php

/*
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link http://vistart.name/
 * @copyright Copyright (c) 2016 vistart
 * @license http://vistart.name/license/
 */

namespace vistart\Models\events;

/**
 * Description of MultipleBlameableEvent
 *
 * @author vistart <i@vistart.name>
 */
class MultipleBlameableEvent extends \yii\base\Event {
    public $blamesChanged = true;
}
