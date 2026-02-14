# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A Statamic CMS addon (`digisoul/private-captcha`) that integrates Private Captcha protection into Statamic forms. PHP 8.2+, Statamic v5.0+.

## Development

No build step, test suite, or linting tools are configured. This is a pure Composer addon.

Install dependencies: `composer install`

Publish config in a Statamic project: `php please vendor:publish --tag=private-captcha-config`

## Architecture

The addon follows Statamic's `AddonServiceProvider` pattern with three core components:

- **ServiceProvider** (`src/ServiceProvider.php`): Registers tags and event listeners with Statamic. Views are namespaced under `privateCaptcha`.
- **Captcha Tag** (`src/Tags/Captcha.php`): Provides two Antlers template tags:
  - `{{ captcha }}` — renders the captcha widget (hidden input + widget div)
  - `{{ captcha:script }}` — injects the JS library and initialization script
- **HandleCaptcha Listener** (`src/Listeners/HandleCaptcha.php`): Listens to `Statamic\Events\FormSubmitted`, verifies the captcha solution server-side using `private-captcha/private-captcha-php`, and throws `ValidationException` on failure.

## Configuration

Two env vars are required: `PRIVATECAPTCHA_KEY` (private API key) and `PRIVATECAPTCHA_SITEKEY` (public site key). Accessed via `config('private-captcha.key')` and `config('private-captcha.sitekey')`.

## Key Conventions

- Namespace: `Digisoul\PrivateCaptcha\`
- Templates use Antlers syntax with inline CSS (no Tailwind/build tools)
- The Captcha tag behaves differently in local vs production when sitekey is missing (throws exception locally, logs warning in production)