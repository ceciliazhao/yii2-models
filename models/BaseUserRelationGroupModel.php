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

namespace vistart\Models\models;

use vistart\Models\traits\UserRelationGroupTrait;

/**
 * 该类帮助用户定义关系组。
 *
 * $contentAttribute 关系组名称。
 * $contentTypeAttribute 关系组类型。
 * 
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
abstract class BaseUserRelationGroupModel extends BaseBlameableModel {

    use UserRelationGroupTrait;

    public $confirmationAttribute = false;
    public $enableIP = false;
    public $idAttribute = false;
    public $updatedAtAttribute = false;
    public $updatedByAttribute = false;

    public function init() {
        if ($this->skipInit)
            return;
        $this->initUserRelationGroupEvents();
        parent::init();
    }

}
