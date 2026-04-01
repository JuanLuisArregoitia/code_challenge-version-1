<script setup>
import { reactive, ref } from 'vue'
import { RouterLink } from 'vue-router'

import AuthLayout from '@/components/layout/AuthLayout.vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

const form = reactive({
  email: '',
})

const isSubmitting = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

async function handleSubmit() {
  isSubmitting.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const response = await auth.forgotPassword(form)
    successMessage.value = response.message
  } catch (error) {
    errorMessage.value = error.message
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <AuthLayout
    title="Recover your password"
    description="This screen triggers the reset email flow and uses the frontend reset view as the destination URL."
  >
    <form class="form-grid" @submit.prevent="handleSubmit">
      <label class="field">
        <span>Email</span>
        <input v-model="form.email" class="input" type="email" placeholder="you@example.com" />
      </label>

      <p v-if="successMessage" class="feedback feedback--success">{{ successMessage }}</p>
      <p v-if="errorMessage" class="feedback feedback--error">{{ errorMessage }}</p>

      <div class="action-bar">
        <button class="button" type="submit" :disabled="isSubmitting">
          {{ isSubmitting ? 'Sending...' : 'Send recovery link' }}
        </button>
        <RouterLink class="button button--ghost" :to="{ name: 'login' }">Back to login</RouterLink>
      </div>
    </form>
  </AuthLayout>
</template>
