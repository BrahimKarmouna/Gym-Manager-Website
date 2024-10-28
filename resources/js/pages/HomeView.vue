<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md">
      <div class="p-6">
        <h2 class="text-2xl font-bold">Dashboard</h2>
      </div>
      <nav>
        <ul>
          <li class="py-2 px-4 hover:bg-gray-200">
            <a href="#" class="text-gray-800">Home</a>
          </li>
          <li class="py-2 px-4 hover:bg-gray-200">
            <a href="#" class="text-gray-800">Profile</a>
          </li>
          <li class="py-2 px-4 hover:bg-gray-200">
            <a href="#" class="text-gray-800">Settings</a>
          </li>
          <li class="py-2 px-4 hover:bg-gray-200">
            <a href="#" class="text-gray-800">Logout</a>
          </li>
        </ul>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6">
      <header class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Welcome to the Dashboard</h1>
        <div class="relative">
          <button @click="toggleDropdown" class="flex items-center focus:outline-none">
            <img
              src="https://via.placeholder.com/40"
              alt="User Profile"
              class="w-10 h-10 rounded-full"
            />
          </button>
          <transition name="fade">
            <div v-if="isDropdownOpen" class="absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-md z-10">
              <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
              <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Settings</a>
              <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Logout</a>
            </div>
          </transition>
        </div>
      </header>

 
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const isDropdownOpen = ref(false);

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value;
};
</script>

<style>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}
</style>
