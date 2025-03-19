<template>
  <div>
    <q-dialog v-model="visible" persistent maximized transition-show="slide-up" transition-hide="slide-down">
      <q-card class="bg-white">
        <!-- Header -->
        <q-card-section class="bg-black text-white q-px-lg">
          <div class="row items-center justify-between">
            <div class="text-h5 font-weight-bold">New Client Registration</div>
            <q-btn round flat icon="close" color="white" v-close-popup @click="closeModal" />
          </div>
        </q-card-section>

        <!-- Form Content -->
        <q-card-section class="q-pa-lg">
          <div class="row q-col-gutter-x-xl q-col-gutter-y-md">
            <!-- Left Column -->
            <div class="col-12 col-md-6">
              <div class="text-h6 q-mb-md text-weight-medium">Personal Information</div>
              
              <q-input 
                v-model="client.Full_name" 
                label="Full Name" 
                outlined 
                class="q-mb-md" 
                bg-color="white"
                color="black"
              />
              
              <q-input 
                v-model="client.date_of_birth" 
                label="Date of Birth" 
                type="date" 
                outlined 
                class="q-mb-md" 
                bg-color="white"
                color="black"
              />
              
              <q-input 
                v-model="client.id_card_number" 
                label="ID Card Number" 
                outlined 
                class="q-mb-md" 
                bg-color="white"
                color="black"
              />
              
              <q-input 
                v-model="client.email" 
                label="Email Address" 
                type="email" 
                outlined 
                class="q-mb-md" 
                bg-color="white"
                color="black"
              />
              
              <q-input 
                v-model="client.phone" 
                label="Phone Number" 
                outlined 
                class="q-mb-md" 
                bg-color="white"
                color="black"
              />
              
              <q-input 
                v-model="client.address" 
                label="Address" 
                outlined 
                type="textarea" 
                rows="3" 
                class="q-mb-md"
                bg-color="white"
                color="black"
              />
            </div>

            <!-- Right Column -->
            <div class="col-12 col-md-6">
              <div class="text-h6 q-mb-md text-weight-medium">Gym Details & Documents</div>
              
              <q-input 
                v-model="client.gym_id" 
                label="Gym ID" 
                type="number" 
                outlined 
                class="q-mb-md" 
                bg-color="white"
                color="black"
              />

              <!-- ID Card Upload -->
              <div class="q-mb-lg">
                <p class="q-mb-xs text-subtitle1">ID Card Picture</p>
                <div class="file-upload-container">
                  <q-file
                    v-model="client.id_card_picture"
                    outlined
                    accept="image/*"
                    class="full-width"
                    bg-color="white"
                    color="black"
                    bottom-slots
                  >
                    <template v-slot:prepend>
                      <q-icon name="attach_file" color="black" />
                    </template>
                    <template v-slot:hint>
                      JPG, PNG or PDF format
                    </template>
                  </q-file>
                </div>
              </div>

              <!-- Client Picture Upload -->
              <div class="q-mb-lg">
                <p class="q-mb-xs text-subtitle1">Client Picture</p>
                <div class="file-upload-container">
                  <q-file
                    v-model="client.client_picture"
                    outlined
                    accept="image/*"
                    class="full-width"
                    bg-color="white"
                    color="black"
                    bottom-slots
                  >
                    <template v-slot:prepend>
                      <q-icon name="photo_camera" color="black" />
                    </template>
                    <template v-slot:hint>
                      Passport-sized photo recommended
                    </template>
                  </q-file>
                </div>
              </div>
            </div>
          </div>
        </q-card-section>

        <!-- Footer Actions -->
        <q-card-section class="q-pa-lg bg-grey-1">
          <div class="row justify-end q-gutter-md">
            <q-btn 
              label="Cancel" 
              color="dark" 
              flat 
              @click="closeModal" 
              class="q-px-md" 
              v-close-popup
            />
            <q-btn 
              label="Save Client" 
              color="black" 
              unelevated 
              @click="saveClient" 
              class="q-px-md" 
              icon-right="save"
            />
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>


<script setup>
import { ref } from "vue";
import axios from "axios";

// Définition des props
const visible = defineModel("visible", { default: false, type: Boolean });

// Objet client
const client = ref({
  Full_name: "",
  date_of_birth: "",
  address: "",
  gym_id: "",
  id_card_picture: null,
  client_picture: null,
  id_card_number: "",
  email: "",
  phone: "",
});

// Capture les fichiers uploadés
const handleIdCardUpload = (files) => {
  client.value.id_card_picture = files[0]; // Prend le premier fichier
};

const handleClientPictureUpload = (files) => {
  client.value.client_picture = files[0]; // Prend le premier fichier
};

// Fonction pour fermer le modal
const closeModal = () => {
  visible.value = false;
  resetClient();
};

// Réinitialiser les valeurs après enregistrement
const resetClient = () => {
  client.value = {
      Full_name: "",
      date_of_birth: "",
      address: "",
      gym_id: "",
      id_card_picture: null,
      client_picture: null,
      id_card_number: "",
      email: "",
      phone: "",
  };
};

// Fonction pour enregistrer un client
const saveClient = () => {
  const formData = new FormData();
  formData.append("Full_name", client.value.Full_name);
  formData.append("date_of_birth", client.value.date_of_birth);
  formData.append("address", client.value.address);
  formData.append("gym_id", client.value.gym_id);
  formData.append("id_card_picture", client.value.id_card_picture);
  formData.append("client_picture", client.value.client_picture);
  formData.append("id_card_number", client.value.id_card_number);
  formData.append("email", client.value.email);
  formData.append("phone", client.value.phone);

  axios
      .post("/api/clients", formData, {
          headers: { "Content-Type": "multipart/form-data" },
      })
      .then(() => {
          console.log("Client saved successfully.");
          closeModal();
      })
      .catch((error) => {
          console.error("Error saving client:", error);
      });
};
</script>
