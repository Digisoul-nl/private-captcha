# Changelog

All notable changes to `digisoul/private-captcha` are documented here.

## v1.2.0

### Added

- **`display="widget"` now requires an actual click to solve.** A fully
  automatic widget (the previous behavior for every display mode) computes
  its solution on page load, which headless browsers clear for free without
  ever rendering the page. `{{ captcha display="widget" }}` now renders with
  `data-start-mode="click"`, so the visitor must tick the checkbox before a
  solution exists. The injected script detects the mode from the widget's own
  `data-start-mode` attribute: hidden/auto widgets still block-and-poll for
  their background solution as before, while click widgets just block
  submission and scroll the visitor to the widget with a hint message until
  they solve it. `display="hidden"` (and the default `"auto"`) are
  unaffected. (`src/Tags/Captcha.php`, `resources/views/captcha.antlers.html`,
  `resources/views/captcha-script.html`)

## v1.1.2

### Fixed

- **Verification is now bound to the configured property.** `HandleCaptcha`
  called `verify()` without a sitekey, so the API validated the solution against
  the API key alone rather than against this specific property. A solution
  minted by another property's widget under the same key would therefore be
  accepted. The configured `private-captcha.sitekey` is now passed through,
  which sends the `X-PC-Sitekey` header and scopes verification correctly.
  (`src/Listeners/HandleCaptcha.php`)

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
