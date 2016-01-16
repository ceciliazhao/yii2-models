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

use vistart\Models\traits\UserRelationTrait;

/**
 * 该类帮助用户定义用户关系。
 * 
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
abstract class BaseUserRelationModel extends BaseBlameableModel {

    use UserRelationTrait;

    public $idAttribute = false;
    public $confirmationAttribute = false;
    public $contentAttribute = false;
    public $updatedByAttribute = false;

    public function init() {
        if ($this->skipInit)
            return;
        $this->initUserRelationEvents();
        parent::init();
    }

}
