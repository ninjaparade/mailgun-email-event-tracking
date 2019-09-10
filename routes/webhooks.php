<?php
Route::group([
    'namespace' => 'Webhooks',
], function () {
    Route::post('mailgun', 'MailgunWebhookController');
});
