<?php

namespace Digisoul\PrivateCaptcha\Tags;

use Illuminate\Support\Facades\Log;
use Statamic\Tags\Tags;

class Captcha extends Tags
{
    /**
     * The {{ captcha }} tag.
     */
    public function index()
    {
        $sitekey = config('private-captcha.sitekey');

        if (!$sitekey) {
            if (app()->environment('local')) {
                throw new \RuntimeException('PrivateCaptcha sitekey is not configured.');
            }
            Log::warning('PrivateCaptcha sitekey is missing. CAPTCHA will not function properly.');
        }

        $display = $this->params->get('display', 'auto');

        return view('privateCaptcha::captcha', [
            'display' => $display,
            'start_mode' => $display === 'widget' ? 'click' : 'auto',
            'sitekey' => $sitekey,
        ]);
    }

    /**
     * The {{ captcha:script }} tag.
     */
    public function script()
    {
        return view('privateCaptcha::captcha-script');
    }
}
