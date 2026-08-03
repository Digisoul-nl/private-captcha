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

        // "widget" is the widget's own default. The previous default here was
        // "auto", which is not a display mode the widget knows: it kept the
        // value as-is, matched none of hidden/popup/widget, and so rendered a
        // visible widget through an untested code path.
        $display = $this->params->get('display', 'widget');

        // A visible widget only starts solving once the visitor ticks the box.
        // Hidden widgets keep solving in the background, and "popup" is left on
        // auto: it only ever becomes visible through execute(), which the click
        // path never calls, so click mode would leave the form unsubmittable.
        $clickToSolve = $display === 'widget';

        return view('privateCaptcha::captcha', [
            'display' => $display,
            'start_mode' => $clickToSolve ? 'click' : 'auto',
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
