Models for Yii 2
================
The Models for Yii 2 Applications

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist vistart/yii2-models "*"
```

or add

```
"vistart/yii2-models": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
use vistart\Models\BaseEntityModel;

class Example extends BaseEntityModel
{
...
}
```

