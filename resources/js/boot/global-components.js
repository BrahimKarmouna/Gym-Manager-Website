import { boot } from "quasar/wrappers";
import { defineAsyncComponent } from "vue";

export default boot(({ app }) => {
  // Password
  app.component(
    "q-password",
    defineAsyncComponent(() => import("@/components/QPassword.js"))
  );
});
