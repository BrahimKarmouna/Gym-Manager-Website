<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CurrentUserController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\InsuranceController;
use App\Http\Controllers\Api\InsurancePlansController;
use App\Http\Controllers\Api\OtherBrowserSessionsController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\ProfilePhotoController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\RevenueController;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;
use Stripe\PaymentIntent;
use Stripe\Stripe;


Route::get('/revenue/monthly', [RevenueController::class, 'monthlyRevenue']);

Route::prefix('insurance')->group(function () {
  // Route to get all insurance plans
  Route::get('/', [InsuranceController::class, 'index']);

  // Route to store new insurance
  Route::post('/', [InsuranceController::class, 'store']);
});


Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('auth:sanctum');

// clients
Route::get('/clients', ClientController::class . '@index');
Route::get('/clients/{id}', ClientController::class . '@show');
Route::post('/clients', ClientController::class . '@store');
Route::put('/clients/{id}', ClientController::class . '@update');
Route::delete('/clients/{id}', ClientController::class . '@destroy');
Route::get('/clients/search', [ClientController::class, 'search']);

// payments
Route::get('/payments', PaymentController::class . '@index');
Route::get('/payments/{id}', PaymentController::class . '@show');
Route::post('/payments', PaymentController::class . '@store');
Route::put('/payments/{id}', PaymentController::class . '@update');
Route::delete('/payments/{id}', PaymentController::class . '@destroy');


// plans

//insurance

// Route::get('/insurance', [App\Http\Controllers\InsuranceController::class, 'index']);
// Route::post('/insurance', [App\Http\Controllers\InsuranceController::class, 'store']);


Route::get('/plans', [PlanController::class, 'index']);
Route::get('/plans/{id}', [PlanController::class, 'show']);
Route::post('/plans', [PlanController::class, 'store']);
Route::put('/plans/{id}', [PlanController::class, 'update']);
Route::delete('/plans/{id}', [PlanController::class, 'destroy']);





// Insurance Plans Routes
Route::prefix('insurance-plans')->group(function () {
  // Get all insurance plans
  Route::get('/', [InsurancePlansController::class, 'index']);

  // Get a specific insurance plan
  Route::get('/{id}', [InsurancePlansController::class, 'getPlanById']);

  // Create a new insurance plan
  Route::post('/', [InsurancePlansController::class, 'store']);

  // Update an existing insurance plan
  Route::put('/{id}', [InsurancePlansController::class, 'updatePlan']);

  // Delete an insurance plan
  Route::delete('/{id}', [InsurancePlansController::class, 'destroy']);
});

// Insurance Routes (for creating a client's insurance and associating them with a plan)
Route::prefix('insurance')->group(function () {
  // Get all insurances (with client details)
  Route::get('/', [InsuranceController::class, 'index']);

  // Get a specific insurance (for a client and associated plan)
  Route::get('/{id}', [InsuranceController::class, 'show']);

  // Create a new insurance for a client (associate client with a plan)
  Route::post('/', [InsuranceController::class, 'store']);

  // Update an existing insurance
  Route::put('/{id}', [InsuranceController::class, 'update']);

  // Delete an insurance
  Route::delete('/{id}', [InsuranceController::class, 'destroy']);
});

//payment
Route::prefix('payments')->group(function () {
  // Get all payments
  Route::get('/', [PaymentController::class, 'index']);

  // Get a specific payment
  Route::get('/{id}', [PaymentController::class, 'show']);

  // Create a new payment
  Route::post('/', [PaymentController::class, 'store']);

  // Update an existing payment
  Route::put('/{id}', [PaymentController::class, 'update']);

  // Delete a payment
  Route::delete('/{id}', [PaymentController::class, 'destroy']);
});
Route::get('test', [UserProfileController::class, 'index'])->name('test.page');
// Auth
Route::name('api.')
  ->middleware(['auth:sanctum'])
  ->group(function () {
    Route::get('me', [UserProfileController::class, 'me'])->name('auth.user');

    Route::get('/user/profile', [UserProfileController::class, 'show'])->name('profile.show');

    // Delete other browser sessions
    Route::delete('/user/other-browser-sessions', [OtherBrowserSessionsController::class, 'destroy'])
      ->name('other-browser-sessions.destroy');

    // Delete profile photo
    Route::delete('/user/profile-photo', [ProfilePhotoController::class, 'destroy'])
      ->name('current-user-photo.destroy');

    // Delete profile
    Route::delete('/user', [CurrentUserController::class, 'destroy'])
      ->name('current-user.destroy');

    //! Categories
    Route::apiResource('categories', CategoryController::class);
  });
  // // New Clients This Month
  // Route::get('/clients/new-this-month', [ClientController::class, 'newClientsThisMonth']);

  // // Active Subscriptions
  // Route::get('/subscriptions/active', [SubscriptionController::class, 'activeSubscriptions']);

  // // Expired Subscriptions
  // Route::get('/subscriptions/expired', [SubscriptionController::class, 'expiredSubscriptions']);




//! stripe payments
Route::post('/charge', function (Request $request) {
  $user = $request->user();
  Stripe::setApiKey(env('STRIPE_SECRET'));

  try {
    $paymentIntent = PaymentIntent::create([
      'amount' => 2000, // Amount in cents (e.g., $20.00)
      'currency' => 'usd',
      'payment_method' => $request->payment_method_id,
      'confirm' => true,
    ]);

    return response()->json([
      'success' => true,
      'paymentIntent' => $paymentIntent,
    ]);
  } catch (\Exception $e) {
    return response()->json([
      'success' => false,
      'message' => $e->getMessage(),
    ], 500);
  }
});
Route::get('/checkout-success', function (Request $request) {

  $session = Cashier::stripe()->checkout->sessions->retrieve($request->get('session_id'));

  if ($session->payment_status !== 'paid') {
    return response()->json([
      'message' => 'Payment issue has been occured',
    ]);
  }

  /** @var App\Models\User $user */
  $user = $request->user();

  dd($session->metadata['order_id']);

  $paymentMethod = $user->paymentMethods('', $session->payment_intent);

  return response()->json([
    'session' => $session,
    'payments' => $paymentMethod,
  ]);

})->name('checkout.success');

Route::get('/checkout-canceled', function (Request $request) {
  return $request;
})->name('checkout.canceled');

Route::get('/charge-checkout', function (Request $request) {

  $product = [
    'name' => 'T-shirt Nike',
    'amount' => 200
  ];

  return $request->user()->checkoutCharge($product['amount'], $product['name'], 5, [
    'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => route('checkout.success'),
  ]);
});


Route::middleware(['auth:sanctum'])
  ->post('/create-checkout-session', function (Request $request) {
    /** @var App\Models\User */
    $user = $request->user();

    $checkoutSession = $user
      ->checkout(
        items: [
          [
            'price_data' => [
              'currency' => 'USD',
              'unit_amount' => 1000,

              'product_data' => [
                'name' => 'Send me money!!!'
              ]
            ],
            'quantity' => 1,
          ],

          [
            'price_data' => [
              'currency' => 'USD',
              'unit_amount' => 2100,

              'product_data' => [
                'name' => 'Send me money 2!!!'
              ]
            ],
            'quantity' => 1,
          ],

          // [
          //   'price' => 2000,
          //   'quantity' => 1,
          //   // 'product_data' => [
          //   //   'name' => 'Send me money!!!'
          //   // ]
          // ]
        ],

        // name: 'T-Shirt',
        // amount: 1000,
        sessionOptions: [
          'ui_mode' => 'embedded',
          'mode' => 'payment',
          'metadata' => [
            'order_id' => 1
          ],
          'return_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
          // 'cancel_url' => route('checkout.success'),
        ],

        // name: 'T-Shirt',
        // sessionOptions: [
        //   'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
        //   'cancel_url' => route('checkout.success'),
        // ]
      )->asStripeCheckoutSession();

    return [
      'client_secret' => $checkoutSession->client_secret
    ];
  });
