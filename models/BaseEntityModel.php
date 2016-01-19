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

use yii\db\ActiveRecord;
use vistart\Models\traits\EntityTrait;

/**
 * The abstract BaseEntityModel is used for entity model class which associates
 * with relational database table.
 * Note: the $idAttribute and $guidAttribute are not be assigned to false
 * simultaneously, and you should set at least one of them as primary key.
 * @version 2.0
 * @author vistart <i@vistart.name>
 */
abstract class BaseEntityModel extends ActiveRecord {

    use EntityTrait;

    /**
     * @var string the name of query class or sub-class.
     */
    public $queryClass;

    /**
     * Initialize new entity.
     */
    public function init() {
        if ($this->skipInit)
            return;
        $this->initEntityEvents();
        $this->checkAttributes();
        parent::init();
    }

    /**
     * Check whether all properties meet the standards. If you want to disable
     * checking, please override this method and return true directly. This
     * method runs when environment is not production or disable debug mode.
     * @return boolean true if all checks pass.
     * @throws \yii\base\NotSupportedException
     */
    public function checkAttributes() {
        if (YII_ENV !== YII_ENV_PROD || YII_DEBUG) {
            if (!is_string($this->idAttribute) && empty($this->idAttribute) &&
                    !is_string($this->guidAttribute) && empty($this->guidAttribute)) {
                throw new \yii\base\NotSupportedException('ID and GUID attributes are not be disabled simultaneously in relational database.');
            }
        }
        return true;
    }

    /**
     * @inheritdoc
     * @return \vistart\Models\models\BaseEntityQuery the newly created [[BaseEntityQuery]] or its sub-class instance.
     */
    public static function find() {
        parent::find();
        $self = static::buildNoInitModel();
        if (!is_string($self->queryClass)) {
            $self->queryClass = \vistart\Models\queries\BaseEntityQuery::className();
        }
        $queryClass = $self->queryClass;
        return new $queryClass(get_called_class(), ['noInitModel' => $self]);
    }

}
