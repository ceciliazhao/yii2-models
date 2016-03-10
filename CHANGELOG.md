## Base Models for Yii 2 Applications

### since 2.0.0 Beta 1 released

- Add: hasParent(), hasAncestor(), getAncestorChain(), getAncestorModels(), getAncestors(), hasCommonAncestor(), getCommonAncestor(), setParent() methods or magic property. (vistart)

### 2.0.0 Beta 1

- Chg: Move components to [vistart/yii2-components](https://github.com/vistart/yii2-components) (vistart)

- Chg: change dependency to [vistart/helpers](https://github.com/vistart/helpers). (vistart)

#### traits/MessageTrait.php

- Add: getInitiator(), getRecipient() and setRecipient() method. (vistart)

#### traits/BlameableTrait.php

- Chg: rename count() method with countOfOwner(). (vistart)
- Add: findAllByIdentityInBatch(), findOneById(), countByIdentity() and getPagination(). (vistart)
- Fix: generate required rules for multi-content-attributes. (vistart)

#### traits/MetaTrait.php

- Add: new. (vistart)

#### traits/EntityQueryTrait.php

- Add: page() condition. (vistart)

#### models/BaseMetaModel.php

- Chg: merge behaviors from BaseBlameableModel. (vistart)
- Add: get() method for retriving meta value. (vistart)
- Chg: use MetaTrait. (vistart)

 ### models/BaseRedisMetaModel.php

- Add: new. (vistart)

#### models/BaseRedisEntityModel.php

- Add: primaryKey() method for specifying primary key with guid or id attribute. (vistart)

#### traits/TimestampTrait.php

- Add: enabledTimestampFields() method for getting enabled fields of timestamp attributes. (vistart)

#### traits/IPTrait.php

- Add: enabledIPFields() method for getting enabled fields of IP attributes. (vistart)

### 2.0.0 Alpha 2

#### traits/IPTrait.php

- Add: `$request` property enables to specify request component ID. (vistart)
- Chg: getWebRequest() method will return web request component, if it is console one, then will return null. (vistart)

#### traits/TimestampTrait.php

- Add: getInitDatetime() event and initDatetime() method. (vistart)
- Add: isInitDatetime() method for judging whether specified attribute is init date & time. (vistart)
- Chg: isInitDatetime() considers the null value is init value. (vistart)
- Add: $initDatetime & $initTimestamp static properties. (vistart)
- Add: offsetDatetime() method for generating current or specified date & time with specified offset. (vistart)
- Add: getUpdatedAt() method, known as magic-property. (vistart)
- Add: expiration features. (vistart)

#### traits/PasswordTrait.php

- Chg: rename `applyNewPassword()` with `applyForNewPassword()`. (vistart)
- Chg: permit to disable the password reset feature. (vistart)
- Add: `eventNewPasswordAppliedFor` event will be triggered when succeeded to apply new password. (vistart) 

#### traits/BlameableTrait.php

- Add: getUpdater() method for getting the user who updated this model recently. (vistart)
- Add: enabledFields() method overriding the parent method for getting the built-in attributes enabled. (vistart)

#### traits/BlameableQueryTrait.php

- Enh: createdBy() and updatedBy() conditions support base user model instance. (vistart)
- Enh: byIdentity condition supports specifying identity. (vistart)

#### traits/EntityQueryTrait.php

- Fix: createdAt() and updatedAt() conditions reference error attributes. (vistart)

#### traits/MessageTrait.php

- Add: new. (vistart)

#### traits/MessageTraitQuery.php

- Add: new. (vistart)

#### traits/MutualQueryTrait.php

- Add: new. (vistart)

#### traits/SelfBlameableTrait.php

- Chg: private self blameable rules name changed. It doesn't matter with logic. (vistart)

#### traits/UserTrait.php

- Chg: create() method can load default value after model was created. (vistart)
- Chg: findOneOrCreate() method's third parameter defaults to null. (vistart)
- Enh: findOneOrCreate() method will take the query condition to the config if $config is null or not a array. (vistart)
- Chg: create() method will unset `class` of config array. (vistart)
- Chg: loadDefaultValue() method executed if it exists. (vistart)

#### traits/IdentityTrait.php

- Chg: onInitStatusAttribute() event will skip to init the status attribute if status was not empty. (vistart)

#### traits/UserRelationTrait.php

- Chg: specify `userClass` property in buildRelation() method. (vistart)
- Add: createGroup(), addOrCreateGroup() and getOrCreateGroup() method. see detail in MultipleBlameableTrait.php (vistart)
- Enh: buildRelation() method will reuse the query's no init model. (vistart)
- Chg: buildOppositeRelation() method will not be skipped if its type is single relation. (vistart)
- Add: `$relationSelf` property, determines whether permits to build self relation. If not, and initiator is same as recipient, null will be given. (vistart)
- Add: getInitiator() and getRecipient() queries and `$initiator`, `$recipient` magic properties. (vistart)
- Chg: buildSuspendRelation() will give null if current type of relation is not mutual. (vistart)

#### traits/UserRelationGroupTrait.php

- Chg: remove add() static method. (vistart)

#### traits/MultipleBlameableTrait.php

- Add: createBlame() method for create blame instance. (vistart)
- Add: addOrCreateBlame() method for add blame or create one before adding if it didn't exist. (vistart)
- Add: getOrCreateBlame() method for get blame or create one before returning if it didn't exist. (vistart)
- Chg: change error message if user is not an instance of [[BaseUserModel]]. (vistart)
- Chg: the limit of blames is 64. (vistart)

#### models/BaseBlameableModel.php

- Add: findByIdentity() method for getting the query with specified identity condition. (vistart)

#### models/BaseMongoEntityModel.php

- Chg: $idAttribute will be assigned '_id' in init() method. (vistart)
- Enh: Defaults to take enabledFields() to attributes(). (vistart)
- Chg: $idAttribute will be always set to '_id'. (vistart)
- Chg: $idAttributeType is auto-increment. (vistart)

#### models/BaseMongoBlameableModel.php

- Add: `init()` method. (vistart)

#### models/BaseRedisEntityModel.php

- Enh: Defaults to take enabledFields() to attributes(). (vistart)

#### models/BaseRedisBlameableModel.php

- Fix: add missing namespace. (vistart)
- Add: `init()` method. (vistart)

#### models/BaseUserRelationModel.php

- Chg: remove `$idAttribute` redeclaration. (vistart)

#### models/BaseUserRelationGroupModel.php

- Fix: add missing initialization of `$queryClass`. (vistart)

#### models/BaseAdditionalAccountModel.php

- Fix: add missing initialization of `$queryClass`. (vistart)

#### models/BaseMongoMessageModel.php

- Add: new. (vistart)

#### models/BaseRedisMessageModel.php

- Add: new. (vistart)

#### queries/BaseMongoEntityQuery.php

- Fix: fix typo in namespace. (vistart)

#### queries/BaseMongoBlameableQuery.php

- Add: new. (vistart)

#### queries/BaseRedisBlameableQuery.php

- Add: new. (vistart)

#### queries/BaseMongoMessageQuery.php

- Add: new. (vistart)

#### queries/BaseRedisMessageQuery.php

- Add: new. (vistart)

#### components/SSOIdentity.php

- Fix: ForbiddenHttpException reference missing. (vistart)
- Add: support specifying MultiDomainsManager component ID. (vistart)
- Chg: rename getMultipleDomainsManager() with getMultiDomainsManager(). (vistart)

#### components/MultipleDomainsManager.php -> MultiDomainsManager.php

- Chg: rename with `MultiDomainsManager`. (vistart)
- Chg: rename config parameter of sub-domain. (vistart)

#### components/MultipleDomainsUrlManager.php -> MultiDomainsUrlManager.php

- Chg: rename with `MultiDomainsUrlManager`. (vistart)

### 2.0.0 under development

- Bug #11: 8 problems. (yuanyuancin)
- Bug #11: deregister() of RegistrationTrait has referenced wrong event names. (yuanyuancin)
- Bug #11: getStatusRules() and setStatusRules() of IdentityTrait has referenced the wrong field _sourceRules. (yuanyuancin)
- Bug #11: onGetCurrentUserGuid() event of BlameableTrait has wrong logic. (yuanyuancin)
- Enh #11: add a passwordHashAttributeLength to determine the length of passwordHash. (yuanyuancin)
- Enh #11: register() of RegistrationTrait has redundant statements in line.58. (yuanyuancin)
- Chg #11: When confirmationAttribute ignored, the confirmationAttribute and confirmTimeAttribute also appeared in rules(). (yuanyuancin)
- Chg #11: onInitConfirmation() event of ConfirmationTrait ignored the non-specified $confirmationAttribute. (yuanyuancin)
- New: Add CHANGELOG.md (vistart)
- New: Add API documents (vistart)
- New: Add Base Models Generators (vistart)