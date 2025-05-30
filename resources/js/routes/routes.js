import { AuthorizationMiddleware } from "@/middlewares/AuthorizationMiddleware";
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

      // Assistant login
      {
        path: "/assistant-login",
        name: "assistant.login",
        meta: {
          middleware: [GuestMiddleware],
        },
        component: () => import("../pages/auth/AssistantLoginPage.vue"),
      },
      //for guest user
      {
        path: "products",
        alias: "/product",
        name: "products",
        meta: {
          middleware: [GuestMiddleware],
        },
        component: () => import("../../../resources/js/pages/products/ProductsPage.vue"),
      },
      {
        path: "product_show",
        alias: "show_product",
        name: "products.show",
        meta: {
          middleware: [GuestMiddleware],
        },
        component: () => import("../pages/guest/products/show.vue"),
      },
      {
        path: "register",
        alias: "/",
        name: "register",
        meta: {
          middleware: [GuestMiddleware],
        },
        component: () => import("../pages/auth/SignupPage.vue"),
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
        path: "test",
        name: "test.page",

        component: () => import("../pages/test/testView.vue"),
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
        path: "reset-password/:token",
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
    path: "",
    alias: "/admin",
    component: () => import("../layouts/DashboardLayout.vue"),

    children: [
      // Profile
      {
        path: "/profile",
        component: RouterView,
        children: [
          {
            path: "",
            name: "profile.index",
            component: () => import("../pages/profile/ProfilePage.vue"),
          },
        ],
      },

      // User Orders
      {
        path: "/orders",
        component: RouterView,
        children: [
          {
            path: "",
            name: "user.orders.index",
            component: () => import("../pages/user/orders/index.vue"),
          },
          {
            path: "confirmation/:id?",
            name: "user.orders.confirmation",
            props: true,
            component: () => import("../pages/user/orders/confirmation.vue"),
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
          {
            path: "",
            name: "dashboard.index",
            component: () =>
              import("../../../resources/pages/Dashboard/Dashboard.vue"),
            meta: {
              middleware: [AuthorizationMiddleware("view-dashboard")],
            },
          },
          // for admin
          {
            path: "/my_products",
            name: "my_products",

            component: () => import("../pages/my_products/index.vue"),
          },
          {
            path: "/sells",
            name: "sells",

            component: () => import("../pages/Sells/index.vue"),
          },
          {
            path: "/bills",
            name: "bills.index",

            component: () => import("../pages/admin/bills/index.vue"),
          },
          {
            path: "/expenses",
            name: "expenses.index",

            component: () => import("../pages/admin/expenses/index.vue"),
          },
          {
            path: "/assistants/manage",
            name: "assistants.manage",
            component: () => import("../pages/assistants/old-manage.vue"),
          },
          {
            path: "/assistants",
            name: "assistants.index",

            component: () => import("../pages/assistants/old-index.vue"),
          },
          {
            path: "/user-management",
            name: "user-management",
            component: () => import("../pages/user-management/index.vue"),
          },
          {
            path: "/user-management/roles",
            name: "user-management.roles",
            component: () => import("../pages/user-management/roles.vue"),
            meta: {
              middleware: [AuthorizationMiddleware("manage-roles")]
            }
          },
          // Transaction Categories
          {
            path: "plans",
            name: "categories.index",
            // component: () => import("../pages/categories/IndexPage.vue"),
            children: [
              {
                path: "Payment_plans",
                name: "incomes.index",
                component: () => import("../../pages/plans/index.vue"),
              },
              {
                path: "Insurance",
                name: "insurance.index",
                component: () => import("../../pages/Insurance/index.vue"),
              },
              {
                path: "Insurance_plan",
                name: "expenses_plan.index",
                component: () =>
                  import("../../pages/insurance_plans/index.vue"),
              },
            ],
          },
          {
            path: "403",
            name: "unauthorized",
            component: () => import("../../pages/errors/403.vue"),
          },

          // {
          //   path: "categories/create",
          //   name: "categories.create",
          //   component: () => import("../pages/categories/CreatePage.vue"),
          // },

          // {
          //   path: "categories/:id/edit",
          //   name: "categories.edit",
          //   props: true,
          //   component: () => import("../pages/categories/EditPage.vue"),
          // },

          // Transactions
          {
            path: "transaction",
            name: "transaction.index",
            props: true,
            component: () => import("../../pages/Payments/index.vue"),

            children: [
              {
                path: "",
                name: "transaction.index",
                component: () =>
                  import("../pages/transactions/Transfer/IndexTransfer.vue"),
              },
              {
                path: "income",
                name: "transaction.income",
                component: () => import("../../pages/plans/index.vue"),
              },
              {
                path: "expense",
                name: "transaction.expense",
                component: () =>
                  import("../pages/transactions/Expense/IndexExpense.vue"),
              },
            ],
          },

          //account
          {
            path: "/clients",
            component: RouterView,
            children: [
              {
                path: "",
                name: "account.index-account",
                component: () =>
                  import("../../../resources/pages/Client/Clients.vue"),
              },
              {
                path: "show/:id",
                props: true,
                name: "account.show",
                component: () => import("../pages/accounts/ShowPage.vue"),
                children: [
                  {
                    path: "",
                    props: true,
                    name: "account.transfers",
                    component: () =>
                      import("../pages/accounts/TransfersPage.vue"),
                  },
                  {
                    props: true,
                    path: "incomes",
                    name: "account.incomes",
                    component: () =>
                      import("../pages/accounts/IncomesPage.vue"),
                  },
                  {
                    props: true,
                    path: "expenses",
                    name: "account.expenses",
                    component: () =>
                      import("../pages/accounts/ExpensesPage.vue"),
                  },
                ],
              },
            ],
          },
        ],
      },
    ],
  },
  {
    path: "/stripe",
    name: "stripe",
    component: () => import("../pages/stripe/stripeForm.vue"),
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
