<script setup>
defineProps({
  eyebrow: {
    type: String,
    default: 'Step 2 / VueJS SPA',
  },
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    required: true,
  },
  asideTitle: {
    type: String,
    default: 'Operational flow with a calmer UI rhythm.',
  },
  asideText: {
    type: String,
    default:
      'This SPA is prepared to consume the Laravel API, protect routes with Sanctum and move from authentication into CRUD views without changing context.',
  },
})
</script>

<template>
  <div class="auth-shell">
    <section class="auth-story card">
      <p class="eyebrow">{{ eyebrow }}</p>
      <h1>{{ asideTitle }}</h1>
      <p class="auth-story__text">{{ asideText }}</p>

      <div class="story-metrics">
        <article>
          <strong>6</strong>
          <span>Connected resources</span>
        </article>
        <article>
          <strong>1</strong>
          <span>Session-based Sanctum flow</span>
        </article>
        <article>
          <strong>SPA</strong>
          <span>Vue views ready to extend</span>
        </article>
      </div>
    </section>

    <section class="auth-card card">
      <p class="eyebrow">{{ eyebrow }}</p>
      <h2>{{ title }}</h2>
      <p class="auth-card__description">{{ description }}</p>
      <slot />
      <slot name="footer" />
    </section>
  </div>
</template>
