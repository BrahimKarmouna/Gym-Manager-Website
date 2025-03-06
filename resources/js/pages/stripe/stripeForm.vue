<template>
  <!-- Stripe Elements Placeholder -->
  <div id="card-element"></div>
</template>

<script>
import { api } from '@/boot/axios';

export default {
  data() {
    return {
      stripe: null,
      elements: null,
      cardElement: null,
      cardHolderName: "",
    };
  },
  async mounted() {
    const stripe = Stripe(import.meta.env.VITE_STRIPE_KEY);

    const checkout = await stripe.initEmbeddedCheckout({
      fetchClientSecret: this.fetchClientSecret,
    });

    // const elements = stripe.elements({

    // });
    // const cardElement = elements.create('card');

    checkout.mount('#card-element');

    // this.stripe = await loadStripe(import.meta.env.VITE_STRIPE_KEY);

    // if (!this.stripe) {
    //   console.error("Stripe failed to load.");
    //   return;
    // }

    // this.elements = this.stripe.elements();
    // this.cardElement = this.elements.create("card");

    // if (this.cardElement) {
    //   this.cardElement.mount("#card-element");
    //   console.log("Stripe Elements mounted successfully!");
    // } else {
    //   console.error("Failed to create card element.");
    // }
  },
  methods: {
    async fetchClientSecret() {
      const response = await api.post("/create-checkout-session");

      const { client_secret: clientSecret } = await response.data;

      return clientSecret;
    },

    async processPayment() {
      if (!this.stripe || !this.cardElement) {
        alert("Stripe is not properly initialized.");
        return;
      }

      const { paymentMethod, error } = await this.stripe.createPaymentMethod(
        "card",
        this.cardElement,
        { billing_details: { name: this.cardHolderName } }
      );

      if (error) {
        alert(error.message);
      } else {
        alert("Payment method created: " + paymentMethod.id);
        this.submitPayment(paymentMethod.id);
      }
    },
    async submitPayment(paymentMethodId) {
      try {
        const response = await fetch("/api/charge", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ payment_method_id: paymentMethodId }),
        });

        const result = await response.json();
        if (result.success) {
          alert("Payment successful!");
        } else {
          alert("Payment failed: " + result.message);
        }
      } catch (error) {
        alert("Error processing payment: " + error.message);
      }
    },
  },
};
</script>
