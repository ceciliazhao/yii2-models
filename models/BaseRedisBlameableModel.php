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

use vistart\Models\queries\BaseRedisBlameableQuery;
use vistart\Models\traits\BlameableTrait;

/**
 * Description of BaseRedisBlameableModel
 *
 * @author vistart <i@vistart.name>
 */
abstract class BaseRedisBlameableModel extends BaseRedisEntityModel
{
    use BlameableTrait;

    /**
     * Initialize the blameable model.
     * If query class is not specified, [[BaseBlameableQuery]] will be taken.
     */
    public function init()
    {
        if (!is_string($this->queryClass)) {
            $this->queryClass = BaseRedisBlameableQuery::className();
        }
        if ($this->skipInit) {
            return;
        }
        $this->initBlameableEvents();
        parent::init();
    }

    /**
     * Get the query class with specified identity.
     * @param BaseUserModel $identity
     * @return BaseRedisBlameableQuery
     */
    public static function findByIdentity($identity = null)
    {
        return static::find()->byIdentity($identity);
    }
}
