import { boot } from "quasar/wrappers";

export default boot(({ app }) => {
  app.config.errorHandler = (err, vm, info) => {
    console.error(err, vm, info);
  };
});
