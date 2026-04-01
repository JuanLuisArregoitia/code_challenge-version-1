<script setup>
import { RouterLink } from 'vue-router'
</script>

<template>
  <main class="landing-shell">
    <section class="landing-hero card">
      <p class="eyebrow">Laravel API REST + VueJS SPA</p>
      <h1>A calmer control room for the operational flow.</h1>
      <p class="landing-hero__copy">
        The backend already exposes authentication, email verification, password recovery and
        the six CRUD resources from the challenge. This frontend foundation is ready to consume
        that API with Sanctum and grow into the full Step 2 experience.
      </p>

      <div class="action-bar">
        <RouterLink class="button" :to="{ name: 'login' }">Sign in</RouterLink>
        <RouterLink class="button button--ghost" :to="{ name: 'register' }">
          Create account
        </RouterLink>
      </div>
    </section>

    <section class="landing-grid">
      <article class="card">
        <p class="eyebrow">Auth flow</p>
        <h2>Register, log in, verify email and recover passwords.</h2>
        <p>
          The Vue routes already cover the full auth loop, including the views the README asks
          for.
        </p>
      </article>

      <article class="card">
        <p class="eyebrow">CRUD workspace</p>
        <h2>Products, suppliers, clients, orders and relationship resources.</h2>
        <p>
          A generic CRUD screen is wired for every resource so the next design pass can focus on
          refining UX instead of rebuilding data plumbing.
        </p>
      </article>

      <article class="card">
        <p class="eyebrow">Sanctum SPA</p>
        <h2>Session-based auth, CSRF bootstrapping and guarded routes.</h2>
        <p>
          The frontend and backend are prepared to work as a proper first-party SPA with
          credentials and protected navigation.
        </p>
      </article>
    </section>
  </main>
</template>
