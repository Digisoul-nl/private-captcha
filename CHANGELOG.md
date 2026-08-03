# Changelog

All notable changes to `digisoul/private-captcha` are documented here.

## v2.0.1

### Fixed

- **A widget that fails no longer posts its error sentinel and blames the
  visitor.** When the puzzle cannot be loaded — a network problem, a blocked
  request, a property the site's domain is not allowed to use — the widget does
  not leave its solution field empty. It writes a short error sentinel
  (`AQMAAAAAAA==.`) into it. `solutionValue()` only tested for a non-empty
  field, so the submit guard read that sentinel as "already solved", released
  the POST immediately, and the server answered with a flat "Invalid Captcha."
  for a visitor who had ticked the box and done nothing wrong. There was no way
  to recover: every retry produced the same sentinel and the same rejection.

  Solving is now judged by the widget's `privatecaptcha:error` event rather
  than by the field alone, so a failed widget never counts as solved. On submit
  the form is held back and a distinct message — "Verification is unavailable
  right now. Please reload the page and try again." — is shown next to the
  widget instead of spending a round-trip to be told the solution was invalid.
  Ticking the box again clears the failure, so a widget that recovers is not
  held against the visitor. (`resources/views/captcha-script.html`,
  `resources/views/captcha.antlers.html`)

  The same check also applies while a submission is being held: a widget that
  gives up mid-wait now stops the wait and shows the message, where before it
  released the sentinel as if it were a solution.

  This has been present since the poll-and-release path was introduced in
  v1.1.1; it is not specific to the click widgets added in v2.0.0.

  **Template note:** the message is a new `[data-captcha-error]` element in the
  captcha view. Sites that have published their own copy of
  `resources/views/vendor/private-captcha/captcha.antlers.html` need to add it,
  or the submit is still blocked but silently.

## v2.0.0

### Changed

- **Visible widgets now require an actual click to solve, and are the
  default.** A fully automatic widget (the previous behavior for every display
  mode) computes its solution on page load, so a scripted client gets one
  without ever rendering the page. `{{ captcha display="widget" }}` now renders
  with `data-start-mode="click"`, so the visitor has to tick the checkbox
  before a solution is computed. That raises the cost of an automated
  submission — it now takes real DOM interaction — rather than ruling one out.
  The injected script detects the mode from the widget's own
  `data-start-mode` attribute: automatic widgets still block-and-poll for
  their background solution as before, while click widgets block submission and
  point the visitor at the widget until they tick it. `display="hidden"` is
  unaffected. (`src/Tags/Captcha.php`, `resources/views/captcha.antlers.html`,
  `resources/views/captcha-script.html`)

  **This is a breaking behavior change for every site that does not set
  `display="hidden"`.** The tag's default `display` was `"auto"`, which is not
  a display mode the widget recognises — it kept the value verbatim, matched
  none of `hidden`/`popup`/`widget`, and so rendered a visible widget through
  an untested path, while still solving automatically. The default is now
  `"widget"`, the widget's own default, which means a plain `{{ captcha }}`
  also requires the extra click. Sites that want the old hands-off behavior
  should switch to `{{ captcha display="hidden" }}`.

  `display="popup"` stays on `data-start-mode="auto"`. A popup only becomes
  visible via `execute()`, which the click path never calls, so click mode
  would leave the visitor with no widget to tick and no way to submit.

### Fixed

- **Submitting straight after ticking the box no longer stalls the form.** The
  proof-of-work still runs for a moment after the visitor ticks the checkbox,
  during which the solution field is empty. The click path treated that as
  "not solved yet", showed the "confirm you are not a robot" hint to a visitor
  who had just confirmed exactly that, and never released the submission once
  the solution arrived — the visitor had to press submit a second time. The
  script now tracks the widget's `privatecaptcha:checked` and
  `privatecaptcha:start` events and, for a visitor who has already ticked,
  waits for the solution using the same poll-and-release path as the automatic
  widgets. The hint is shown only when the box genuinely has not been ticked.
  (`resources/views/captcha-script.html`)
- **The submit hold no longer times out while a click widget is still
  solving.** The 8s budget was written for automatic widgets, which start
  solving on page load or first focus and have therefore nearly always
  finished by the time the visitor submits. A click widget starts when the box
  is ticked, so the entire proof-of-work has to fit in that budget — on a
  low-end phone it does not, and the form was posted with an empty solution
  and rejected as "Invalid Captcha." for a visitor who did everything right.
  Click widgets now get 30s; automatic widgets keep the 8s they had. The hold
  is also released straight away on the widget's `privatecaptcha:error` event,
  so a widget that gives up does not keep the form waiting out the full
  budget. (`resources/views/captcha-script.html`)
- **Repeated submit clicks no longer stack polling timers.** Pressing submit
  again while a submission was being held started a second interval, so the
  form could be submitted more than once. The poll is now started at most once
  per form. (`resources/views/captcha-script.html`)

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
