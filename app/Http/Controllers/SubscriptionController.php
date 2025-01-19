<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $user = $request->user();
        
        try {
            // Créer ou récupérer le client Stripe
            $user->createOrGetStripeCustomer();

            // Souscrire au plan premium
            $subscription = $user->newSubscription('default', env('STRIPE_PREMIUM_PRICE_ID'))
                ->create($request->payment_method_id);
            
            // Mettre à jour le statut de l'utilisateur et les dates
            $user->update([
                'user_plan' => 'premium',
                'user_subscription_status' => 'active',
                'subscription_ends_at' => $subscription->ends_at,
                'trial_ends_at' => $subscription->trial_ends_at,
                // 'stripe_user_customer_id' => $stripeCustomer->id,
                'stripe_user_subscription_id' => $subscription->id
            ]);
            
            return response()->json([
                'message' => 'Subscription successful',
                'subscription' => $subscription
            ]);
            
        } catch (IncompletePayment $exception) {
            return response()->json([
                'error' => 'Payment incomplete',
                'payment_intent' => $exception->payment->id
            ], 402);
        }
    }

    public function cancel(Request $request)
    {
        $user = $request->user();
        
        // Annuler l'abonnement à la fin de la période
        $user->subscription('default')->cancel();
        
        // Mettre à jour le statut
        $user->update([
            'user_subscription_status' => 'canceled'
        ]);
        
        return response()->json([
            'message' => 'Subscription canceled'
        ]);
    }

    public function getPlans()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => config('subscription.plans')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des plans'
            ], 500);
        }
    }
} 