import { boot } from "quasar/wrappers";
import { createWebHistory, createRouter } from "vue-router";
import routes from "./routes";
import { middlewarePipeline } from "@/middlewares/middlewarePipeline";


export default boot(({ app, store }) => {
  const router = createRouter({
    history: createWebHistory(),
    routes,
  });

  router.beforeEach(async (to, from, next) => {
    if (!to.meta.middleware) {
      return next();
    }

    // Get all middlewares from matched routes
    const middleware = to.matched
      .map((route) => route.meta.middleware)
      .filter(Boolean)
      .flat();

    const context = {
      to,
      from,
      next,
      store, // | You can also pass store as an argument
    };

    return await middleware[0]({
      ...context,
      next: middlewarePipeline(context, middleware, 1),
    });
  });

  app.use(router);

  return router;
});
