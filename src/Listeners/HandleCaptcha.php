<?php

namespace Digisoul\PrivateCaptcha\Listeners;

use Illuminate\Validation\ValidationException;
use PrivateCaptcha\Client;
use PrivateCaptcha\Exceptions\SolutionException;
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
            throw ValidationException::withMessages(['captcha' => 'Invalid Captcha.']);
        }

        try {
            $client = new Client(config('private-captcha.key'));
            $client->verify($solution);
        } catch (SolutionException $e) {
            throw ValidationException::withMessages(['captcha' => 'Invalid Captcha.']);
        }
    }
}
