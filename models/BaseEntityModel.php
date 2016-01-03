<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link http://vistart.name/
 * @copyright Copyright (c) 2015 vistart
 * @license http://vistart.name/license/
 */

namespace vistart\Models\models;

use yii\db\ActiveRecord;
use vistart\Models\traits\EntityTrait;

/**
 * The abstract BaseEntityModel is used for entity class.
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
abstract class BaseEntityModel extends ActiveRecord {

    use EntityTrait;

    /**
     * @var boolean Determines to skip initialization.
     */
    public $skipInit = false;

    /**
     * Initialize new entity.
     */
    public function init() {
        if ($this->skipInit)
            return;
        $this->on(self::$EVENT_NEW_RECORD_CREATED, [$this, 'onInitGuidAttribute']);
        $this->on(self::$EVENT_NEW_RECORD_CREATED, [$this, 'onInitIdAttribute']);
        $this->on(self::$EVENT_NEW_RECORD_CREATED, [$this, 'onInitIpAddress']);
        if ($this->isNewRecord) {
            $this->trigger(self::$EVENT_NEW_RECORD_CREATED);
        }
        parent::init();
    }

}
