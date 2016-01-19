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

use vistart\Models\traits\BlameableTrait;

/**
 * Description of BaseMongoBlameableModel
 *
 * @author vistart <i@vistart.name>
 */
class BaseMongoBlameableModel extends BaseMongoEntityModel
{

    use BlameableTrait;

    public $guidAttribute = false;

    public function attributes()
    {
        return [
            '_id',
            $this->createdByAttribute,
        ];
    }
}
