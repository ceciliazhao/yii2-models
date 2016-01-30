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

namespace vistart\Models\tests\data\ar;

/**
 * Description of RedisEntity
 *
 * @author vistart <i@vistart.name>
 */
class RedisEntity extends \vistart\Models\models\BaseRedisEntityModel
{

    public $guidAttribute = false;
    public $idAttribute = 'alpha2';

    public static function primaryKey()
    {
        return [
            'alpha2',
        ];
    }
}
