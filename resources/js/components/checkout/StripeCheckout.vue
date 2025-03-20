<template>
  <div class="stripe-checkout-container">
    <!-- Loading overlay -->
    <div v-if="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <q-spinner color="primary" size="3em" />
      <p class="text-white ml-4 text-lg">Processing payment...</p>
    </div>

    <!-- Checkout Form -->
    <div class="bg-white rounded-xl shadow-xl p-6 md:p-8 max-w-3xl mx-auto">
      <div class="flex items-center justify-between border-b pb-4 mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Secure Checkout</h2>
        <div class="flex gap-2">
          <img src="/images/payment/visa.svg" alt="Visa" class="h-8">
          <img src="/images/payment/mastercard.svg" alt="Mastercard" class="h-8">
          <img src="/images/payment/amex.svg" alt="Amex" class="h-8">
        </div>
      </div>

      <!-- Order Summary -->
      <div class="mb-6 p-4 bg-gray-50 rounded-lg">
        <h3 class="text-lg font-semibold mb-3 text-gray-800">Order Summary</h3>
        <div v-for="item in cartStore.items" :key="item.id" class="flex justify-between mb-2">
          <span>{{ item.name }} × {{ item.quantity }}</span>
          <span>${{ cartStore.formatPrice(item.price * item.quantity) }}</span>
        </div>
        <div class="border-t border-gray-200 my-3 pt-3">
          <div class="flex justify-between mb-1">
            <span>Subtotal</span>
            <span>${{ cartStore.formatPrice(cartStore.subtotal) }}</span>
          </div>
          <div v-if="cartStore.couponDiscount > 0" class="flex justify-between mb-1 text-green-600">
            <span>Discount</span>
            <span>-${{ cartStore.formatPrice(cartStore.discount) }}</span>
          </div>
          <div class="flex justify-between mb-1">
            <span>Tax</span>
            <span>${{ cartStore.formatPrice(cartStore.tax) }}</span>
          </div>
          <div class="flex justify-between font-bold text-lg mt-2">
            <span>Total</span>
            <span>${{ cartStore.formatPrice(cartStore.total) }}</span>
          </div>
        </div>
      </div>

      <!-- Stripe Elements -->
      <div class="mb-6">
        <h3 class="text-lg font-semibold mb-3 text-gray-800">Payment Information</h3>
        <div 
          id="card-element" 
          class="p-4 border border-gray-300 rounded-lg bg-white"
          :class="{'border-red-500': cardError}"
        ></div>
        <div v-if="cardError" class="text-red-500 text-sm mt-1">{{ cardError }}</div>
      </div>

      <!-- Shipping Information -->
      <div class="mb-6">
        <h3 class="text-lg font-semibold mb-3 text-gray-800">Shipping Address</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
            <input 
              v-model="shippingDetails.firstName" 
              type="text" 
              class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
            <input 
              v-model="shippingDetails.lastName" 
              type="text" 
              class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
            >
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <input 
              v-model="shippingDetails.address" 
              type="text" 
              class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
            <input 
              v-model="shippingDetails.city" 
              type="text" 
              class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
            <input 
              v-model="shippingDetails.postalCode" 
              type="text" 
              class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">State/Province</label>
            <input 
              v-model="shippingDetails.state" 
              type="text" 
              class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
            <select 
              v-model="shippingDetails.country" 
              class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
            >
              <option value="US">United States</option>
              <option value="CA">Canada</option>
              <option value="GB">United Kingdom</option>
              <option value="AU">Australia</option>
              <!-- Add more countries as needed -->
            </select>
          </div>
        </div>
      </div>

      <!-- Submit Button -->
      <button 
        @click="submitPayment"
        :disabled="loading || !isFormValid"
        class="w-full py-3 px-4 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <span v-if="!loading">Pay ${{ cartStore.formatPrice(cartStore.total) }}</span>
        <span v-else>Processing...</span>
      </button>

      <!-- Error message -->
      <div v-if="error" class="mt-4 p-3 bg-red-100 text-red-700 rounded-lg">
        {{ error }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useCartStore } from '../../stores/cart';
import { useRouter } from 'vue-router';
import { useQuasar } from 'quasar';

const router = useRouter();
const cartStore = useCartStore();
const $q = useQuasar();

// State
const loading = ref(false);
const error = ref(null);
const cardError = ref(null);
const stripe = ref(null);
const elements = ref(null);
const cardElement = ref(null);

// Shipping details form
const shippingDetails = ref({
  firstName: '',
  lastName: '',
  address: '',
  city: '',
  postalCode: '',
  state: '',
  country: 'US'
});

// Check if the form is valid
const isFormValid = computed(() => {
  return shippingDetails.value.firstName && 
         shippingDetails.value.lastName && 
         shippingDetails.value.address && 
         shippingDetails.value.city && 
         shippingDetails.value.postalCode && 
         shippingDetails.value.state && 
         shippingDetails.value.country;
});

// Initialize Stripe on component mount
onMounted(async () => {
  if (cartStore.items.length === 0) {
    // Redirect to cart if no items
    router.push({ name: 'cart.index' });
    return;
  }
  
  // Load Stripe.js
  if (!window.Stripe) {
    const script = document.createElement('script');
    script.src = 'https://js.stripe.com/v3/';
    script.async = true;
    document.head.appendChild(script);
    
    // Wait for Stripe to load
    await new Promise(resolve => {
      script.onload = resolve;
    });
  }
  
  // Initialize Stripe with your public key
  // Replace this with your actual Stripe public key from environment
  stripe.value = window.Stripe(import.meta.env.VITE_STRIPE_PUBLIC_KEY || 'pk_test_your_key');
  
  // Create Stripe Elements
  elements.value = stripe.value.elements();
  
  // Create and mount the card Element
  cardElement.value = elements.value.create('card', {
    style: {
      base: {
        color: '#32325d',
        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
        fontSmoothing: 'antialiased',
        fontSize: '16px',
        '::placeholder': {
          color: '#aab7c4'
        }
      },
      invalid: {
        color: '#fa755a',
        iconColor: '#fa755a'
      }
    }
  });
  
  cardElement.value.mount('#card-element');
  
  // Handle card validation errors
  cardElement.value.addEventListener('change', event => {
    cardError.value = event.error ? event.error.message : '';
  });
});

// Submit payment
const submitPayment = async () => {
  if (!isFormValid.value) {
    error.value = 'Please fill in all required fields';
    return;
  }
  
  loading.value = true;
  error.value = null;
  
  try {
    // First create a checkout session with our cart
    const checkoutResult = await cartStore.proceedToCheckout();
    
    if (checkoutResult.error) {
      error.value = checkoutResult.error;
      loading.value = false;
      return;
    }
    
    if (!checkoutResult.session || !checkoutResult.session.client_secret) {
      error.value = 'Unable to initialize checkout. Please try again.';
      loading.value = false;
      return;
    }
    
    // Create payment method with Stripe
    const { error: stripeError, paymentMethod } = await stripe.value.createPaymentMethod({
      type: 'card',
      card: cardElement.value,
      billing_details: {
        name: `${shippingDetails.value.firstName} ${shippingDetails.value.lastName}`,
        address: {
          line1: shippingDetails.value.address,
          city: shippingDetails.value.city,
          postal_code: shippingDetails.value.postalCode,
          state: shippingDetails.value.state,
          country: shippingDetails.value.country
        }
      }
    });
    
    if (stripeError) {
      error.value = stripeError.message;
      loading.value = false;
      return;
    }
    
    // Confirm the payment with the session
    const { error: confirmError } = await stripe.value.confirmCardPayment(
      checkoutResult.session.client_secret,
      {
        payment_method: paymentMethod.id,
        shipping: {
          name: `${shippingDetails.value.firstName} ${shippingDetails.value.lastName}`,
          address: {
            line1: shippingDetails.value.address,
            city: shippingDetails.value.city,
            postal_code: shippingDetails.value.postalCode,
            state: shippingDetails.value.state,
            country: shippingDetails.value.country
          }
        }
      }
    );
    
    if (confirmError) {
      error.value = confirmError.message;
      loading.value = false;
      return;
    }
    
    // Payment successful
    await cartStore.handleCheckoutSuccess(checkoutResult.session);
    
    // Show success notification
    $q.notify({
      message: 'Payment successful! Your order has been placed.',
      color: 'positive',
      position: 'top',
      timeout: 2000
    });
    
    // Redirect to order confirmation
    router.push({ name: 'order.confirmation', params: { id: checkoutResult.session.id } });
    
  } catch (err) {
    console.error('Payment error:', err);
    error.value = 'An error occurred processing your payment. Please try again.';
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.stripe-checkout-container {
  padding: 2rem 1rem;
}

@media (min-width: 768px) {
  .stripe-checkout-container {
    padding: 3rem 2rem;
  }
}
</style>
