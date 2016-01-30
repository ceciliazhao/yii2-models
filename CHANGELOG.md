## Base Models for Yii 2 Applications

### since 2.0.0-alpha1 released

#### trait/BlameableQueryTrait.php

- Enh: createdBy() and updatedBy() conditions support base user model instance. (vistart)

#### trait/EntityQueryTrait.php

- Fix: createdAt() and updatedAt() conditions reference error attributes. (vistart)

#### trait/SelfBlameableTrait.php

- Chg: private self blameable rules name changed. It doesn't matter with logic. (vistart)

#### traits/UserTrait.php

- Chg: create() method can load default value after model was created. (vistart)
- Chg: findOneOrCreate() method's third parameter defaults to null. (vistart)
- Enh: findOneOrCreate() method will take the query condition to the config if $config is null or not a array. (vistart)

#### models/BaseMongoEntityModel.php

- Chg: $idAttribute will be assigned '_id' in init() method. (vistart)
- Enh: Defaults to take enabledFields() to attributes(). (vistart)

#### models/BaseRedisEntityModel.php

- Enh: Defaults to take enabledFields() to attributes(). (vistart)

#### models/BaseRedisBlameableModel.php

- Fix: add missing namespace. (vistart)

#### queries/BaseMongoEntityQuery.php

- Fix: fix typo in namespace. (vistart)

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