import { createRouter, createWebHistory } from 'vue-router';
import HomePage from '../pages/HomePage.vue';
import LoginPage from '../pages/auth/LoginPage.vue';
import SignupPage from '../pages/auth/SignupPage.vue';
import DashboardPage from '../pages/dashboard/DashboardPage.vue';

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomePage,
    meta: {
      title: 'Home',
    },
  },
  {
    path: '/login',
    name: 'login',
    component: LoginPage,
    meta: {
      title: 'Login',
    },
  },
  {
    path: '/signup',
    name: 'signup',
    component: SignupPage,
    meta: {
      title: 'Sign Up',
    },
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: DashboardPage,
    meta: {
      requiresAuth: true,
      title: 'Dashboard',
    },
  },
  {
    path: '/products',
    name: 'products',
    component: () => import('@/pages/products/ProductsPage.vue'),
    meta: {
      title: 'Fitness Products',
    }
  },
  {
    path: '/products/:id',
    name: 'product.show',
    component: () => import('@/pages/products/ProductPage.vue'),
    props: true,
    meta: {
      title: 'Product Details',
    }
  },
  {
    path: '/cart',
    name: 'cart',
    component: () => import('../pages/cart/CartPage.vue'),
    meta: {
      title: 'Your Cart',
    }
  },
  {
    path: '/checkout',
    name: 'checkout',
    component: () => import('../pages/checkout/CheckoutPage.vue'),
    meta: {
      requiresAuth: true,
      title: 'Checkout',
    }
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  document.title = to.meta.title || 'Gym Manager';
  next();
});

export default router;