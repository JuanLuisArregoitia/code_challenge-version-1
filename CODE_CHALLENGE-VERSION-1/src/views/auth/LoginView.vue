<script setup>
import { reactive, ref } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'

import AuthLayout from '@/components/layout/AuthLayout.vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const route = useRoute()
const router = useRouter()

const form = reactive({
  email: '',
  password: '',
})

const isSubmitting = ref(false)
const errorMessage = ref('')

async function handleSubmit() {
  isSubmitting.value = true
  errorMessage.value = ''

  try {
    await auth.login(form)

    if (typeof route.query.redirect === 'string' && auth.isVerified) {
      await router.push(route.query.redirect)
      return
    }

    await router.push({ name: auth.homeRouteName })
  } catch (error) {
    errorMessage.value = error.message
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <AuthLayout
    title="Sign in to the workspace"
    description="Use the same Laravel-authenticated session that will guard the CRUD routes of the SPA."
  >
    <form class="form-grid" @submit.prevent="handleSubmit">
      <label class="field">
        <span>Email</span>
        <input v-model="form.email" class="input" type="email" placeholder="you@example.com" />
      </label>

      <label class="field">
        <span>Password</span>
        <input v-model="form.password" class="input" type="password" placeholder="••••••••" />
      </label>

      <p v-if="errorMessage" class="feedback feedback--error">{{ errorMessage }}</p>

      <div class="action-bar">
        <button class="button" type="submit" :disabled="isSubmitting">
          {{ isSubmitting ? 'Signing in...' : 'Sign in' }}
        </button>
        <RouterLink class="button button--ghost" :to="{ name: 'forgot-password' }">
          Forgot password
        </RouterLink>
      </div>
    </form>

    <template #footer>
      <p class="inline-note">
        New here?
        <RouterLink :to="{ name: 'register' }">Create your account</RouterLink>
      </p>
    </template>
  </AuthLayout>
</template>
