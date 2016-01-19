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
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
class BaseEntityQuery extends \yii\db\ActiveQuery
{

    use EntityQueryTrait;

    public function init()
    {
        $this->initNoModel();
        parent::init();
    }

    /**
     * 
     * @param string|integer|array $id
     * @return \vistart\Models\queries\BaseUserQuery
     */
    public function id($id)
    {
        $model = $this->noInitModel;
        if (!is_string($model->idAttribute)) {
            return $this;
        }
        return $this->andWhere([$model->idAttribute => $id]);
    }
}
