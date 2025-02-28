<template>
    <div class="q-pa-md q-gutter-sm">
        <q-dialog
            v-model="visible"
            persistent
        >
            <q-card style="min-width: 600px; height: auto;">
                <q-card-section class="row justify-between items-center">
                    <div class="text-h6">Ajouter un Plan</div>
                    <q-btn
                        flat
                        round
                        dense
                        icon="close"
                        @click="closeModal"
                    />
                </q-card-section>

                <q-card-section class="q-pt-none">
                    <div class="row q-col-gutter-md">
                        <!-- Nom du plan -->
                        <q-input
                            v-model="plan.name"
                            label="Nom du plan"
                            filled
                            class="col-12 q-mb-md"
                        />

                        <!-- Durée (en mois) -->
                        <q-input
                            v-model="plan.duration"
                            label="Durée (en mois)"
                            type="number"
                            filled
                            class="col-6 q-mb-md"
                        />

                        <!-- Prix -->
                        <q-input
                            v-model="plan.price"
                            label="Prix (DH)"
                            type="number"
                            filled
                            class="col-6 q-mb-md"
                        />
                    </div>
                </q-card-section>

                <q-card-actions
                    align="right"
                    class="text-primary"
                >
                    <q-btn
                        flat
                        label="Annuler"
                        @click="closeModal"
                    />
                    <q-btn
                        flat
                        label="Enregistrer"
                        @click="savePlan"
                    />
                </q-card-actions>
            </q-card>
        </q-dialog>
    </div>
</template>

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
