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

use vistart\Models\tests\data\ar\RedisEntity;

/**
 * Description of RedisEntityTest
 *
 * @author vistart <i@vistart.name>
 */
class RedisEntityTest extends TestCase
{

    /**
     * @group redis
     * @group entity
     */
    public function testNew()
    {
        $entity = new RedisEntity();
        $this->assertTrue($entity->save());
        $query = RedisEntity::find()->id($entity->id);
        $query1 = clone $query;
        $this->assertInstanceOf(RedisEntity::className(), $query1->one());
        $this->assertEquals(1, $entity->delete());
    }
}
