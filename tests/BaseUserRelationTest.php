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

use vistart\Models\tests\data\ar\User;
use vistart\Models\tests\data\ar\UserRelation;
use vistart\Models\tests\data\ar\UserRelationGroup;

/**
 * Description of UserRelationTest
 *
 * @author vistart <i@vistart.name>
 */
class BaseUserRelationTest extends TestCase {

    private function prepareModels() {
        $user = new User(['password' => '123456']);
        $other_user = new User(['password' => '123456']);
        $this->assertTrue($user->register());
        $this->assertTrue($other_user->register());
        return [$user, $other_user];
    }

    private function destroyModels($users = []) {
        foreach ($users as $user) {
            $this->assertTrue($user->deregister());
        }
    }

    private function prepareRelationModels($user, $other) {
        $initRelation = UserRelation::buildNoInitModel();
        $relation = $user->createModel(UserRelation::className(), [$initRelation->otherGuidAttribute => $other->guid]);
        if ($relation->save()) {
            $this->assertTrue(true);
            $opposite = UserRelation::findOneOppositeRelation($user, $other);
            $this->assertInstanceOf(UserRelation::className(), $opposite);
            $opposite = $relation->opposite;
            $this->assertInstanceOf(UserRelation::className(), $opposite);
            $opposites = UserRelation::findAllOppositeRelations($user, $other);
            $this->assertEquals(1, count($opposites));
        } else {
            $this->assertTrue(false);
            var_dump($relation->errors);
        }
        return [$relation, $opposite];
    }

    public function testNew() {
        $users = $this->prepareModels();
        $user = $users[0];
        $other = $users[1];
        $relations = $this->prepareRelationModels($user, $other);
        $this->destroyModels($users);
        echo __METHOD__ . ":Done!\n";
    }

    /**
     * @depends testNew
     */
    public function testRemoveOne() {
        echo __METHOD__ . ":Start!\n";
        $users = $this->prepareModels();
        $user = $users[0];
        $other = $users[1];
        echo "Initiator:" . $user->guid . "\n";
        echo "Recipient:" . $other->guid . "\n";

        $this->prepareRelationModels($user, $other);
        UserRelation::removeOneRelation($user, $other);
        $relations = UserRelation::findAllRelations($user, $other);
        $this->assertEmpty($relations);
        $opposites = UserRelation::findAllOppositeRelations($user, $other);
        $this->assertEmpty($opposites);

        $relations = $this->prepareRelationModels($user, $other);
        $this->assertEquals(1, $relations[0]->remove());
        $relations = UserRelation::findAllRelations($user, $other);
        $this->assertEmpty($relations);
        $opposites = UserRelation::findAllOppositeRelations($user, $other);
        $this->assertEmpty($opposites);

        $this->destroyModels($users);
        echo __METHOD__ . ":Done!\n";
    }

    /**
     * @depends testRemoveOne
     */
    public function testDeregisterOne() {
        $users = $this->prepareModels();
        $user = $users[0];
        $other = $users[1];
        $this->prepareRelationModels($user, $other);
        if ($user->deregister()) {
            $this->assertTrue(true);
            $relations = UserRelation::findAllRelations($user, $other);
            $this->assertEmpty($relations);
            $opposites = UserRelation::findAllOppositeRelations($user, $other);
            $this->assertEmpty($opposites);
            $this->assertTrue($other->deregister());
        } else {
            $this->assertTrue(false);
            var_dump($user->errors);
        }
        echo __METHOD__ . ":Done!\n";
    }
    
    /**
     * @depends testDeregisterOne
     */
    public function testFavorite() {
        $users = $this->prepareModels();
        $relations = $this->prepareRelationModels($users[0], $users[1]);
        $favoriteAttribute = $relations[0]->favoriteAttribute;
        $this->assertEquals(0, $relations[0]->$favoriteAttribute);
        $this->assertFalse($relations[0]->isFavorite);
        $relations[0]->isFavorite = true;
        $this->assertTrue($relations[0]->save());
        $this->assertEquals(1, $relations[0]->$favoriteAttribute);
        $this->assertTrue($relations[0]->isFavorite);
        $this->destroyModels($users);
        echo __METHOD__ . ":Done!\n";
    }
    
    /**
     * @depends testFavorite
     */
    public function testGroup() {
        // 准备两个用户
        $users = $this->prepareModels();
        
        // 准备两个用户之间的双向关系
        $relations = $this->prepareRelationModels($users[0], $users[1]);
        
        // 第一个用户的主动关系
        $relation = $relations[0];
        
        // 当前关系组应为空数组
        $groupsAttribute = $relation->groupsAttribute;
        $this->assertEquals('[]', $relation->$groupsAttribute);
        
        // 当前未分组用户应为 1，即对方
        $members = $relation->getNonGroupMembers();
        $this->assertEquals(1, count($members));
        
        // 新建一个组，在保存前，当前关系找不到该组。
        $group = $users[0]->createModel(UserRelationGroup::className(), ['content' => 'home']);
        $this->assertEmpty($relation->getGroupMembers($group));
        $this->assertEmpty(UserRelation::getGroup($group->guid));
        if ($group->save()) {
            $this->assertTrue(true);
        } else {
            var_dump($group->errors);
            $this->assertFalse(true);
        }
        // 保存后也应当找不到，因为当前关系没有添加任何组。
        $this->assertEmpty($relation->getGroupMembers($group));
        // 不过
        $this->assertNotEmpty(UserRelation::getGroup($group->guid));
        
        // 添加一个关系组，并获得添加后的关系组数组。
        $relationGroups = $relation->addGroup($group);
        
        // 此时应该有 1 个元素，即 1 个组。
        $this->assertEquals(1, count($relationGroups));
        $this->assertEquals($group->guid, $relationGroups[0]);
        $this->assertEquals($group->guid, $relation->groupGuids[0]);
        if ($relation->save()) {
            $this->assertTrue(true);
        } else {
            var_dump($relation->errors);
            $this->assertFalse(true);
        }
        // 删除成功
        $this->assertGreaterThanOrEqual(1, $group->delete());
        // 虽然关系组删除了，但不会影响涉及到的关系，所以包含了被删除的关系组的关系，其关系组列表依然包含该关系组。
        // 因此，此时直接获取关系组列表，被删除的组GUID依然在列表中。
        $this->assertNotEmpty($relation->groupGuids);
        // 如果要主动将失效的关系组剔除出关系组列表，可以在获取关系组列表时，强制检查有效性：
        $this->assertEmpty($relation->getGroupGuids(true)); // 此时应该为空。
        // 而且标明列表已经改变了。
        $this->assertTrue($relation->blamesChanged);
        
        $this->destroyModels($users);
        echo __METHOD__ . ":Done!\n";
    }

}
