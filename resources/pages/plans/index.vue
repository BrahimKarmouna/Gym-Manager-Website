<template>
    <CreateForm v-model:visible="is_visible" />

    <q-card-section class=" pt-10 shadow-none">
        <q-btn
            class="mb-6"
            color="primary"
            label="Add Plan"
            @click="OpenModal"
        />
    </q-card-section>

    <!-- Wrapper for cards with row layout and no top margin -->
    <div class="row q-gutter-md justify-start items-start">
        <q-card
            v-for="plan in plans"
            :key="plan.id"
            flat
            bordered
            class="my-card col-12 col-sm-6 col-md-4 col-lg-3"
            :class="$q.dark.isActive ? 'bg-grey-9' : 'bg-grey-2'"
            style="margin-top: 0 !important;"
        >
            <q-card-section>
                <div class="row items-center no-wrap">
                    <div class="col">
                        <div class="text-h6">{{ plan.name }}</div>
                        <div class="text-subtitle2"> {{ plan.price }} DH</div>
                    </div>
                    <div class="col-auto">
                        <q-btn
                            color="grey-7"
                            round
                            flat
                            icon="more_vert"
                        >
                            <q-menu
                                cover
                                auto-close
                            >
                                <q-list>
                                    <q-item clickable>
                                        <q-item-section>Remove Card</q-item-section>
                                    </q-item>
                                    <q-item clickable>
                                        <q-item-section>Send Feedback</q-item-section>
                                    </q-item>
                                    <q-item clickable>
                                        <q-item-section>Share</q-item-section>
                                    </q-item>
                                </q-list>
                            </q-menu>
                        </q-btn>
                    </div>
                </div>
            </q-card-section>

            <q-card-section>
                {{ lorem }}
            </q-card-section>

            <q-separator />

            <q-card-actions>
                <q-btn
                    color="red"
                    icon="delete"
                    flat
                >Delete</q-btn>
                <q-btn
                    icon="edit"
                    color="primary"
                    flat
                >Update</q-btn>
            </q-card-actions>
        </q-card>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import CreateForm from './createForm.vue';

const is_visible = ref(false);

const OpenModal = () => {
    is_visible.value = true;
};
const plans = ref([]);

const createPlan = () => {
    axios.post('/api/plans', {
        name: 'Plan 1',
        duration: 1,
        price: 100


    }).then(response => {
        plans.value.push(response.data);
    })
};

onMounted(() => {
    axios.get('/api/plans').then(response => {
        plans.value = response.data;
    });
});

</script>
