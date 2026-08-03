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

- `{{ captcha }}` and `{{ captcha display="widget" }}` render a visible
  checkbox that the visitor must tick before a solution is computed. That
  makes an automated submission more expensive than a fully automatic widget,
  at the cost of an extra click. The injected script blocks submission and
  scrolls the widget into view until the visitor ticks it, and then waits for
  the solution before posting.
- `{{ captcha display="hidden" }}` renders an invisible widget that solves
  itself in the background. The injected script holds the form submission
  until the captcha solution has been computed before posting.

`display="popup"` is passed through to the widget but is not covered by the
injected script beyond the hidden-widget handling, and is untested here.


