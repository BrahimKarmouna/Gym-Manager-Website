import { defineStore } from 'pinia';
import axios from 'axios';
import { useAuthStore } from './auth';

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    showCart: false,
    loading: false,
    error: null,
    couponCode: '',
    couponDiscount: 0,
    checkoutStatus: null
  }),

  getters: {
    // Total number of items in cart
    totalItems: (state) => {
      return state.items.reduce((total, item) => total + item.quantity, 0);
    },

    // Subtotal of all items
    subtotal: (state) => {
      return state.items.reduce((total, item) => total + (item.price * item.quantity), 0);
    },

    // Discount amount based on coupon
    discount: (state) => {
      return (state.subtotal * state.couponDiscount) / 100;
    },

    // Tax calculation (assuming 7.5% tax rate)
    tax: (state) => {
      return ((state.subtotal - state.discount) * 0.075);
    },

    // Total after discount and tax
    total: (state) => {
      return state.subtotal - state.discount + state.tax;
    },

    // Check if cart is empty
    isEmpty: (state) => {
      return state.items.length === 0;
    },

    // Format currency
    formatPrice: () => (price) => {
      return (Math.round(price * 100) / 100).toFixed(2);
    }
  },

  actions: {
    // Initialize cart from localStorage or API
    async initCart() {
      this.loading = true;
      this.error = null;
      
      try {
        const authStore = useAuthStore();
        
        // If user is authenticated, get cart from API
        if (authStore.authenticated) {
          await this.fetchCartFromAPI();
        } else {
          // For guest users, get cart from localStorage
          this.loadCartFromLocalStorage();
        }
      } catch (error) {
        console.error('Error initializing cart:', error);
        this.error = 'Failed to load your cart. Please try again.';
      } finally {
        this.loading = false;
      }
    },

    // Load cart from localStorage for guest users
    loadCartFromLocalStorage() {
      try {
        const cartData = localStorage.getItem('cart');
        this.items = cartData ? JSON.parse(cartData) : [];
      } catch (error) {
        console.error('Error loading cart from localStorage:', error);
        this.items = [];
      }
    },

    // Fetch cart from API for authenticated users
    async fetchCartFromAPI() {
      try {
        const response = await axios.get('/api/cart');
        this.items = response.data.items || [];
      } catch (error) {
        console.error('Error fetching cart from API:', error);
        // Fallback to localStorage if API fails
        this.loadCartFromLocalStorage();
      }
    },

    // Save cart to localStorage
    saveCartToLocalStorage() {
      localStorage.setItem('cart', JSON.stringify(this.items));
    },

    // Save cart to API for authenticated users
    async saveCartToAPI() {
      try {
        const authStore = useAuthStore();
        
        if (authStore.authenticated) {
          await axios.post('/api/cart', {
            items: this.items
          });
        }
      } catch (error) {
        console.error('Error saving cart to API:', error);
      }
    },

    // Save cart to appropriate storage
    async saveCart() {
      const authStore = useAuthStore();
      
      // Always save to localStorage for quick access
      this.saveCartToLocalStorage();
      
      // If authenticated, also save to API
      if (authStore.authenticated) {
        await this.saveCartToAPI();
      }
    },

    // Add item to cart
    async addToCart(product, quantity = 1) {
      const existingItem = this.items.find(item => item.id === product.id);
      
      if (existingItem) {
        existingItem.quantity += quantity;
      } else {
        this.items.push({
          id: product.id,
          name: product.name,
          price: product.price,
          description: product.description,
          image: product.image || (product.images && product.images.length > 0 ? product.images[0] : ''),
          quantity: quantity
        });
      }
      
      await this.saveCart();
    },

    // Update item quantity
    async updateItemQuantity(itemId, quantity) {
      const item = this.items.find(item => item.id === itemId);
      
      if (item) {
        item.quantity = quantity;
        
        // Remove item if quantity is 0 or less
        if (item.quantity <= 0) {
          this.removeItem(itemId);
          return;
        }
        
        await this.saveCart();
      }
    },

    // Remove item from cart
    async removeItem(itemId) {
      this.items = this.items.filter(item => item.id !== itemId);
      await this.saveCart();
    },

    // Clear cart
    async clearCart() {
      this.items = [];
      this.couponCode = '';
      this.couponDiscount = 0;
      await this.saveCart();
    },

    // Toggle cart visibility
    toggleCart() {
      this.showCart = !this.showCart;
    },

    // Show cart
    showCartPanel() {
      this.showCart = true;
    },

    // Hide cart
    hideCartPanel() {
      this.showCart = false;
    },

    // Apply coupon code
    async applyCoupon(code) {
      this.loading = true;
      this.error = null;
      
      try {
        // Simulated coupon codes for demo
        const validCoupons = {
          'WELCOME10': 10,
          'SAVE20': 20,
          'SUMMER25': 25
        };
        
        // Check if the coupon code is valid
        if (validCoupons[code]) {
          this.couponCode = code;
          this.couponDiscount = validCoupons[code];
          return true;
        } else {
          // In production, validate coupon with API
          /* 
          const response = await axios.post('/api/validate-coupon', { code });
          if (response.data.valid) {
            this.couponCode = code;
            this.couponDiscount = response.data.discount;
            return true;
          }
          */
          this.error = 'Invalid coupon code';
          return false;
        }
      } catch (error) {
        console.error('Error applying coupon:', error);
        this.error = 'Failed to apply coupon. Please try again.';
        return false;
      } finally {
        this.loading = false;
      }
    },

    // Remove coupon
    removeCoupon() {
      this.couponCode = '';
      this.couponDiscount = 0;
    },

    // Proceed to checkout
    async proceedToCheckout() {
      const authStore = useAuthStore();
      
      // Redirect to login if not authenticated
      if (!authStore.authenticated) {
        // Store the cart in localStorage before redirecting
        this.saveCartToLocalStorage();
        
        // Return path to redirect to login
        return { redirectToLogin: true };
      }
      
      // If already authenticated, prepare checkout
      this.loading = true;
      this.error = null;
      
      try {
        // Save cart to the server
        await this.saveCartToAPI();
        
        // Create a checkout session
        const response = await axios.post('/api/checkout/create-session', {
          items: this.items,
          couponCode: this.couponCode
        });
        
        // Return success with session info
        return {
          success: true,
          session: response.data
        };
      } catch (error) {
        console.error('Error creating checkout session:', error);
        this.error = 'Failed to create checkout session. Please try again.';
        return { error: this.error };
      } finally {
        this.loading = false;
      }
    },

    // Handle successful checkout
    async handleCheckoutSuccess(session) {
      this.checkoutStatus = 'success';
      
      try {
        // Record order in the database
        await axios.post('/api/orders', {
          sessionId: session.id,
          items: this.items,
          couponCode: this.couponCode,
          discount: this.couponDiscount,
          subtotal: this.subtotal,
          tax: this.tax,
          total: this.total
        });
        
        // Clear the cart after successful order
        await this.clearCart();
      } catch (error) {
        console.error('Error recording order:', error);
      }
    },

    // Merge guest cart with user cart after login
    async mergeCartsAfterLogin() {
      const localCart = JSON.parse(localStorage.getItem('cart') || '[]');
      
      if (localCart.length > 0) {
        try {
          // Get the user cart from API
          const response = await axios.get('/api/cart');
          const userCart = response.data.items || [];
          
          // Merge the carts
          localCart.forEach(localItem => {
            const userItem = userCart.find(item => item.id === localItem.id);
            
            if (userItem) {
              // If item exists in both carts, add quantities
              userItem.quantity += localItem.quantity;
            } else {
              // If item only exists in local cart, add it to user cart
              userCart.push(localItem);
            }
          });
          
          // Update the merged cart to API
          await axios.post('/api/cart', {
            items: userCart
          });
          
          // Update local store
          this.items = userCart;
          
          // Clear the local storage cart
          localStorage.removeItem('cart');
        } catch (error) {
          console.error('Error merging carts after login:', error);
        }
      }
    }
  }
});