# Changelog

All notable changes to `digisoul/private-captcha` are documented here.

## v1.1.1

### Fixed

- **Hidden widgets no longer submit an empty solution.** With a hidden widget
  (`data-display-mode="hidden"`) the captcha only writes its solution into the
  form field after `execute()` is called. The submit handler previously called
  `execute()` but let the native POST fire immediately, so the form was sent
  with an empty `private_captcha_solution` and the server rejected every
  submission as "Invalid Captcha.". The handler now blocks the submission, runs
  `execute()`, and polls until the solution field is populated (with an 8s
  safety timeout) before submitting. (`resources/views/captcha-script.html`)
- **Server now checks the verification result.** `HandleCaptcha` called
  `verify()` but ignored the returned `VerifyOutput`, so any non-empty solution
  passed — the captcha was effectively bypassable. It now checks
  `$output->isOK()` and rejects non-OK results. It also catches the base
  `PrivateCaptchaException`, so API (`HttpException`) and network
  (`VerificationFailedException`) errors return a clean validation error instead
  of a 500. Failures are logged for observability. (`src/Listeners/HandleCaptcha.php`)
