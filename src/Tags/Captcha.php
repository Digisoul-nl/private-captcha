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

        // Visible widgets only start solving once the visitor ticks the box;
        // hidden ones keep solving in the background. The widget documents
        // "click" for popup mode too, since it renders on interaction anyway.
        $clickToSolve = in_array($display, ['widget', 'popup'], true);

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
