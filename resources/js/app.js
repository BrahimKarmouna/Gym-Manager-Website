import "./bootstrap";
import { createApp } from "vue";
import { createPinia } from "pinia";
import { Dark, Dialog, Notify, Quasar } from "quasar";
// Import icon libraries
import "@quasar/extras/material-icons/material-icons.css";
import iconSet from "quasar/icon-set/material-symbols-rounded";
// Import Quasar css
import "quasar/src/css/index.sass";
import "../scss/app.scss";
import VueApexCharts from "vue3-apexcharts";
// Assumes your root component is App.vue
// and placed in same folder as main.js
import App from "./App.vue";
import router from "./routes";
import axios from "./boot/axios";
import errorHandler from "./boot/error-handler";
import globalComponents from "./boot/global-components";
import { usePermissions } from "./composables/usePermissions";
const pinia = createPinia();
const app = createApp(App);
// Import permissions

app.use(Quasar, {
  plugins: {
    Notify: Notify,
    Dark: Dark,
    Dialog: Dialog,
  }, // import Quasar plugins and add here
  iconSet,
});

app.use(pinia);
app.use(VueApexCharts);


app.use({
  install(app) {
    const context = {
      store: pinia,
      app,
    };

    const _router = router(context);

    context.router = _router;

    axios(context);
    errorHandler(context);
    globalComponents(context);
  },
});

const { hasPermission } = usePermissions();
app.config.globalProperties.$permission = hasPermission;

// Assumes you have a <div id="app"></div> in your index.html
app.mount("#app");
