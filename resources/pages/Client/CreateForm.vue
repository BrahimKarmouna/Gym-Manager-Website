<template>
  <div class="q-pa-md q-gutter-sm">
    <q-dialog v-model="visible" persistent>
      <q-card style="min-width: 900px; height: 700px;">
        <q-card-section class="row justify-between items-center">
          <div class="text-h6">Add Client Information</div>
          <q-btn flat round dense icon="close" @click="closeModal" />
        </q-card-section>

        <q-card-section class="q-pt-none">
          <div class="row q-col-gutter-md">
            <!-- Full Name -->
            <q-input dense v-model="client.Full_name" label="Full Name" filled class="col-6 q-mb-md" />
            <!-- ID Card Number -->
            <q-input dense v-model="client.id_card_number" label="ID Card Number" filled class="col-6 q-mb-md" />

            <!-- Date of Birth -->
            <q-input dense v-model="client.date_of_birth" label="Date of Birth" type="date" filled class="col-6 q-mb-md" />
            <!-- Phone Number -->
            <q-input dense v-model="client.phone" label="Phone Number" type="tel" filled class="col-6 q-mb-md" />

            <!-- Email -->
            <q-input dense v-model="client.email" label="Email" type="email" filled class="col-6 q-mb-md" />
            <!-- Address -->
            <q-input dense v-model="client.address" label="Address" filled class="col-6 q-mb-md" />

            <!-- Gym ID -->
            <q-input dense v-model="client.gym_id" label="Gym ID" type="number" filled class="col-6 q-mb-md" />

            <!-- Uploads alignés en bas -->
            <q-uploader label="Upload ID Card Picture" accept="image/*" filled class="col-6 q-mb-md" @added="handleIdCardUpload" />
            <q-uploader label="Upload Client Picture" accept="image/*" filled class="col-6 q-mb-md" @added="handleClientPictureUpload" />
          </div>
        </q-card-section>

        <q-card-actions align="right" class="text-primary">
          <q-btn flat label="Cancel" @click="closeModal" />
          <q-btn flat label="Save Client" @click="saveClient" />
        </q-card-actions>
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
