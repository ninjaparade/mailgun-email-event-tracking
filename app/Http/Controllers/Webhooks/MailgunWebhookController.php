<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Http\Middleware\Webhooks\MailgunWebhook;
use App\Message;
use Illuminate\Http\Request;

class MailgunWebhookController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->middleware(MailgunWebhook::class);
    }

    /**
     * Handles the Stripe Webhook call.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        $data = $request->get('event-data');

        $message_id = $data['message']['headers']['message-id'];

        if ($email = Message::whereMessageId($message_id)->first()) {
            if ($data['event'] === 'opened' || $data['event'] === 'clicked') {
                $email->increment($data['event']);
            }

            if ($data['event'] === 'delivered' || $data['event'] === 'failed') {
                $email->update(["{$data['event']}_at" => now()]);
            }
        }
    }
}
