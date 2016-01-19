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

namespace vistart\Models\queries;

use vistart\Models\traits\EntityQueryTrait;

/**
 * Description of BaseEntityQuery
 *
 * @author vistart <i@vistart.name>
 */
class BaseEntityQuery extends \yii\db\ActiveQuery {

    use EntityQueryTrait;

    public function init() {
        $this->initNoModel();
        parent::init();
    }

}
