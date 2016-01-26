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

/**
 * Description of BaseUserQuery
 *
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
class BaseUserQuery extends BaseEntityQuery
{

    /**
     * Specify active status.
     * @param integer $active
     * @return \vistart\Models\queries\BaseUserQuery
     */
    public function active($active)
    {
        $model = $this->noInitModel;
        if (!is_string($model->statusAttribute)) {
            return $this;
        }
        return $this->andWhere([$model->statusAttribute => $active]);
    }

    /**
     * Specify source.
     * @param null|string|array $source
     * @return \vistart\Models\queries\BaseUserQuery
     */
    public function source($source = null)
    {
        $model = $this->noInitModel;
        if (!is_string($model->sourceAttribute)) {
            return $this;
        }
        if (!is_string($source)) {
            $modelClass = $this->modelClass;
            $source = $modelClass::$sourceSelf;
        }
        return $this->andWhere([$model->sourceAttribute => $source]);
    }
}
