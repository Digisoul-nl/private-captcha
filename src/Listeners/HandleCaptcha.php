<?php

namespace Digisoul\PrivateCaptcha\Listeners;

use Illuminate\Validation\ValidationException;
use PrivateCaptcha\Exceptions\SolutionException;
use Statamic\Events\FormSubmitted;
use PrivateCaptcha\Client;

class HandleCaptcha
{
    private Client $client;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->client = new Client(config('private-captcha.key'));
    }

    /**
     * Handle the event.
     */
    public function handle(FormSubmitted $event): void
    {
        $solution = request()->input('private_captcha_solution');

        if(!$solution) {
            throw ValidationException::withMessages(['Invalid Captcha.']);
        }

        try {
            $this->client->verify($solution);
            return;

        } catch (SolutionException $e) {
            throw ValidationException::withMessages(['Invalid Captcha.']);
        }
    }
}
