import { createRouter, createWebHistory } from 'vue-router';
import { useUserStore } from '../store/userStore';

const routes = [
  {
    path: '/register',
    name: 'Register',
    component: () => import('../views/RegisterView.vue'),
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/LoginView.vue'),
  },
  {
    path: '/portfolio',
    name: 'Portfolio',
    component: () => import('../views/PortfolioView.vue'),
  },
  {
    path: '/',
    redirect: '/portfolio',
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const userStore = useUserStore();
  if (userStore.isAuthenticated === null) {
    await userStore.checkAuth();
  }
  if (to.name !== 'Login' && to.name !== 'Register' && !userStore.isAuthenticated) {
    next({ name: 'Login' });
  } else {
    next();
  }
});

export default router;
