<?php

namespace Digisoul\PrivateCaptcha\Tags;

use Statamic\Tags\Tags;

class Captcha extends Tags
{
    /**
     * The {{ captcha }} tag.
     *
     * @return string|array
     */
    public function index()
    {
        $sitekey = config('private-captcha.sitekey');

        if (!$sitekey) {
            if (app()->environment('local')) {
                throw new \RuntimeException('PrivateCaptcha sitekey is not configured.');
            }
            \Log::warning('PrivateCaptcha sitekey is missing. CAPTCHA will not function properly.');
        }

        return view('privateCaptcha::captcha', [
            'display' => $this->params->get('display', 'auto'),
            'sitekey' => $sitekey,
        ]);
    }

    public function script() {
        return view('privateCaptcha::captcha-script');
    }
}
