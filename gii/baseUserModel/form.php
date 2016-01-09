<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link http://vistart.name/
 * @copyright Copyright (c) 2015 vistart
 * @license http://vistart.name/license/
 */

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\form\Generator */

echo $form->field($generator, 'tableName');
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'ns');
echo $form->field($generator, 'baseClass');
echo $form->field($generator, 'db');
echo $form->field($generator, 'guidAttribute');
echo $form->field($generator, 'idAttribute');
echo $form->field($generator, 'idAttributeType')->dropDownList($generator->idAttributeTypes);
echo $form->field($generator, 'passwordHashAttribute');
echo $form->field($generator, 'createdAtAttribute');
echo $form->field($generator, 'updatedAtAttribute');
echo $form->field($generator, 'enableIP')->checkbox();
echo $form->field($generator, 'ipAttribute1');
echo $form->field($generator, 'ipAttribute2');
echo $form->field($generator, 'ipAttribute3');
echo $form->field($generator, 'ipAttribute4');
echo $form->field($generator, 'ipTypeAttribute');
echo $form->field($generator, 'authKeyAttribute');
echo $form->field($generator, 'accessTokenAttribute');
echo $form->field($generator, 'passwordResetTokenAttribute');
echo $form->field($generator, 'statusAttribute');
echo $form->field($generator, 'sourceAttribute');
echo $form->field($generator, 'useTablePrefix')->checkbox();
echo $form->field($generator, 'generatePresettingRules')->checkbox();
echo $form->field($generator, 'generatePresettingBehaviors')->checkbox();
echo $form->field($generator, 'generateRelations')->checkbox();
echo $form->field($generator, 'generateLabelsFromComments')->checkbox();
echo $form->field($generator, 'generateQuery')->checkbox();
echo $form->field($generator, 'queryNs');
echo $form->field($generator, 'queryClass');
echo $form->field($generator, 'queryBaseClass');
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
