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
use vistart\Models\tests\data\ar\UserSingleRelation;
use vistart\Models\tests\data\ar\UserRelationGroup;

/**
 * Test BaseUserRelationModel.
 *
 * @author vistart <i@vistart.name>
 */
class BaseUserRelationTest extends TestCase
{

    private function prepareUsers()
    {
        $user = new User(['password' => '123456']);
        $other_user = new User(['password' => '123456']);
        $this->assertTrue($user->register());
        $this->assertTrue($other_user->register());
        return [$user, $other_user];
    }

    private function destroyUsers($users = [])
    {
        foreach ($users as $user) {
            $this->assertTrue($user->deregister());
        }
    }

    private function prepareSingleRelationModels($user, $other)
    {
        $relation = UserSingleRelation::buildNormalRelation($user, $other);
        if ($relation->save()) {
            $this->assertTrue(true);
            $opposite = UserSingleRelation::find()->opposite($user, $other);
            $this->assertNull($opposite);
        } else {
            var_dump($relation->rules());
            var_dump($relation->errors);
            $this->fail('Single Relation Save Failed.');
        }
        return $relation;
    }

    private function prepareMutualRelationModels($user, $other, $bi_type = null)
    {
        if (!$bi_type)
            $bi_type = UserRelation::$mutualTypeNormal;
        switch ($bi_type) {
            case UserRelation::$mutualTypeNormal:
                $relation = UserRelation::buildNormalRelation($user, $other);
                break;
            case UserRelation::$mutualTypeSuspend:
                $relation = UserRelation::buildSuspendRelation($user, $other);
                break;
        }
        if ($relation->save()) {
            $this->assertTrue(true);
            $opposite = UserRelation::findOneOppositeRelation($user, $other);
            $this->assertInstanceOf(UserRelation::className(), $opposite);
            $opposite = $relation->opposite;
            $this->assertInstanceOf(UserRelation::className(), $opposite);
            $opposite = UserRelation::find()->opposite($user, $other);
            $this->assertInstanceOf(UserRelation::className(), $opposite);

            $opposites = UserRelation::find()->opposites($user);
            $this->assertEquals(1, count($opposites));
        } else {
            var_dump($relation->rules());
            var_dump($relation->errors);
            $this->fail('Mutual Relation Save Failed.');
        }
        return [$relation, $opposite];
    }

    public function testNew()
    {
        $users = $this->prepareUsers();
        $user = $users[0];
        $other = $users[1];
        $relations = $this->prepareMutualRelationModels($user, $other);
        $this->destroyUsers($users);
    }

    /**
     * @depends testNew
     */
    public function testRemoveOne()
    {
        $users = $this->prepareUsers();
        $user = $users[0];
        $other = $users[1];

        $this->prepareMutualRelationModels($user, $other);
        UserRelation::removeOneRelation($user, $other);

        $relations = $this->prepareMutualRelationModels($user, $other);
        $this->assertEquals(1, $relations[0]->remove());

        $this->destroyUsers($users);
    }

    /**
     * @depends testRemoveOne
     */
    public function testDeregisterOne()
    {
        $users = $this->prepareUsers();
        $user = $users[0];
        $other = $users[1];
        $this->prepareMutualRelationModels($user, $other);
        if ($user->deregister()) {
            $this->assertTrue(true);
            $this->assertTrue($other->deregister());
        } else {
            $this->assertTrue(false);
            var_dump($user->errors);
        }
    }

    /**
     * @depends testDeregisterOne
     */
    public function testFavorite()
    {
        $users = $this->prepareUsers();
        $relations = $this->prepareMutualRelationModels($users[0], $users[1]);
        $favoriteAttribute = $relations[0]->favoriteAttribute;
        $this->assertEquals(0, $relations[0]->$favoriteAttribute);
        $this->assertFalse($relations[0]->isFavorite);
        $relations[0]->isFavorite = true;
        $this->assertTrue($relations[0]->save());
        $this->assertEquals(1, $relations[0]->$favoriteAttribute);
        $this->assertTrue($relations[0]->isFavorite);
        $this->destroyUsers($users);
    }

    /**
     * @depends testFavorite
     */
    public function testSingleRelation()
    {
        $users = $this->prepareUsers();
        $user = $users[0];
        $other = $users[1];
        
        $relation = $this->prepareSingleRelationModels($user, $other);
        $this->destroyUsers($users);
    }

    /**
     *  @depends testSingleRelation
     */
    public function testMutualRelation()
    {
        $users = $this->prepareUsers();
        $user = $users[0];
        $other = $users[1];

        // 测试双向关系类型和重建。
        $relations = $this->prepareMutualRelationModels($user, $other, UserRelation::$mutualTypeNormal);
        $mutualTypeAttribute = $relations[0]->mutualTypeAttribute;
        $this->assertEquals(UserRelation::$mutualTypeNormal, $relations[0]->$mutualTypeAttribute);
        $rguid = $relations[0]->guid;
        $oguid = $relations[1]->guid;
        $rcreatedAt = $relations[0]->createdAtAttribute;
        $rcreatetime = $relations[0]->$rcreatedAt;
        $rupdatedAt = $relations[0]->updatedAtAttribute;
        $rupdatetime = $relations[0]->$rupdatedAt;
        //$this->assertGreaterThanOrEqual(1, $relations[0]->remove());
        sleep(1); //延时一秒，测试修改。
        $relations = $this->prepareMutualRelationModels($user, $other, UserRelation::$mutualTypeSuspend);
        $this->assertEquals($rguid, $relations[0]->guid);
        $this->assertEquals($oguid, $relations[1]->guid);
        $this->assertEquals($rcreatetime, $relations[0]->$rcreatedAt);
        $this->assertNotEquals($rupdatetime, $relations[0]->$rupdatedAt);
        $this->assertEquals(UserRelation::$mutualTypeSuspend, $relations[0]->$mutualTypeAttribute);
        $this->assertGreaterThanOrEqual(1, $relations[0]->remove());

        // 测试上限。
        $this->destroyUsers($users);
    }

    /**
     * @depends testMutualRelation
     */
    public function testRelationGroup()
    {
        // 准备两个用户
        $users = $this->prepareUsers();

        // 准备两个用户之间的双向关系
        $relations = $this->prepareMutualRelationModels($users[0], $users[1]);

        // 第一个用户的主动关系
        $relation = $relations[0];

        // 当前关系组应为空数组
        $groupsAttribute = $relation->multiBlamesAttribute;
        $this->assertEquals('[]', $relation->$groupsAttribute);

        // 当前未分组用户应为 1，即对方
        $members = $relation->getNonGroupMembers();
        $this->assertEquals(1, count($members));

        // 新建一个组，在保存前，当前关系找不到该组。
        $group = $users[0]->create(UserRelationGroup::className(), ['content' => 'classmate']);
        $group1 = $users[0]->create(UserRelationGroup::className(), ['content' => 'relative']);
        $this->assertEmpty($relation->getGroupMembers($group));
        $this->assertEmpty(UserRelation::getGroup($group->guid));
        $this->assertEmpty($relation->getGroupMembers($group1));
        $this->assertEmpty(UserRelation::getGroup($group1->guid));
        if ($group->save()) {
            $this->assertTrue(true);
        } else {
            var_dump($group->errors);
            $this->assertFalse(true);
        }
        if ($group1->save()) {
            $this->assertTrue(true);
        } else {
            var_dump($group1->errors);
            $this->assertFalse(true);
        }
        // 保存后也应当找不到，因为当前关系没有添加任何组。
        $this->assertEmpty($relation->getGroupMembers($group));
        // 不过
        $this->assertNotEmpty(UserRelation::getGroup($group->guid));

        // 添加一个关系组，并获得添加后的关系组数组。
        $relationGroups = $relation->addGroup($group);
        // 测试长度。
        $mbAttribute = $relations[0]->multiBlamesAttribute;
        $this->assertEquals($relations[0]->getGroupsCount() * 39 + 1, strlen($relations[0]->$mbAttribute));

        // 此时应该有 1 个元素，即 1 个组。
        $this->assertEquals(1, count($relationGroups));
        $this->assertEquals($group->guid, $relationGroups[0]);
        $this->assertEquals($group->guid, $relation->groupGuids[0]);

        // 再添加一个组，并获得添加后的关系组数组。
        $relationGroups = $relation->addGroup($group1);
        $this->assertEquals($relations[0]->getGroupsCount() * 39 + 1, strlen($relations[0]->$mbAttribute));

        // 此时应该有 2 个元素，即 2 个组。
        $this->assertEquals(2, count($relationGroups));
        $this->assertEquals($group1->guid, $relationGroups[1]);
        $this->assertEquals($group1->guid, $relation->groupGuids[1]);

        if ($relation->save()) {
            $this->assertTrue(true);
        } else {
            var_dump($relation->errors);
            $this->assertFalse(true);
        }

        $baseQuery = UserRelation::find()->initiators($users[0]->guid)->recipients($users[1]->guid);
        $query = $baseQuery->groups($relation->groupGuids[0]);
        $commandQuery = clone $query;
        echo $commandQuery->createCommand()->getRawSql() . "\n";
        $this->assertEquals(1, count($query->all()));

        $baseQuery = UserRelation::find()->initiators($users[0]->guid)->recipients($users[1]->guid);
        $query = $baseQuery->groups($relation->groupGuids[1]);
        $commandQuery = clone $query;
        echo $commandQuery->createCommand()->getRawSql() . "\n";
        $this->assertEquals(1, count($query->all()));

        $baseQuery = UserRelation::find()->initiators($users[0]->guid)->recipients($users[1]->guid);
        $query = $baseQuery->groups($relation->groupGuids);
        $commandQuery = clone $query;
        echo $commandQuery->createCommand()->getRawSql() . "\n";
        $this->assertEquals(1, count($query->all()));

        $baseQuery = UserRelation::find()->initiators($users[0]->guid)->recipients($users[1]->guid);
        $query = $baseQuery->groups("g");
        $this->assertEquals(0, count($query->all()));

        $baseQuery = UserRelation::find()->initiators($users[0]->guid)->recipients($users[1]->guid);
        $query = $baseQuery->groups();
        $this->assertEquals(0, count($query->all()));

        // 删除成功
        $this->assertGreaterThanOrEqual(1, $group->delete());
        $this->assertGreaterThanOrEqual(1, $group1->delete());
        // 虽然关系组删除了，但不会影响涉及到的关系，所以包含了被删除的关系组的关系，其关系组列表依然包含该关系组。
        // 因此，此时直接获取关系组列表，被删除的组GUID依然在列表中。
        $this->assertNotEmpty($relation->groupGuids);
        // 如果要主动将失效的关系组剔除出关系组列表，可以在获取关系组列表时，强制检查有效性：
        $groups = $relation->getGroupGuids(true);
        $this->assertEmpty($groups); // 此时应该为空。

        $query = UserRelation::find()->groups($groups)->all();
        // 此时未分组关系应该有一个。
        $this->assertEquals(1, count($query));

        // 而且标明列表已经改变了。
        $this->assertTrue($relation->blamesChanged);

        $this->destroyUsers($users);
    }
}
