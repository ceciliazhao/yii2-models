## Base Models for Yii 2 Applications

### since 2.0.0-alpha1 released

#### traits/UserTrait.php

- Chg: create() method can load default value after model was created. (vistart)

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