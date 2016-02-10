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

/**
 * Description of MessageQueryTrait
 *
 * @author vistart <i@vistart.name>
 */
trait MessageQueryTrait
{

    public function unread()
    {
        $model = $this->noInitModel;
        $raAttribute = $model->readAtAttribute;
        if (!is_string($raAttribute)) {
            return $this;
        }
        return $this->andWhere([$this->$raAttribute => $model->initDatetime()]);
    }

    public function read()
    {
        $model = $this->noInitModel;
        $raAttribute = $model->readAtAttribute;
        if (!is_string($raAttribute)) {
            return $this;
        }
        return $this->andWhere(['!=', $this->$raAttribute, $model->initDatetime()]);
    }

    public function unreceived()
    {
        $model = $this->noInitModel;
        $raAttribute = $model->receivedAtAttribute;
        if (!is_string($raAttribute)) {
            return $this;
        }
        return $this->andWhere([$this->$raAttribute => $model->initDatetime()]);
    }

    public function received()
    {
        $model = $this->noInitModel;
        $raAttribute = $model->receivedAttribute;
        if (!is_string($raAttribute)) {
            return $this;
        }
        return $this->andWhere(['!=', $this->$raAttribute, $model->initDatetime()]);
    }
}
