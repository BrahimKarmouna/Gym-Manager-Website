<template>
  <div class="w-full h-screen bg-black text-white flex justify-center items-center px-10">
    <div class="w-full max-w-[1800px] grid grid-cols-1 md:grid-cols-2 gap-20">
      <!-- Images du produit -->
      <div class="flex flex-col items-center w-full">
        <img
          :src="selectedImage"
          class="w-[500px] h-[500px] object-contain rounded-lg shadow-lg bg-white"
          alt="Product Image"
        />
        <div class="flex flex-wrap justify-center gap-4 mt-6">
          <img
            v-for="(img, index) in product.images"
            :key="index"
            :src="img"
            class="w-28 h-28 object-contain rounded-lg cursor-pointer border-4 border-transparent hover:border-gray-400 bg-white"
            @click="selectedImage = img"
          />
        </div>
      </div>

      <!-- Détails du produit (smaller) -->
      <div class="flex flex-col justify-center w-full max-w-md">
        <h1 class="text-4xl font-bold">{{ product.name }}</h1>
        <p class="text-xl mt-4">{{ product.description }}</p>

        <div class="mt-6">
          <span class="text-3xl font-semibold text-blue-400">{{ cartStore.formatPrice(product.price) }} €</span>
          <span
            v-if="product.discount"
            class="text-red-400 line-through ml-4 text-2xl"
          >{{ cartStore.formatPrice(product.originalPrice) }} €</span>
        </div>

        <!-- Sélecteur de quantité -->
        <div class="flex items-center mt-8">
          <button
            @click="decreaseQuantity"
            class="px-6 py-3 bg-gray-700 text-2xl rounded-l"
          >-</button>
          <input
            type="number"
            v-model="quantity"
            class="w-20 text-2xl text-center border text-black"
          />
          <button
            @click="increaseQuantity"
            class="px-6 py-3 bg-gray-700 text-2xl rounded-r"
          >+</button>
        </div>

        <!-- Add to Cart Button -->
        <button
          @click="addToCart"
          class="mt-4 w-full bg-orange-600 text-center text-white py-5 text-2xl rounded-lg hover:bg-orange-700 transition"
        >
          Add to Cart
        </button>

        <!-- Bouton Achetez maintenant (WhatsApp) -->
        <a
          :href="'https://wa.me/+212766269594?text=J%27ai%20besoin%20de%20commander%20la%20montre%20' + product.name + '%20pour%20' + product.price + '%20€'"
          target="_blank"
          class="mt-4 w-full bg-blue-600 text-center text-white py-5 text-2xl rounded-lg hover:bg-green-700 transition"
        >
          Achetez maintenant
        </a>

        <!-- Cart Indicator -->
        <div class="mt-4 flex justify-between items-center">
          <div class="flex items-center cursor-pointer" @click="cartStore.showCartPanel()">
            <span class="material-icons text-2xl mr-2">shopping_cart</span>
            <span>Cart ({{ cartStore.totalItems }} items)</span>
          </div>
          <span v-if="cartStore.totalItems > 0" class="text-blue-400">
            Total: {{ cartStore.formatPrice(cartStore.subtotal) }} €
          </span>
        </div>

        <!-- Avis clients -->
        <div class="mt-12">
          <h2 class="text-3xl font-semibold">Avis clients</h2>
          <div
            v-for="(review, index) in product.reviews"
            :key="index"
            class="mt-6 p-6 border border-gray-600 rounded-lg"
          >
            <p class="text-xl text-yellow-400">⭐⭐⭐⭐⭐ {{ review.rating }}/5</p>
            <p class="text-lg text-gray-300">{{ review.comment }}</p>
            <p class="text-lg text-gray-400">- {{ review.author }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast-notification" v-if="showToast" :class="{ 'show': showToast }">
      <div class="toast-content">
        <span class="material-icons success-icon">check_circle</span>
        <span>{{ toastMessage }}</span>
      </div>
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted } from "vue";
import { useCartStore } from "../../../stores/cart";

// Initialize cart store
const cartStore = useCartStore();

const product = ref({
  id: 1, // Added ID for cart functionality
  name: "Montre de Luxe",
  description: "Une montre élégante et moderne pour toutes les occasions.",
  price: 249.99,
  originalPrice: 299.99,
  discount: true,
  images: [
    "https://cdn11.bigcommerce.com/s-z6voly6yu7/images/stencil/1028x1028/products/1526/16338/T50_DSF1873-1028px__47362.1727973907.jpg?c=1",
    "https://cdn11.bigcommerce.com/s-z6voly6yu7/images/stencil/1028x1028/products/1526/16337/T50_DSF1882-1028px__15166.1727973907.jpg?c=1",
    "https://cdn11.bigcommerce.com/s-z6voly6yu7/images/stencil/1028x1028/products/1526/16341/T50_DSF2016-1028px__37972.1727973908.jpg?c=1",
    "https://cdn11.bigcommerce.com/s-z6voly6yu7/images/stencil/1028x1028/products/1526/16340/T50_DSF2043-1028px__46372.1727973908.jpg?c=1",
    "https://cdn11.bigcommerce.com/s-z6voly6yu7/images/stencil/1028x1028/products/1526/16339/T50_DSF1888-1028px__33386.1727973908.jpg?c=1",
  ],
  reviews: [
    { author: "Alice", rating: 5, comment: "Superbe montre ! Très élégante." },
    { author: "Bob", rating: 4, comment: "Très satisfait, mais la livraison a pris du temps." },
  ],
});

const selectedImage = ref(product.value.images[0]);
const quantity = ref(1);
const showToast = ref(false);
const toastMessage = ref('');

const increaseQuantity = () => {
  quantity.value++;
};

const decreaseQuantity = () => {
  if (quantity.value > 1) quantity.value--;
};

const addToCart = async () => {
  await cartStore.addToCart(product.value, quantity.value);
  toastMessage.value = `${product.value.name} x${quantity.value} added to cart!`;
  showToast.value = true;
  setTimeout(() => {
    showToast.value = false;
  }, 3000);
};

// Initialize cart on page load
onMounted(async () => {
  await cartStore.initCart();
});
</script>

<style scoped>
.toast-notification {
  position: fixed;
  bottom: -100px;
  left: 50%;
  transform: translateX(-50%);
  background-color: #4CAF50;
  color: white;
  padding: 16px 24px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  transition: bottom 0.3s;
}

.toast-notification.show {
  bottom: 20px;
}

.toast-content {
  display: flex;
  align-items: center;
  gap: 10px;
}

.success-icon {
  font-size: 1.5rem;
}
</style>
