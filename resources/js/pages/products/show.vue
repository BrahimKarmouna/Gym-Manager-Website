<template>
    <div class="container mx-auto px-4 py-10 text-white">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        
        <!-- Images du produit -->
        <div>
          <img :src="selectedImage" class="w-full h-96 object-cover rounded-lg shadow-lg" alt="Product Image" />
          <div class="flex mt-4 space-x-2">
            <img 
              v-for="(img, index) in product.images" 
              :key="index" 
              :src="img" 
              class="w-16 h-16 rounded-lg cursor-pointer border-2 border-transparent hover:border-gray-400"
              @click="selectedImage = img"
            />
          </div>
        </div>
  
        <!-- Détails du produit -->
        <div>
          <h1 class="text-3xl font-bold">{{ product.name }}</h1>
          <p class="text-lg mt-2">{{ product.description }}</p>
  
          <div class="mt-4">
            <span class="text-2xl font-semibold text-blue-400">{{ product.price }} €</span>
            <span v-if="product.discount" class="text-red-400 line-through ml-3">{{ product.originalPrice }} €</span>
          </div>
  
          <!-- Sélecteur de quantité -->
          <div class="flex items-center mt-5">
            <button @click="decreaseQuantity" class="px-4 py-2 bg-gray-700 rounded-l">-</button>
            <input type="number" v-model="quantity" class="w-16 text-center border text-black" />
            <button @click="increaseQuantity" class="px-4 py-2 bg-gray-700 rounded-r">+</button>
          </div>
  
          <!-- Bouton Ajouter au panier -->
          <button 
            @click="addToCart" 
            class="mt-6 w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition"
          >
            Ajouter au panier
          </button>
  
          <!-- Avis clients -->
          <div class="mt-10">
            <h2 class="text-xl font-semibold">Avis clients</h2>
            <div v-for="(review, index) in product.reviews" :key="index" class="mt-4 p-4 border border-gray-600 rounded-lg">
              <p class="text-sm text-yellow-400">⭐⭐⭐⭐⭐ {{ review.rating }}/5</p>
              <p class="text-gray-300">{{ review.comment }}</p>
              <p class="text-sm text-gray-400">- {{ review.author }}</p>
            </div>
          </div>
        </div>
  
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from "vue";
  
  const product = ref({
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
  
  const increaseQuantity = () => {
    quantity.value++;
  };
  
  const decreaseQuantity = () => {
    if (quantity.value > 1) quantity.value--;
  };
  
  const addToCart = () => {
    alert(`Ajouté au panier : ${product.value.name} x${quantity.value}`);
  };
  </script>
  
  <style scoped>
  </style>
  