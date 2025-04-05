<?php

namespace App\Http\Controllers;

use App\Services\StripeService;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle Stripe webhook requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handleStripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        
        if (empty($sigHeader)) {
            return response()->json(['error' => 'Stripe signature header is missing'], 400);
        }
        
        try {
            $stripeService = new StripeService();
            $success = $stripeService->handleWebhook($payload, $sigHeader);
            
            if (!$success) {
                return response()->json(['error' => 'Failed to process webhook'], 500);
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Handle PayPal webhook requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handlePayPalWebhook(Request $request)
    {
        $payload = $request->getContent();
        $headers = $request->headers->all();
        
        try {
            $paypalService = new PayPalService();
            $success = $paypalService->handleWebhook($payload, $headers);
            
            if (!$success) {
                return response()->json(['error' => 'Failed to process webhook'], 500);
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('PayPal webhook error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}