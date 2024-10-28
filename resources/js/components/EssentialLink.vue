<template>
  <q-item clickable
          tag="a"
          :target="internalTarget"
          :href="href"
          :to="to"
          :exact="exact"
          dense
          class="rounded-md q-px-sm">
    <q-item-section v-if="icon"
                    style="min-width: 8px"
                    avatar>
      <q-icon :name="icon"
              size="xs" />
    </q-item-section>

    <q-item-section>
      <q-item-label>{{ title }}</q-item-label>

      <q-item-label v-if="caption"
                    caption>
        {{ caption }}
      </q-item-label>
    </q-item-section>
  </q-item>
</template>

<script>
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
.q-item {
  // height: 40px;

  &.q-router-link--active {
    background-color: rgba($color: $grey-4, $alpha: 0.9);
    color: $grey-10;
  }
}
</style>
