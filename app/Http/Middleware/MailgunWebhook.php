<?php

namespace App\Http\Middleware\Webhooks;

use Closure;
use Illuminate\Http\Response;

/**
 * Validate Mailgun Webhooks.
 *
 * @see https://documentation.mailgun.com/user_manual.html#securing-webhooks
 */
class MailgunWebhook
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->isMethod('post')) {
            abort(Response::HTTP_FORBIDDEN, 'Only POST requests are allowed.');
        }

        if ($this->verify($request)) {
            return $next($request);
        }

        abort(Response::HTTP_FORBIDDEN);
    }

    /**
     * Build the signature from POST data.
     *
     * @see https://documentation.mailgun.com/user_manual.html#securing-webhooks
     *
     * @param  $request The request object
     *
     * @return string
     */
    private function buildSignature($request)
    {
        return hash_hmac(
            'sha256',
            sprintf('%s%s', $request->input('timestamp'), $request->input('token')),
            config('services.mailgun.secret')
        );
    }

    public function verify($request)
    {
        $token = $request->input('signature.token');
        $timestamp = $request->input('signature.timestamp');
        $signature = $request->input('signature.signature');

        if (abs(time() - $timestamp) > 15) {
            return false;
        }

        return hash_hmac('sha256', $timestamp.$token, config('services.mailgun.secret')) === $signature;
    }
}
