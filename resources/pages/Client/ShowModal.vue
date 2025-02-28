<template>
  <q-dialog v-model="visible" persistent>
    <q-card class="min-w-[900px] h-[700px] shadow-2xl rounded-lg border border-gray-200 bg-white">
      
      <!-- Profile Header -->
      <div class="flex justify-between items-center p-6 bg-gray-50 rounded-t-lg">
        <h2 v-if="client" class="text-3xl font-semibold text-gray-800">{{ client.Full_name }}</h2>
        <span v-if="client" class="text-sm text-gray-500">Date of Birth: {{ client.date_of_birth }}</span>
      </div>

      <!-- Profile Details -->
      <div v-if="client" class="p-6 space-y-6">
        <div class="mb-4 flex items-center">
          <label class="font-semibold text-gray-600 w-32">Phone:</label>
          <p class="text-gray-700">{{ client.phone }}</p>
        </div>
        <div class="mb-4 flex items-center">
          <label class="font-semibold text-gray-600 w-32">Email:</label>
          <p class="text-gray-700">{{ client.email }}</p>
        </div>
        <div class="mb-4 flex items-center">
          <label class="font-semibold text-gray-600 w-32">Address:</label>
          <p class="text-gray-700">{{ client.address }}</p>
        </div>

        <!-- Status Information -->
        <div class="grid grid-cols-2 gap-6">
          <div class="flex items-center space-x-2">
            <div class="h-2.5 w-2.5 rounded-full bg-green-500"></div>
            <span class="text-gray-700">Assure</span>
          </div>
          <div class="flex items-center space-x-2">
            <div class="h-2.5 w-2.5 rounded-full bg-red-500"></div>
            <span class="text-gray-700">Non Payée</span>
          </div>
        </div>

        <!-- Images Section -->
        <div class="mt-6 grid grid-cols-2 gap-6">
          <!-- Client Picture -->
          <div class="flex justify-center items-center">
            <img
              id="client-picture"
              v-if="client.client_picture"
              :src="'storage/' + client.client_picture"
              alt="Client Image"
              class="w-48 h-48 object-cover rounded-full border-4 border-gray-200 shadow-md transform transition duration-300 hover:scale-110"
            />
          </div>

          <!-- ID Card Picture -->
          <div class="flex justify-center items-center">
            <img
              id="id-card-picture"
              v-if="client.id_card_picture"
              :src="'storage/' + client.id_card_picture"
              alt="ID Card"
              class="w-80 max-h-56 object-cover rounded-lg border-4 border-gray-200 shadow-md transform transition duration-300 hover:scale-110"
            />
          </div>
        </div>
      </div>

      <!-- Actions -->
      <q-card-actions align="right" class="p-4 bg-gray-100 rounded-b-lg">
        <q-btn flat label="Fermer" @click="closeModal" class="text-red-500 hover:text-red-700 font-semibold" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { defineProps, defineModel, ref, watch } from "vue";
import axios from "axios";

// Définition des props
const props = defineProps({
  id: Number,
  modelValue: { type: Boolean, default: false },
});

// Déclaration des variables réactives
const client = ref(null);

// Surveillance des changements d'ID pour charger les données du client
watch(() => props.id, (newId) => {
  if (newId) {
    fetchClientById(newId);
  }
});

// Fonction pour récupérer les informations du client
const fetchClientById = (id) => {
  axios
    .get(`/api/clients/${id}`)
    .then((response) => {
      client.value = response.data;
    })
    .catch((error) => {
      console.error("Erreur lors de la récupération du client :", error);
    });
};

// Gérer la visibilité du modal
const visible = defineModel("visible", { default: false, type: Boolean });

// Fonction pour fermer le modal
const closeModal = () => {
  visible.value = false;
};
</script>
