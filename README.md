# Private Captcha

> Protect your form using private captcha

## How to Install

You can install this addon via Composer:

``` bash
composer require digisoul/private-captcha
```

if you want to customize the .env variable names you can publish the config file:

``` bash
php please vendor:publish --tag=private-captcha-config
```
otherwise update your .env to include the following values:

```
PRIVATECAPTCHA_KEY=
PRIVATECAPTCHA_SITEKEY=
```

## How to Use



add the {{ captcha:script }} in the head

add the {{ captcha }} tag in your form

add a display parameter if needed


