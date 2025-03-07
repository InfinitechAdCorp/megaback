<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription as WebPushSubscription;

class PushNotificationController extends Controller
{
    /**
     * Save the subscription from the frontend to the database.
     */
    public function saveSubscription(Request $request)
    {
        $request->validate([
            'subscription' => 'required|array',
        ]);

        // Store the subscription data in the database
        Subscription::create([
            'subscription' => $request->subscription, // No need for json_encode()
        ]);

        return response()->json(['message' => 'Subscription saved successfully']);
    }

    /**
     * Send push notifications to all stored subscriptions.
     */
    public function sendNotification()
    {
        // Retrieve VAPID keys from .env
        $webPush = new WebPush([
            'VAPID' => [
                'subject' => 'mailto:example@yourdomain.com', // Change this to your contact email
                'publicKey' => env('VAPID_PUBLIC_KEY'),
                'privateKey' => env('VAPID_PRIVATE_KEY'),
            ],
        ]);

        // Get all subscriptions
        $subscriptions = Subscription::all();
        if ($subscriptions->isEmpty()) {
            return response()->json(['message' => 'No subscriptions found'], 404);
        }

        // Create and queue notifications
        foreach ($subscriptions as $subscription) {
            $subscriptionData = $subscription->subscription; // Laravel auto-casts

            if (!isset($subscriptionData['endpoint'], $subscriptionData['keys']['p256dh'], $subscriptionData['keys']['auth'])) {
                continue; // Skip if data is missing
            }

            $webPushSubscription = WebPushSubscription::create([
                'endpoint' => $subscriptionData['endpoint'],
                'keys' => [
                    'p256dh' => $subscriptionData['keys']['p256dh'],
                    'auth' => $subscriptionData['keys']['auth'],
                ],
            ]);

            $webPush->queueNotification(
                $webPushSubscription,
                json_encode([
                    'title' => 'New Notification',
                    'body' => 'You have a new message!',
                    'icon' => '/icons/icon-192x192.png',
                ])
            );
        }

        // Send all queued notifications and remove invalid subscriptions
        foreach ($webPush->flush() as $report) {
            if (!$report->isSuccess()) {
                $endpoint = $report->getRequest()->getUri();
                Subscription::whereJsonContains('subscription->endpoint', $endpoint)->delete();
            }
        }

        return response()->json(['message' => 'Notifications sent successfully']);
    }
}
