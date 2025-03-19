<template>
  <div class="q-pa-md q-gutter-sm">
    <q-dialog v-model="visible" persistent transition-show="scale" transition-hide="scale">
      <q-card class="modern-card">
        <div class="header-gradient q-pa-md">
          <div class="text-h5 text-white q-ml-sm">Update Client Information</div>
          <q-btn flat round dense color="white" icon="close" @click="closeModal" />
        </div>

        <q-card-section class="q-pt-lg q-px-lg">
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6">
              <q-input dense v-model="client.Full_name" label="Full Name" filled 
                class="modern-input" :rules="[val => !!val || 'Name is required']"
                lazy-rules bottom-slots>
                <template v-slot:prepend>
                  <q-icon name="person" />
                </template>
              </q-input>
            </div>
            <div class="col-12 col-md-6">
              <q-input dense v-model="client.id_card_number" label="ID Card Number" filled 
                class="modern-input" lazy-rules>
                <template v-slot:prepend>
                  <q-icon name="badge" />
                </template>
              </q-input>
            </div>
            <div class="col-12 col-md-6">
              <q-input dense v-model="client.date_of_birth" label="Date of Birth" type="date" filled 
                class="modern-input" lazy-rules>
                <template v-slot:prepend>
                  <q-icon name="event" />
                </template>
              </q-input>
            </div>
            <div class="col-12 col-md-6">
              <q-input dense v-model="client.phone" label="Phone Number" type="tel" filled 
                class="modern-input" lazy-rules>
                <template v-slot:prepend>
                  <q-icon name="phone" />
                </template>
              </q-input>
            </div>
            <div class="col-12 col-md-6">
              <q-input dense v-model="client.email" label="Email" type="email" filled 
                class="modern-input" lazy-rules>
                <template v-slot:prepend>
                  <q-icon name="email" />
                </template>
              </q-input>
            </div>
            <div class="col-12 col-md-6">
              <q-input dense v-model="client.address" label="Address" filled 
                class="modern-input" lazy-rules>
                <template v-slot:prepend>
                  <q-icon name="home" />
                </template>
              </q-input>
            </div>
            <div class="col-12 col-md-6">
              <q-input dense v-model="client.gym_id" label="Gym ID" type="number" filled 
                class="modern-input" lazy-rules>
                <template v-slot:prepend>
                  <q-icon name="fitness_center" />
                </template>
              </q-input>
            </div>
          </div>

          <div class="row q-col-gutter-md q-mt-md">
            <div class="col-12 col-md-6">
              <q-uploader
                class="modern-uploader full-width"
                label="Upload ID Card Picture"
                accept="image/*"
                bordered
                @added="handleIdCardUpload"
                flat
                color="primary"
              >
                <template v-slot:header="scope">
                  <div class="row no-wrap items-center q-pa-sm">
                    <q-icon name="contact_page" size="28px" class="q-mr-sm" />
                    <div class="col">Upload ID Card Picture</div>
                    <q-btn v-if="scope.queuedFiles.length > 0" icon="clear_all" flat dense @click="scope.removeQueuedFiles" />
                  </div>
                </template>
              </q-uploader>
            </div>
            <div class="col-12 col-md-6">
              <q-uploader
                class="modern-uploader full-width"
                label="Upload Client Picture"
                accept="image/*"
                bordered
                @added="handleClientPictureUpload"
                flat
                color="primary"
              >
                <template v-slot:header="scope">
                  <div class="row no-wrap items-center q-pa-sm">
                    <q-icon name="photo_camera" size="28px" class="q-mr-sm" />
                    <div class="col">Upload Client Picture</div>
                    <q-btn v-if="scope.queuedFiles.length > 0" icon="clear_all" flat dense @click="scope.removeQueuedFiles" />
                  </div>
                </template>
              </q-uploader>
            </div>
          </div>
        </q-card-section>

        <q-card-actions align="right" class="q-pa-md q-pb-lg">
          <q-btn flat label="Cancel" color="grey-7" class="q-px-md" @click="closeModal" />
          <q-btn 
            unelevated 
            label="Update Client" 
            color="primary" 
            class="q-px-lg" 
            :loading="loading"
            @click="updateClient" 
          >
            <template v-slot:loading>
              <q-spinner-dots />
            </template>
          </q-btn>
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>
// Script remains the same...
import { ref, watch } from "vue";
import axios from "axios";

const props = defineProps({
  id: { type: Number, required: true },
});

const visible = defineModel("visible", { default: false, type: Boolean });

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

const loading = ref(false);

const updateClient = () => {
  if (!props.id) return;

  loading.value = true;

  const formData = new FormData();
  formData.append('Full_name', client.value.Full_name);
  formData.append('date_of_birth', client.value.date_of_birth);
  formData.append('address', client.value.address);
  formData.append('gym_id', client.value.gym_id);
  formData.append('id_card_number', client.value.id_card_number);
  formData.append('email', client.value.email);
  formData.append('phone', client.value.phone);

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

watch(() => props.id, (newId) => {
  if (newId) {
    fetchClientById(newId);
  }
});

const closeModal = () => {
  visible.value = false;
  resetClient();
};

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

<style lang="scss" scoped>
.modern-card {
  border-radius: 12px;
  overflow: hidden;
  width: 90%;
  max-width: 1000px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.header-gradient {
  background: linear-gradient(135deg, #4a6cf7 0%, #2146e8 100%);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modern-input {
  margin-bottom: 14px;
  border-radius: 8px;
  .q-field__control {
    height: 48px;
  }
}

.modern-uploader {
  border-radius: 8px;
  border: 1px dashed rgba(0,0,0,0.2);
  transition: all 0.3s;

  &:hover {
    border-color: #4a6cf7;
  }
}

:deep(.q-field--filled .q-field__control) {
  background-color: #f5f7fa;
}
</style>
  