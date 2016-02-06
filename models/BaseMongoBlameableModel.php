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

namespace vistart\Models\models;

use vistart\Models\traits\BlameableTrait;

/**
 * Description of BaseMongoBlameableModel
 *
 * @author vistart <i@vistart.name>
 */
abstract class BaseMongoBlameableModel extends BaseMongoEntityModel
{
    use BlameableTrait;

    /**
     * Initialize the blameable model.
     * If query class is not specified, [[BaseBlameableQuery]] will be taken.
     */
    public function init()
    {
        if (!is_string($this->queryClass)) {
            $this->queryClass = \vistart\Models\queries\BaseMongoBlameableQuery::className();
        }
        if ($this->skipInit) {
            return;
        }
        $this->initBlameableEvents();
        parent::init();
    }

    /**
     * Get the query class with specified identity.
     * @param \vistart\Models\models\BaseUserModel $identity
     * @return \vistart\Models\queries\BaseBlameableQuery
     */
    public static function findByIdentity($identity = null)
    {
        return static::find()->byIdentity($identity);
    }

    /**
     * Because every document has a `MongoId" class, this class is no longer needed GUID feature.
     * @var boolean determines whether enable the GUID features.
     */
    public $guidAttribute = false;
    public $idAttribute = '_id';
    
    public function attributes()
    {
        return $this->enabledFields();
    }
}
