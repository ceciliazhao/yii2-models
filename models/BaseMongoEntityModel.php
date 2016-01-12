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

namespace vistart\Models\models;

use vistart\Models\traits\EntityTrait;

/**
 * Description of BaseMongoEntityModel
 *
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
abstract class BaseMongoEntityModel extends \yii\mongodb\ActiveRecord {

    use EntityTrait;
    
    public $guidAttribute = false;

    /**
     * Initialize new entity.
     */
    public function init() {
        if ($this->skipInit)
            return;
        $this->initEvents();
        parent::init();
    }

}
