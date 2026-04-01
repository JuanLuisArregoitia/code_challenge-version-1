import { computed, ref } from 'vue'
import { defineStore } from 'pinia'

import { ApiError, api } from '@/lib/api'

let bootstrapPromise = null

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const ready = ref(false)
  const pending = ref(false)

  const isAuthenticated = computed(() => Boolean(user.value))
  const isVerified = computed(() => Boolean(user.value?.email_verified_at))
  const homeRouteName = computed(() => (isVerified.value ? 'dashboard' : 'email-confirmation'))

  async function initialize() {
    if (ready.value) {
      return user.value
    }

    if (!bootstrapPromise) {
      bootstrapPromise = (async () => {
        try {
          const response = await api.get('/api/v1/auth/me')
          user.value = response?.user ?? null
        } catch (error) {
          if (!(error instanceof ApiError) || error.status !== 401) {
            throw error
          }

          user.value = null
        } finally {
          ready.value = true
          bootstrapPromise = null
        }

        return user.value
      })()
    }

    return bootstrapPromise
  }

  async function refreshUser() {
    const response = await api.get('/api/v1/auth/me')
    user.value = response?.user ?? null
    ready.value = true

    return user.value
  }

  async function login(credentials) {
    pending.value = true

    try {
      const response = await api.post('/api/v1/auth/login', credentials)
      user.value = response?.user ?? null
      ready.value = true

      return response
    } finally {
      pending.value = false
    }
  }

  async function register(payload) {
    pending.value = true

    try {
      const response = await api.post('/api/v1/auth/register', payload)
      user.value = response?.user ?? null
      ready.value = true

      return response
    } finally {
      pending.value = false
    }
  }

  async function logout() {
    pending.value = true

    try {
      await api.post('/api/v1/auth/logout')
    } finally {
      user.value = null
      ready.value = true
      pending.value = false
    }
  }

  async function forgotPassword(payload) {
    pending.value = true

    try {
      return await api.post('/api/v1/auth/forgot-password', payload)
    } finally {
      pending.value = false
    }
  }

  async function resetPassword(payload) {
    pending.value = true

    try {
      return await api.post('/api/v1/auth/reset-password', payload)
    } finally {
      pending.value = false
    }
  }

  async function resendVerificationEmail() {
    pending.value = true

    try {
      return await api.post('/api/v1/auth/email/verification-notification')
    } finally {
      pending.value = false
    }
  }

  async function verifyEmail({ id, hash, expires, signature }) {
    const query = new URLSearchParams({
      expires,
      signature,
    })

    const response = await api.get(`/api/v1/auth/email/verify/${id}/${hash}?${query.toString()}`)

    if (isAuthenticated.value) {
      await refreshUser()
    }

    return response
  }

  return {
    user,
    ready,
    pending,
    isAuthenticated,
    isVerified,
    homeRouteName,
    initialize,
    refreshUser,
    login,
    register,
    logout,
    forgotPassword,
    resetPassword,
    resendVerificationEmail,
    verifyEmail,
  }
})
