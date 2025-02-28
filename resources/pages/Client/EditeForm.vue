<template>
    <div class="q-pa-md q-gutter-sm">
      <q-dialog v-model="visible" persistent>
        <q-card style="min-width: 900px; height: 700px;">
          <q-card-section class="row justify-between items-center">
            <div class="text-h6">Update Client Information</div>
            <q-btn flat round dense icon="close" @click="closeModal" />
          </q-card-section>
  
          <q-card-section class="q-pt-none">
            <div class="row q-col-gutter-md">
              <q-input dense v-model="client.Full_name" label="Full_name" filled class="col-6 q-mb-md" />
              <q-input dense v-model="client.id_card_number" label="ID Card Number" filled class="col-6 q-mb-md" />
              <q-input dense v-model="client.date_of_birth" label="Date of Birth" type="date" filled class="col-6 q-mb-md" />
              <q-input dense v-model="client.phone" label="Phone Number" type="tel" filled class="col-6 q-mb-md" />
              <q-input dense v-model="client.email" label="Email" type="email" filled class="col-6 q-mb-md" />
              <q-input dense v-model="client.address" label="Address" filled class="col-6 q-mb-md" />
              <q-input dense v-model="client.gym_id" label="Gym ID" type="number" filled class="col-6 q-mb-md" />
              <q-uploader label="Upload ID Card Picture" accept="image/*" filled class="col-6 q-mb-md" @added="handleIdCardUpload" />
              <q-uploader label="Upload Client Picture" accept="image/*" filled class="col-6 q-mb-md" @added="handleClientPictureUpload" />
            </div>
          </q-card-section>
  
          <q-card-actions align="right" class="text-primary">
            <q-btn flat label="Cancel" @click="closeModal" />
            <q-btn flat label="Update Client" @click="updateClient" />
          </q-card-actions>
        </q-card>
      </q-dialog>
    </div>
  </template>
  
  <script setup>
  import { ref, watch } from "vue";
  import axios from "axios";
  
  // Définition des props
  const props = defineProps({
    id: { type: Number, required: true },
  });
  
  const visible = defineModel("visible", { default: false, type: Boolean });
  
  // Objet client
  const client = ref({
    Full_name: "{{ client.Full_name }}",
    date_of_birth: "{{ client.date_of_birth }}",
    address: "{{ client.address }}",
    gym_id: "{{ client.gym_id }}",
    id_card_picture: "{{ client.id_card_picture }}",
    client_picture: "{{ client.client_picture }}",
    id_card_number: "{{ client.id_card_number }}",
    email: "{{ client.email }}",
    phone: "{{ client.phone }}",
  });
  
  const loading = ref(false);
  
  const updateClient = () => {
    if (!props.id) return;
  
    loading.value = true;
  
    // Create FormData to handle files
    const formData = new FormData();
    formData.append('Full_name', client.value.Full_name);
    formData.append('date_of_birth', client.value.date_of_birth);
    formData.append('address', client.value.address);
    formData.append('gym_id', client.value.gym_id);
    formData.append('id_card_number', client.value.id_card_number);
    formData.append('email', client.value.email);
    formData.append('phone', client.value.phone);
  
    // Append images if they exist
    if (client.value.id_card_picture) {
      formData.append('id_card_picture', client.value.id_card_picture);
    }
    if (client.value.client_picture) {
      formData.append('client_picture', client.value.client_picture);
    }
  
    axios
      .put(`/api/clients/${props.id}`, client, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      })
      .then((response) => {
        console.log("Client updated successfully", response.data);
        closeModal();
      })
      .catch((error) => {
        console.error("Erreur lors de la mise à jour du client :", error);
      })
      .finally(() => {
        loading.value = false;
      });
  };
  
  // Capture the uploaded files
  const handleIdCardUpload = (files) => {
    if (files && files.length > 0) {
      client.value.id_card_picture = files[0];
    }
  };
  
  const handleClientPictureUpload = (files) => {
    if (files && files.length > 0) {
      client.value.client_picture = files[0];
    }
  };
  
  // Fetch client data by ID
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
  
  // Watch for ID change to fetch new client data
  watch(() => props.id, (newId) => {
    if (newId) {
      fetchClientById(newId);
    }
  });
  
  // Close modal and reset client data
  const closeModal = () => {
    visible.value = false;
    resetClient();
  };
  
  // Reset client data after saving
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
  </script>
  