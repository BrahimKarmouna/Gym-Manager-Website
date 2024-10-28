import { defineStore } from "pinia";
import { ref } from "vue";

export const useLayoutStore = defineStore("layout", () => {
  const sidebar = ref({
    opened: false,
    mini: false,
  });

  return {
    sidebar
  };
});
