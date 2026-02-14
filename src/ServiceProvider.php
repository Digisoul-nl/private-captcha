<?php

namespace Digisoul\PrivateCaptcha;

use Digisoul\PrivateCaptcha\Tags\Captcha;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $viewNamespace = 'privateCaptcha';

    protected $tags = [
        Captcha::class
    ];

    protected $listen = [
        \Statamic\Events\FormSubmitted::class => [Listeners\HandleCaptcha::class],
    ];

}
