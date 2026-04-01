<script setup>
import { reactive, ref } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'

import AuthLayout from '@/components/layout/AuthLayout.vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const route = useRoute()
const router = useRouter()

const form = reactive({
  token: typeof route.query.token === 'string' ? route.query.token : '',
  email: typeof route.query.email === 'string' ? route.query.email : '',
  password: '',
  password_confirmation: '',
})

const isSubmitting = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

async function handleSubmit() {
  isSubmitting.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const response = await auth.resetPassword(form)
    successMessage.value = response.message

    window.setTimeout(() => {
      router.push({ name: 'login' })
    }, 1200)
  } catch (error) {
    errorMessage.value = error.message
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <AuthLayout
    title="Set a new password"
    description="This route reads the reset token from the email link and posts the completed payload back to Laravel."
  >
    <form class="form-grid" @submit.prevent="handleSubmit">
      <label class="field">
        <span>Email</span>
        <input v-model="form.email" class="input" type="email" placeholder="you@example.com" />
      </label>

      <label class="field">
        <span>Token</span>
        <input v-model="form.token" class="input" type="text" placeholder="Reset token" />
      </label>

      <label class="field">
        <span>Password</span>
        <input v-model="form.password" class="input" type="password" placeholder="New password" />
      </label>

      <label class="field">
        <span>Confirm password</span>
        <input
          v-model="form.password_confirmation"
          class="input"
          type="password"
          placeholder="Repeat the new password"
        />
      </label>

      <p v-if="successMessage" class="feedback feedback--success">{{ successMessage }}</p>
      <p v-if="errorMessage" class="feedback feedback--error">{{ errorMessage }}</p>

      <div class="action-bar">
        <button class="button" type="submit" :disabled="isSubmitting">
          {{ isSubmitting ? 'Updating...' : 'Update password' }}
        </button>
        <RouterLink class="button button--ghost" :to="{ name: 'login' }">Back to login</RouterLink>
      </div>
    </form>
  </AuthLayout>
</template>
