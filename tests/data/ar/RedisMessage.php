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

namespace vistart\Models\tests\data\ar;

use vistart\Models\models\BaseRedisMessageModel;

/**
 * Description of RedisMessage
 *
 * @author vistart <i@vistart.name>
 */
class RedisMessage extends BaseRedisMessageModel
{

    public static function primaryKey()
    {
        $model = new static(['skipInit' => true]);
        return [$model->guidAttribute];
    }
}
