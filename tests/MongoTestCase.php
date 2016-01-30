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

namespace vistart\Models\tests;

use yii\db\Connection;

/**
 * Description of MongoTestCase
 *
 * @author vistart <i@vistart.name>
 */
class MongoTestCase extends TestCase
{

    public function setUp()
    {
        $databases = self::getParam('databases');
        $params = isset($databases['mysql']) ? $databases['mysql'] : null;
        if ($params === null) {
            $this->markTestSkipped('No mysql server connection configured.');
        }
        $connection = new Connection($params);
        $redis = self::getParam('redis');
        $mongodb = self::getParam('mongodb');
        $cacheParams = self::getParam('cache');

        $this->mockWebApplication(['components' => ['redis' => $redis, 'mongodb' => $mongodb, 'db' => $connection, 'cache' => $cacheParams]]);
    }
}
