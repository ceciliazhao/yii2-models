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

namespace vistart\Models\queries;

use vistart\Models\traits\EntityQueryTrait;
use yii\mongodb\ActiveQuery;

/**
 * Description of BaseMongoEntityQuery
 *
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
class BaseMongoEntityQuery extends ActiveQuery
{
    use EntityQueryTrait;

    public function init()
    {
        $this->buildNoInitModel();
        parent::init();
    }
}
