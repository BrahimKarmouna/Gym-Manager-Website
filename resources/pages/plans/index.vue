<template>
    <div class="plans-page q-pa-md q-pt-lg">
        <div class="header-section q-mb-xl text-center">
            <h2 class="text-weight-bold text-primary q-my-none">Choose Your <span class="text-secondary">Fitness Journey</span></h2>
            <p class="text-grey-7 q-mt-sm text-subtitle1">Select the membership plan that fits your goals and lifestyle</p>
            
            <q-btn
                color="secondary"
                label="Create New Plan"
                icon-right="add_circle"
                unelevated
                rounded
                class="q-px-xl q-py-sm q-mt-md"
                @click="openModal"
            />
        </div>

   

        <!-- Plans Grid -->
        <div class="row q-col-gutter-xl" v-if="filteredPlans.length">
            <div
                v-for="plan in filteredPlans"
                :key="plan.id"
                class="col-12 col-sm-6 col-md-4"
            >
                <q-card class="plan-card">
                    <q-card-section class="plan-ribbon" :class="`bg-${getPlanColor(plan)}`">
                        <div class="ribbon-content text-white">{{ getPlanType(plan) }}</div>
                    </q-card-section>
                    
                    <q-card-section class="plan-header text-center q-pt-xl">
                        <h4 class="plan-title q-mb-sm">{{ plan.name }}</h4>
                        <div class="plan-price q-my-lg">
                            <span class="text-h3 text-weight-bold">{{ plan.price }}</span>
                            <span class="text-h5"> DH</span>
                            <div class="text-caption q-mt-xs">per {{ plan.duration || 1 }} {{ plan.duration > 1 ? 'months' : 'month' }}</div>
                        </div>
                    </q-card-section>
                    
                    <q-separator />
                    
                    <q-card-section class="q-pa-lg">
                        <p class="plan-description text-center text-grey-8">{{ plan.description || "Experience the best in fitness" }}</p>
                        
                        <div class="plan-features q-mt-lg">
                            <div class="feature-item q-py-sm" v-for="(feature, index) in getPlanFeatures(plan)" :key="index">
                                <q-icon :name="featureIcons[index % featureIcons.length]" :color="getPlanColor(plan)" size="sm" class="q-mr-sm" />
                                <span>{{ feature }}</span>
                            </div>
                        </div>
                    </q-card-section>
                    
                    <q-card-actions align="center" class="q-pa-md">
                        <q-btn rounded :color="getPlanColor(plan)" label="Choose Plan" class="full-width q-py-sm plan-button" />
                    </q-card-actions>
                    
                    <q-btn 
                        fab-mini
                        flat 
                        :color="getPlanColor(plan)"
                        icon="more_vert" 
                        class="absolute-top-right q-ma-sm"
                    >
                        <q-menu>
                            <q-list style="min-width: 150px">
                                <q-item clickable v-close-popup @click="editPlan(plan)">
                                    <q-item-section avatar><q-icon name="edit" color="primary" /></q-item-section>
                                    <q-item-section>Edit Plan</q-item-section>
                                </q-item>
                                <q-item clickable v-close-popup @click="confirmDelete(plan)">
                                    <q-item-section avatar><q-icon name="delete" color="negative" /></q-item-section>
                                    <q-item-section>Delete Plan</q-item-section>
                                </q-item>
                            </q-list>
                        </q-menu>
                    </q-btn>
                </q-card>
            </div>
        </div>
        
        <!-- Empty State -->
        <div v-else class="empty-state q-pa-xl column items-center">
            <img src="/images/empty-plans.svg" style="width: 200px; height: auto;" alt="No Plans" />
            <h5 class="text-grey-7 q-mt-xl">No membership plans available</h5>
            <q-btn color="secondary" rounded label="Create Your First Plan" class="q-mt-md q-px-xl" @click="openModal" />
        </div>
        
        <!-- Delete Confirmation Dialog -->
        <q-dialog v-model="deleteDialog" persistent>
            <q-card class="confirmation-card">
                <q-card-section class="bg-negative text-white">
                    <div class="text-h6">Delete Plan</div>
                </q-card-section>
                <q-card-section class="q-pt-lg">
                    <q-icon name="warning" color="negative" size="md" />
                    <p class="q-ml-sm inline">Are you sure you want to delete <strong>{{ planToDelete?.name }}</strong>?</p>
                    <p class="text-caption text-grey-8">This action cannot be undone.</p>
                </q-card-section>
                <q-card-actions align="right" class="q-pa-md">
                    <q-btn flat label="Cancel" color="dark" v-close-popup />
                    <q-btn unelevated label="Delete" color="negative" v-close-popup @click="deletePlan" />
                </q-card-actions>
            </q-card>
        </q-dialog>
        
        <!-- Create/Edit Plan Modal -->
        <CreateForm v-model:visible="isModalVisible" @plan-created="refreshPlans" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import CreateForm from './createForm.vue';

const isModalVisible = ref(false);
const deleteDialog = ref(false);
const plans = ref([]);
const planToDelete = ref(null);
const activeFilter = ref('all');

// Feature icons array for variety
const featureIcons = [
    'fitness_center',
    'directions_run',
    'sports_gymnastics',
    'restaurant',
    'local_drink',
    'person',
    'spa',
    'shower'
];

const filteredPlans = computed(() => {
    if (activeFilter.value === 'all') return plans.value;
    if (activeFilter.value === 'monthly') return plans.value.filter(p => p.duration === 1);
    if (activeFilter.value === 'quarterly') return plans.value.filter(p => p.duration >= 3 && p.duration < 12);
    if (activeFilter.value === 'yearly') return plans.value.filter(p => p.duration >= 12);
    return plans.value;
});

const openModal = () => {
    isModalVisible.value = true;
};

const editPlan = (plan) => {
    // Implement edit functionality
    console.log('Edit plan:', plan);
};

const confirmDelete = (plan) => {
    planToDelete.value = plan;
    deleteDialog.value = true;
};

const deletePlan = () => {
    // Implement delete functionality
    console.log('Deleting plan:', planToDelete.value);
};

const refreshPlans = () => {
    axios.get('/api/plans').then(response => {
        plans.value = response.data;
    });
};

// Helper functions for UI display
const getPlanColor = (plan) => {
    if (!plan.duration) return 'primary';
    if (plan.duration === 1) return 'teal';
    if (plan.duration <= 3) return 'secondary';
    if (plan.duration <= 6) return 'deep-orange';
    return 'purple';
};

const getPlanType = (plan) => {
    if (!plan.duration) return 'Basic';
    if (plan.duration === 1) return 'Monthly';
    if (plan.duration === 3) return 'Quarterly';
    if (plan.duration === 6) return 'Semi-Annual';
    if (plan.duration === 12) return 'Annual';
    return `${plan.duration}-Month`;
};

const getPlanFeatures = (plan) => {
    const defaultFeatures = [
        'Full gym access',
        'Locker rooms & showers',
        'Standard fitness assessment'
    ];
    
    const extendedFeatures = [
        ...defaultFeatures,
        'Unlimited group classes',
        'Fitness app access'
    ];
    
    const premiumFeatures = [
        ...extendedFeatures,
        '2 personal training sessions',
        'Nutrition consultation',
        'Massage therapy discount'
    ];
    
    if (plan.duration >= 12) return premiumFeatures;
    if (plan.duration >= 3) return extendedFeatures;
    return defaultFeatures;
};

onMounted(refreshPlans);
</script>

<style scoped>
.plans-page {
    max-width: 1200px;
    margin: 0 auto;
    background: #f9f9f9;
    min-height: 100vh;
}

.filter-chip {
    font-weight: 500;
    padding: 12px 16px;
}

.plan-card {
    height: 100%;
    border-radius: 20px;
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
}

.plan-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.plan-ribbon {
    position: absolute;
    top: 20px;
    right: -30px;
    width: 150px;
    padding: 5px 0;
    transform: rotate(45deg);
    text-align: center;
    z-index: 1;
}

.ribbon-content {
    font-weight: 600;
    font-size: 0.9rem;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.plan-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #333;
}

.plan-description {
    font-style: italic;
    font-size: 0.95rem;
    min-height: 40px;
}

.feature-item {
    display: flex;
    align-items: center;
    margin: 10px 0;
    font-size: 0.95rem;
}

.plan-button {
    font-weight: 500;
    letter-spacing: 1px;
}

.empty-state {
    text-align: center;
    margin: 50px auto;
    border-radius: 20px;
    background: white;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
}

.confirmation-card {
    border-radius: 15px;
    overflow: hidden;
    min-width: 400px;
}

.text-secondary {
    background: linear-gradient(135deg, #ff9800, #f44336);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>
