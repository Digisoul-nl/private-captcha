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

add a display parameter if needed:

- `{{ captcha display="hidden" }}` renders an invisible widget that solves
  itself in the background. The injected script holds the form submission
  until the captcha solution has been computed before posting.
- `{{ captcha display="widget" }}` renders a visible checkbox that the
  visitor must click before a solution is computed. This closes off the
  headless-browser bypass a fully automatic widget is exposed to, at the
  cost of an extra click. The injected script blocks submission and scrolls
  the widget into view until the visitor solves it.


