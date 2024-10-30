import { AuthMiddleware } from "../middlewares/AuthMiddleware";
import { GuestMiddleware } from "../middlewares/GuestMiddleware";
import { VerifyMiddleware } from "../middlewares/VerifyMiddleware";
import { RouterView } from "vue-router";

const routes = [
  // Auth
  {
    path: "/",
    component: () => import("../layouts/AuthLayout.vue"),

    children: [
      {
        path: "login",
        alias: "/",
        name: "login",
        meta: {
          middleware: [GuestMiddleware],
        },
        component: () => import("../pages/auth/LoginPage.vue"),
      },

      {
        path: "two-factor",
        name: "auth.two-factor",
        meta: {
          middleware: [GuestMiddleware],
        },
        component: () => import("../pages/auth/TwoFactorChallengePage.vue"),
      },

      {
        path: "password/reset",
        name: "password.reset",
        meta: {
          middleware: [GuestMiddleware],
        },
        component: () => import("../pages/auth/ForgotPasswordPage.vue"),
      },

      {
        path: "password-reset/:token",
        name: "password.reset.token",
        meta: {
          middleware: [GuestMiddleware],
        },
        props: true,
        component: () => import("../pages/auth/ResetPasswordPage.vue"),
      },
    ],
  },

  // Main
  {
    path: "/admin",
    component: () => import("../layouts/MainLayout.vue"),

    meta: {
      middleware: [AuthMiddleware],
    },

    children: [
      // Index
      {
        path: "",
        name: "dashboard",
        component: () => import("../pages/IndexPage.vue"),
      },

      // Profile
      {
        path: "profile",
        component: RouterView,

        children: [
          {
            path: "",
            name: "profile.index",
            component: () => import("../pages/profile/ProfilePage.vue"),
          },
        ],
      },

      {
        path: "",
        name: "app",
        component: RouterView,
        meta: {
          middleware: [VerifyMiddleware],
        },

        children: [
          // Transaction Categories
          {
            path: "transaction-categories",
            name: "transaction-categories.index",
            component: () => import("../pages/transaction-categories/IndexPage.vue"),
          },

          {
            path: "transaction-categories/create",
            name: "transaction-categories.create",
            component: () => import("../pages/transaction-categories/CreatePage.vue"),
          },

          {
            path: "transaction-categories/:id/edit",
            name: "transaction-categories.edit",
            props: true,
            component: () => import("../pages/transaction-categories/EditPage.vue"),
          },

          // Transactions
          {
            path: "transaction",
            name: "transaction.index",
            props: true,
            component: () => import("../pages/transaction/IndexTransaction.vue"),
          },

          // Posts
          {
            path: "posts",
            name: "posts.index",
            props: true,
            component: () => import("../pages/posts/IndexPage.vue"),
          },

          {
            path: "posts/:id",
            name: "posts.show",
            props: true,
            component: () => import("../pages/posts/ShowPage.vue"),
          },
        ],
      },
    ],
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: "/:catchAll(.*)*",
    name: "404",
    component: () => import("../pages/ErrorNotFound.vue"),
  },
];

export default routes;
