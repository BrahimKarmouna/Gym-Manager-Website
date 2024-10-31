<template>
  <div>
    <q-dialog v-model="visible" persistent>
      <q-card style="height: 600px;  width: 3000px;">
        <q-card-section>
          <div class="text-h6">Enter Your Account Inormations </div>
        </q-card-section>

        <q-card-section class="q-mb-md">
         <div class="row q-col-gutter-md">
           <q-input class="col-6" dense label="Account Name" outlined v-model="address" autofocus @keyup.enter="prompt = false" />
           <q-input class="col-6" dense label="Account Balance" outlined v-model="address" autofocus @keyup.enter="prompt = false" />
           <q-input class="col-12" dense label="Account RIB" outlined v-model="address" autofocus @keyup.enter="prompt = false" />
           <div class="col-12">
            <q-select
              v-model="model"
              :options="accountTypes"
              label="Account Type"
              outlined
              dense
              />
            </div>
         </div>
        </q-card-section>

        <q-card-actions align="right" class="text-primary">
          <q-btn flat label="Cancel" v-close-popup />
          <q-btn color="primary" label="Add new account" @click="handleCreate" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>

// const emit = defineEmits("created");

const visible = defineModel("visible", { default: false, type: Boolean });

const accountTypes = [
  { label: "Savings", value: "savings" },
  { label: "Investment", value: "investment" },
  { label: "Credit", value: "credit" },
  { label: "Cash", value: "cash" },
  { label: "Retirement", value: "retirement" },
];

// const data = ref();

// const { create, creation, validation } = useResource

// const handleCreate = async () => {
//   await create(data.value);

//   resetForm();
//   emit("created");
// }
</script>
