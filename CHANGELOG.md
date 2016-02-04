## Base Models for Yii 2 Applications

### since 2.0.0-alpha1 released

#### traits/IPTrait.php

- Add: `$request` property enables to specify request component ID. (vistart)
- Chg: getWebRequest() method will return web request component, if it is console one, then will return null. (vistart)

#### traits/BlameableTrait.php

- Add: getUpdater() method for getting the user who updated this model recently. (vistart)

#### traits/BlameableQueryTrait.php

- Enh: createdBy() and updatedBy() conditions support base user model instance. (vistart)
- Enh: byIdentity condition supports specifying identity. (vistart)

#### traits/EntityQueryTrait.php

- Fix: createdAt() and updatedAt() conditions reference error attributes. (vistart)

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

#### traits/MultipleBlameableTrait.php

- Add: createBlame() method for create blame instance. (vistart)
- Add: addOrCreateBlame() method for add blame or create one before adding if it didn't exist. (vistart)
- Add: getOrCreateBlame() method for get blame or create one before returning if it didn't exist. (vistart)

#### models/BaseBlameableModel.php

- Add: findByIdentity() method for getting the query with specified identity condition. (vistart)

#### models/BaseMongoEntityModel.php

- Chg: $idAttribute will be assigned '_id' in init() method. (vistart)
- Enh: Defaults to take enabledFields() to attributes(). (vistart)

#### models/BaseRedisEntityModel.php

- Enh: Defaults to take enabledFields() to attributes(). (vistart)

#### models/BaseRedisBlameableModel.php

- Fix: add missing namespace. (vistart)

#### queries/BaseMongoEntityQuery.php

- Fix: fix typo in namespace. (vistart)

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