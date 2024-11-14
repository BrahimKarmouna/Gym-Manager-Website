<template>
  <q-item v-if="!children"
          clickable
          :target="internalTarget"
          :href="href"
          :to="to"
          :exact="exact"
          dense
          class="rounded-md q-px-sm text-bold">
    <q-item-section v-if="icon"
                    style="min-width: 8px"
                    avatar>
      <q-icon :name="icon"
              size="25px"
              class="dark:text-blue-400" />
    </q-item-section>

    <q-item-section>
      <q-item-label class="text-blue-900 dark:text-blue-400">
        {{ title }}
      </q-item-label>

      <q-item-label v-if="caption"
                    caption>
        {{ caption }}
      </q-item-label>
    </q-item-section>
  </q-item>

  <q-expansion-item v-else
                    :label="title"
                    :caption="caption"
                    :default-opened="false"
                    dense
                    header-class="rounded-md q-px-sm dark:text-blue-400"
                    class="dark:text-blue-400">
    <template #header>
      <q-item-section v-if="icon"
                      style="min-width: 8px"
                      avatar
                      class="dark:text-blue-400">
        <q-icon :name="icon"
                size="25px" />
      </q-item-section>

      <q-item-section>
        <q-item-label class="text-bold dark:text-blue-400">{{ title }}</q-item-label>

        <q-item-label v-if="caption"
                      caption>
          {{ caption }}
        </q-item-label>
      </q-item-section>
    </template>

    <q-list class="q-py-sm text-gray-500 q-gutter-xs">
      <EssentialLink v-for="child in children"
                     :key="child.title"
                     class="ps-8  dark:text-blue-400"
                     v-bind="child" />
    </q-list>
  </q-expansion-item>
</template>

<script>
import { useQuasar } from 'quasar';
import { defineComponent, computed } from 'vue'

export default defineComponent({
  name: 'EssentialLink',
  props: {
    title: {
      type: String,
      required: true
    },

    caption: {
      type: String,
      default: ''
    },

    href: {
      type: String,
    },

    children: {
      type: Array,
    },

    to: {
      type: [String, Object],
    },

    exact: {
      type: Boolean,
      default: false
    },

    target: {
      type: String,
    },

    icon: {
      type: String,
      default: ''
    }
  },

  setup(props) {
    const internalTarget = computed(() => {
      return props.target || (props.href ? '_blank' : '_self');
    });

    return {
      internalTarget
    }
  }
});
</script>

<style scoped lang="scss">
.q-item,
:deep(.q-item) {
  height: 40px;

  &.q-router-link--active {
    background-color: rgba($color: $grey-4, $alpha: 0.9);
    color: $grey-10;
  }
}
</style>
