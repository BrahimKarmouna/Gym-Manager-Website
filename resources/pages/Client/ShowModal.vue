<template>
  <q-dialog v-model="visible" persistent>
    <q-card class="max-w-4xl w-full bg-white shadow-2xl rounded-xl overflow-hidden border-0">
      
      <!-- Profile Header -->
      <div v-if="client" class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6 relative">
        <div class="flex justify-between items-center">
          <div class="text-white">
            <h2 class="text-2xl font-bold">{{ client.Full_name }}</h2>
            <p class="text-blue-100 text-sm mt-1 flex items-center">
              <q-icon name="event" size="xs" class="mr-1" />
              {{ client.date_of_birth }}
            </p>
          </div>
          <q-btn flat round icon="close" text-color="white" @click="closeModal" class="absolute top-3 right-3" />
        </div>
      </div>

      <!-- Profile Content -->
      <div v-if="client" class="p-0">
        <div class="flex flex-col md:flex-row">
          <!-- Left Column: Client Info -->
          <div class="md:w-1/2 p-6 space-y-4">
            <h3 class="text-lg font-semibold text-gray-800 pb-2 border-b border-gray-200">Personal Information</h3>
            
            <div class="space-y-3">
              <div class="flex items-start">
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                  <q-icon name="phone" size="sm" color="blue-600" />
                </div>
                <div>
                  <p class="text-sm text-gray-500">Phone</p>
                  <p class="text-gray-700 font-medium">{{ client.phone }}</p>
                </div>
              </div>
              
              <div class="flex items-start">
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                  <q-icon name="email" size="sm" color="blue-600" />
                </div>
                <div>
                  <p class="text-sm text-gray-500">Email</p>
                  <p class="text-gray-700 font-medium">{{ client.email }}</p>
                </div>
              </div>
              
              <div class="flex items-start">
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                  <q-icon name="home" size="sm" color="blue-600" />
                </div>
                <div>
                  <p class="text-sm text-gray-500">Address</p>
                  <p class="text-gray-700 font-medium">{{ client.address }}</p>
                </div>
              </div>
            </div>

            <!-- Status Cards -->
            <h3 class="text-lg font-semibold text-gray-800 pb-2 border-b border-gray-200 mt-6">Status</h3>
            <div class="grid grid-cols-2 gap-4 mt-3">
              <div class="p-3 bg-emerald-50 rounded-lg border border-emerald-100">
                <div class="flex items-center">
                  <div class="h-3 w-3 rounded-full bg-emerald-500 mr-2"></div>
                  <span class="text-emerald-700 font-medium">Assure</span>
                </div>
              </div>
              <div class="p-3 bg-red-50 rounded-lg border border-red-100">
                <div class="flex items-center">
                  <div class="h-3 w-3 rounded-full bg-red-500 mr-2"></div>
                  <span class="text-red-700 font-medium">Non Payée</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column: Images -->
          <div class="md:w-1/2 bg-gray-50 p-6">
            <h3 class="text-lg font-semibold text-gray-800 pb-2 border-b border-gray-200 mb-4">Documents & Photos</h3>
            
            <!-- Client Picture -->
            <div class="mb-6">
              <p class="text-sm text-gray-500 mb-2">Client Photo</p>
              <div class="flex justify-center">
                <div class="relative group">
                  <img
                    v-if="client.client_picture"
                    :src="'storage/' + client.client_picture"
                    alt="Client Image"
                    class="w-32 h-32 object-cover rounded-full border-4 border-white shadow-md transition-all duration-300 group-hover:shadow-lg"
                  />
                  <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <q-btn round flat icon="zoom_in" color="white" class="bg-black bg-opacity-50" />
                  </div>
                </div>
              </div>
            </div>

            <!-- ID Card Picture -->
            <div>
              <p class="text-sm text-gray-500 mb-2">ID Card</p>
              <div class="flex justify-center">
                <div class="relative group">
                  <img
                    v-if="client.id_card_picture"
                    :src="'storage/' + client.id_card_picture"
                    alt="ID Card"
                    class="max-w-full h-auto max-h-44 object-cover rounded-lg border-4 border-white shadow-md transition-all duration-300 group-hover:shadow-lg"
                  />
                  <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <q-btn round flat icon="zoom_in" color="white" class="bg-black bg-opacity-50" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="bg-gray-50 p-4 flex justify-end border-t border-gray-200">
        <q-btn 
          label="Close" 
          @click="closeModal" 
          class="bg-white hover:bg-gray-100 text-gray-700 font-medium px-6 py-2 rounded-lg border border-gray-300 transition-colors duration-200"
        />
      </div>
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
