<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response as IlluminateResponse;
use Symfony\Component\HttpFoundation\Response;

class PromotionMW
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Don't inject promo banner into AJAX/JSON requests (partial responses)
        if ($request->ajax() || $request->wantsJson()) {
            return $response;
        }

        if (! $response instanceof IlluminateResponse) {
            return $response;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');
        if ($contentType !== '' && stripos($contentType, 'text/html') === false) {
            return $response;
        }

        $html = $response->getContent();
        if (! is_string($html) || $html === '' || str_contains($html, 'promo-ad-banner')) {
            return $response;
        }

        $banner = <<<'HTML'
<div id="promo-ad-banner" style="background:#212529;color:#fff;border-bottom:1px solid rgba(255,255,255,.12);">
    <div style="max-width:1140px;margin:0 auto;padding:10px 16px;display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:space-between;">
        <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
            <span style="background:#ffc107;color:#212529;font-weight:700;border-radius:999px;padding:4px 10px;letter-spacing:.2px;">50% OFF</span>
            <span style="font-weight:600;">May 5, 2026</span>
            <span style="opacity:.9;">Don&rsquo;t miss out on this amazing promotion!</span>
        </div>
        <div style="opacity:.75;font-size:13px;">Limited-time offer</div>
    </div>
</div>
HTML;

        if (preg_match('/<body\b[^>]*>/i', $html) === 1) {
            $html = preg_replace('/<body\b[^>]*>/i', '$0' . $banner, $html, 1);
        } else {
            $html = $banner . $html;
        }

        $response->setContent($html);

        return $response;
    }
}
