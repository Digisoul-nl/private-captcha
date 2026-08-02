<?php

namespace Digisoul\PrivateCaptcha\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use PrivateCaptcha\Client;
use PrivateCaptcha\Exceptions\PrivateCaptchaException;
use Statamic\Events\FormSubmitted;

class HandleCaptcha
{
    /**
     * Handle the event.
     */
    public function handle(FormSubmitted $event): void
    {
        $solution = request()->input('private_captcha_solution');

        if (!$solution) {
            throw ValidationException::withMessages(['captcha' => __('Invalid Captcha.')]);
        }

        try {
            $client = new Client(config('private-captcha.key'));

            // Passing the sitekey binds verification to this property. Without
            // it the API accepts any solution the key can see, so a solution
            // minted by another property's widget would pass here.
            $output = $client->verify($solution, sitekey: config('private-captcha.sitekey'));

            if (!$output->isOK()) {
                Log::warning('PrivateCaptcha verification rejected.', ['code' => (string) $output]);

                throw ValidationException::withMessages(['captcha' => __('Invalid Captcha.')]);
            }
        } catch (PrivateCaptchaException $e) {
            // Covers an empty/invalid solution (SolutionException), API errors
            // (HttpException) and network failures after retries
            // (VerificationFailedException) — return a clean validation error
            // instead of a 500.
            Log::warning('PrivateCaptcha verification failed.', ['exception' => $e->getMessage()]);

            throw ValidationException::withMessages(['captcha' => __('Invalid Captcha.')]);
        }
    }
}
