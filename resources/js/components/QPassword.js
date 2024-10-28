import { QIcon, QInput } from "quasar";
import { computed, defineComponent, h, ref } from "vue";

export default defineComponent({
  name: "QPassword",

  props: {
    type: {
      type: String,
      default: "password",
    },
  },

  setup(props, { attrs }) {
    const visible = ref(props.type === "text");

    const type = computed(() => (visible.value ? "text" : "password"));

    const togglePassword = () => (visible.value = !visible.value);

    return () => {
      return h(
        QInput,
        {
          ...attrs,
          ...props,
          type: type.value,
        },
        {
          append: () =>
            h(QIcon, {
              name: visible.value ? "sym_r_visibility" : "sym_r_visibility_off",
              class: "cursor-pointer",
              size: "18px",
              onClick: togglePassword,
            }),
        }
      );
    };
  },
});
