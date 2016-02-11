<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.name/
 * @copyright Copyright (c) 2016 vistart
 * @license https://vistart.name/license/
 */

namespace vistart\Models\traits;

use vistart\Models\traits\MutualQueryTrait;

/**
 * Description of MessageQueryTrait
 *
 * @author vistart <i@vistart.name>
 */
trait MessageQueryTrait
{
    use MutualQueryTrait;

    public function unread()
    {
        $model = $this->noInitModel;
        return $this->likeCondition($model->initDatetime(), $model->readAtAttribute);
    }

    public function read()
    {
        $model = $this->noInitModel;
        return $this->likeCondition($model->initDatetime(), $model->readAtAttribute, 'not in');
    }

    public function unreceived()
    {
        $model = $this->noInitModel;
        return $this->likeCondition($model->initDatetime(), $model->receivedAtAttribute);
    }

    public function received()
    {
        $model = $this->noInitModel;
        return $this->likeCondition($model->initDatetime(), $model->receivedAtAttribute, 'not in');
    }
}
