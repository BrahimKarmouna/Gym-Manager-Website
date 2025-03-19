<template>
    <div class="q-pa-md q-gutter-sm">
        <q-dialog v-model="visible" persistent>
            <q-card class="modern-card">
                <q-card-section class="header-section bg-primary text-white">
                    <div class="text-h5 text-weight-bold">Nouveau Plan</div>
                    <q-btn flat round dense icon="close" color="white" @click="closeModal" />
                </q-card-section>

                <q-card-section class="q-pt-lg q-px-lg">
                    <div class="row q-col-gutter-lg">
                        <!-- Nom du plan -->
                        <div class="col-12">
                            <q-input
                                v-model="plan.name"
                                label="Nom du plan"
                                outlined
                                class="custom-input"
                                :rules="[val => !!val || 'Le nom est requis']"
                                bottom-slots
                            >
                                <template v-slot:prepend>
                                    <q-icon name="badge" color="primary" />
                                </template>
                            </q-input>
                        </div>

                        <!-- Durée (en mois) -->
                        <div class="col-6">
                            <q-input
                                v-model="plan.duration"
                                label="Durée (en mois)"
                                type="number"
                                outlined
                                class="custom-input"
                                :rules="[val => val > 0 || 'La durée doit être positive']"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="schedule" color="primary" />
                                </template>
                            </q-input>
                        </div>

                        <!-- Prix -->
                        <div class="col-6">
                            <q-input
                                v-model="plan.price"
                                label="Prix (DH)"
                                type="number"
                                outlined
                                class="custom-input"
                                :rules="[val => val > 0 || 'Le prix doit être positif']"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="paid" color="primary" />
                                </template>
                            </q-input>
                        </div>
                    </div>
                </q-card-section>

                <q-card-actions align="right" class="q-pa-lg">
                    <q-btn
                        flat
                        label="Annuler"
                        color="grey"
                        class="q-px-md"
                        @click="closeModal"
                    />
                    <q-btn
                        unelevated
                        label="Enregistrer"
                        color="primary"
                        class="q-px-md"
                        @click="savePlan"
                    >
                        <q-icon name="save" class="q-ml-sm" />
                    </q-btn>
                </q-card-actions>
            </q-card>
        </q-dialog>
    </div>
</template>

<style scoped>
.modern-card {
    min-width: 600px;
    border-radius: 12px;
}

.header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 12px 12px 0 0;
}

.custom-input {
    margin-bottom: 8px;
}

.custom-input :deep(.q-field__control) {
    border-radius: 8px;
}
</style>

<script setup>
import { ref } from "vue";
import axios from "axios";
import { useQuasar } from 'quasar';
const $q = useQuasar();
// Visibilité du modal
const visible = defineModel("visible", { default: false, type: Boolean });

// Modèle du plan
const plan = ref({
    name: "",
    duration: 1,
    price: 100
});

// Fermer la modal et réinitialiser le formulaire
const closeModal = () => {
    visible.value = false;
    resetForm();
};

// Réinitialisation du formulaire
const resetForm = () => {
    plan.value = { name: "", duration: 1, price: 100 };
};

// Sauvegarder le plan
const savePlan = () => {
    axios.post("/api/plans", plan.value)
        .then(() => {
            $q.notify({
                type: "positive",
                message: "Success",
                caption: "New plan saved successfully.",
                position: "bottom-right",
                timeout: 4000,
                progress: true,

            });
            closeModal(); // Fermer la modal après l'enregistrement
        })
        .catch(error => {
            console.error("Erreur lors de l'enregistrement :", error);
        });
};
</script>
