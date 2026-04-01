import { createRouter, createWebHistory } from 'vue-router'

import AppShell from '@/components/layout/AppShell.vue'
import { resourceNavigation } from '@/config/resources'
import { pinia } from '@/stores'
import { useAuthStore } from '@/stores/auth'

const protectedResourceRoutes = resourceNavigation.map((resource) => ({
  path: resource.path,
  name: resource.key,
  component: () => import('@/views/app/ResourceView.vue'),
  props: { resourceKey: resource.key },
  meta: {
    requiresAuth: true,
    requiresVerified: true,
    title: resource.title,
  },
}))

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'landing',
      component: () => import('@/views/LandingView.vue'),
      meta: {
        guest: true,
        title: 'Operations cockpit',
      },
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/auth/LoginView.vue'),
      meta: {
        guest: true,
        title: 'Sign in',
      },
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('@/views/auth/RegisterView.vue'),
      meta: {
        guest: true,
        title: 'Create account',
      },
    },
    {
      path: '/forgot-password',
      name: 'forgot-password',
      component: () => import('@/views/auth/ForgotPasswordView.vue'),
      meta: {
        guest: true,
        title: 'Password recovery',
      },
    },
    {
      path: '/reset-password',
      name: 'reset-password',
      component: () => import('@/views/auth/ResetPasswordView.vue'),
      meta: {
        guest: true,
        title: 'Reset password',
      },
    },
    {
      path: '/verify-email',
      name: 'verify-email',
      component: () => import('@/views/auth/VerifyEmailView.vue'),
      meta: {
        title: 'Email verification',
      },
    },
    {
      path: '/email-confirmation',
      name: 'email-confirmation',
      component: () => import('@/views/auth/EmailConfirmationView.vue'),
      meta: {
        requiresAuth: true,
        title: 'Confirm your email',
      },
    },
    {
      path: '/app',
      component: AppShell,
      meta: {
        requiresAuth: true,
      },
      children: [
        {
          path: '',
          redirect: { name: 'dashboard' },
        },
        {
          path: 'dashboard',
          name: 'dashboard',
          component: () => import('@/views/app/DashboardView.vue'),
          meta: {
            requiresAuth: true,
            requiresVerified: true,
            title: 'Dashboard',
          },
        },
        ...protectedResourceRoutes,
      ],
    },
    {
      path: '/:pathMatch(.*)*',
      redirect: '/',
    },
  ],
})

router.beforeEach(async (to) => {
  const auth = useAuthStore(pinia)

  if (!auth.ready) {
    try {
      await auth.initialize()
    } catch (error) {
      console.error('Unable to bootstrap authentication state.', error)
    }
  }

  document.title = to.meta.title
    ? `${to.meta.title} | DropStudio Orders`
    : 'DropStudio Orders'

  if (to.meta.guest && auth.isAuthenticated) {
    return auth.isVerified ? { name: 'dashboard' } : { name: 'email-confirmation' }
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return {
      name: 'login',
      query: { redirect: to.fullPath },
    }
  }

  if (to.meta.requiresVerified && auth.isAuthenticated && !auth.isVerified) {
    return { name: 'email-confirmation' }
  }

  if (to.name === 'email-confirmation' && auth.isAuthenticated && auth.isVerified) {
    return { name: 'dashboard' }
  }

  return true
})

export default router
