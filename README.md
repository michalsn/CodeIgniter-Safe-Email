# CodeIgniter Safe Email
[![Build Status](https://travis-ci.org/michalsn/CodeIgniter-Safe-Email.svg?branch=master)](https://travis-ci.org/michalsn/CodeIgniter-Safe-Email)

How is this working exactly? This class converts all emails on the web page to "special tags", that web scrapers can't read. Most of the web scrapers can't run JavaScript. This class requires special jQuery plugin to convert "special tags" back to email addresses. This is the whole secret.

* Full automation
* No additional inline javascript
* No special helper calls in code

## Installation

Copy `application/hooks/Safe_email.php` to your project.

Add below code to `application/config/hooks.php` file
```php
$hook['post_controller'][] = array(
    'class'    => 'Safe_email',
    'function' => 'initialize',
    'filename' => 'Safe_email.php',
    'filepath' => 'hooks'
);
```

Enable hooks in your `application/config/config.php` file, by setting `$config['enable_hooks']` variable to `TRUE`.

Add jQuery plugin from `assets/js/jQuery.safeEmail.min.js` to your project and load it like this:
```js
$(document).ready(function() {
    $.safeEmail();
});
```
## Configuration options

At this time only available configuration option is identifier for "special tags".

Default value is `ci-safe-email`, but this may be changed in Safe_email class. Just edit `$class_name` variable. If you change it, be aware that you need to initialize `$.safeEmail` call with additional option, like this: `$.safeEmail({className: 'some-new-class-name'})`.

## Limitations

If web scraper is build on something like PhantomJS, this class won't help you.

## Testing

```bash
composer install
./vendor/bin/phpunit
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
