<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class GenerateVapidKeys extends Command
{
    protected $signature = 'generate:vapid-keys';
    protected $description = 'Generate VAPID keys for Web Push Notifications';

    public function handle()
    {
        $keys = VAPID::createVapidKeys();

        // Update .env file
        file_put_contents(base_path('.env'), preg_replace(
            [
                '/^VAPID_PUBLIC_KEY=.*/m',
                '/^VAPID_PRIVATE_KEY=.*/m',
            ],
            [
                "VAPID_PUBLIC_KEY={$keys['publicKey']}",
                "VAPID_PRIVATE_KEY={$keys['privateKey']}",
            ],
            file_get_contents(base_path('.env'))
        ));

        $this->info('VAPID keys generated and saved to .env file.');
    }
}
