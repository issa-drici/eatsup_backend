<?php

namespace App\Services\Discord;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class DiscordNotification
{
    public static function send(string $channel, string $message): void
    {
        $webhook = Config::get("discord.webhooks.{$channel}");
        
        if (!$webhook) {
            throw new \Exception("Canal Discord non configuré : {$channel}");
        }

        Http::post($webhook, [
            'content' => $message
        ]);
    }
} 