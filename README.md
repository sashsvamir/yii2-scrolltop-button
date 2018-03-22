# Yii2 scrollTop button


## Installation

`composer require sashsvamir/yii2-scrolltop-button:"dev-master"`



## Description

Scroll to top button. No jquery, only native js.



## Using

in view (usually main template):
```php
use sashsvamir\scrollTopButton\ScrollTopButton;
ScrollTopButton::widget([
	'offset' => 1300,      // Scroll length offset (from top) when button appear
	'duration' => 500,     // Duration time of scrolling
	'nativeStyle' => true, // Whether using native botton's styles or user css
]);
```
