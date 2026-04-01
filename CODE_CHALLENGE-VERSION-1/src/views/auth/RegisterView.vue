<script setup>
import { reactive, ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'

import AuthLayout from '@/components/layout/AuthLayout.vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const isSubmitting = ref(false)
const errorMessage = ref('')

async function handleSubmit() {
  isSubmitting.value = true
  errorMessage.value = ''

  try {
    await auth.register(form)
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
    title="Create your account"
    description="Registration is already connected to the Laravel API and logs the user into the SPA session."
  >
    <form class="form-grid" @submit.prevent="handleSubmit">
      <label class="field">
        <span>Name</span>
        <input v-model="form.name" class="input" type="text" placeholder="Hector Perez" />
      </label>

      <label class="field">
        <span>Email</span>
        <input v-model="form.email" class="input" type="email" placeholder="you@example.com" />
      </label>

      <label class="field">
        <span>Password</span>
        <input v-model="form.password" class="input" type="password" placeholder="Minimum 8 characters" />
      </label>

      <label class="field">
        <span>Confirm password</span>
        <input
          v-model="form.password_confirmation"
          class="input"
          type="password"
          placeholder="Repeat the password"
        />
      </label>

      <p v-if="errorMessage" class="feedback feedback--error">{{ errorMessage }}</p>

      <div class="action-bar">
        <button class="button" type="submit" :disabled="isSubmitting">
          {{ isSubmitting ? 'Creating account...' : 'Create account' }}
        </button>
      </div>
    </form>

    <template #footer>
      <p class="inline-note">
        Already registered?
        <RouterLink :to="{ name: 'login' }">Go to login</RouterLink>
      </p>
    </template>
  </AuthLayout>
</template>
