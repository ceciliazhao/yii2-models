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
 * This trait is used for building message query class for message model.
 *
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
trait MessageQueryTrait
{
    use MutualQueryTrait;

    /**
     * Specify unread message.
     * @return \static $this
     */
    public function unread()
    {
        $model = $this->noInitModel;
        return $this->likeCondition($model->initDatetime(), $model->readAtAttribute);
    }

    /**
     * Specify read message.
     * @return \static $this
     */
    public function read()
    {
        $model = $this->noInitModel;
        return $this->likeCondition($model->initDatetime(), $model->readAtAttribute, 'not in');
    }

    /**
     * Specify unreceived message.
     * @return \static $this
     */
    public function unreceived()
    {
        $model = $this->noInitModel;
        return $this->likeCondition($model->initDatetime(), $model->receivedAtAttribute);
    }

    /**
     * Specify received message.
     * @return \static $this
     */
    public function received()
    {
        $model = $this->noInitModel;
        return $this->likeCondition($model->initDatetime(), $model->receivedAtAttribute, 'not in');
    }
}
