<script setup>
const appVersion = 'v1.0.0';

defineProps({
    siteName: { type: String, default: '' },
    siteLogo: { type: String, default: '' },
    siteSlogan: { type: String, default: '' },
    contactEmail: { type: String, default: '' },
    contactPhone: { type: String, default: '' },
    contactAddress: { type: String, default: '' },
    contactWhatsapp: { type: String, default: '' },
    urls: { type: Object, default: () => ({}) },
    social: { type: Object, default: () => ({}) },
});
</script>

<template>
  <footer class="bg-slate-950 text-white">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
      <div class="grid gap-10 lg:grid-cols-[1.2fr_0.8fr_0.9fr_0.85fr]">
        <div class="space-y-5">
          <div class="inline-flex h-20 w-48 items-center justify-center rounded-md px-2">
            <img :src="siteLogo" :alt="siteName" :title="siteName" loading="lazy" class="max-h-12 w-full object-contain">
          </div>

          <div>
            <h3 class="premium-font-display text-2xl font-black tracking-tight text-white">{{ siteName }}</h3>
            <p class="mt-3 max-w-md text-sm leading-7 text-slate-300">{{ siteSlogan }}</p>
          </div>
        </div>

        <div>
          <h4 class="text-sm font-black uppercase tracking-[0.24em]" :style="{ color: 'var(--premium-accent)' }">Institucional</h4>
          <ul class="mt-5 space-y-3 text-sm font-medium text-slate-200">
            <li><a class="transition hover:text-white" :href="urls.home">Início</a></li>
            <li><a class="transition hover:text-white" :href="urls.biografia">Biografia</a></li>
            <li><a class="transition hover:text-white" :href="urls.agenda">Agenda</a></li>
            <li><a class="transition hover:text-white" :href="urls.blog">Blog</a></li>
            <li><a class="transition hover:text-white" :href="urls.propostas">Propostas</a></li>
            <li><a class="transition hover:text-white" :href="urls.transparencia">Transparência</a></li>
            <li><a class="transition hover:text-white" :href="urls.contato">Contato</a></li>
          </ul>
        </div>

        <div>
          <h4 class="text-sm font-black uppercase tracking-[0.24em]" :style="{ color: 'var(--premium-accent)' }">Contato</h4>
          <ul class="mt-5 space-y-4 text-sm leading-6 text-slate-300">
            <li v-if="contactEmail" class="flex gap-3"><i class="fas fa-envelope mt-1 text-slate-500"></i><span>{{ contactEmail }}</span></li>
            <li v-if="contactPhone" class="flex gap-3"><i class="fas fa-phone mt-1 text-slate-500"></i><span>{{ contactPhone }}</span></li>
            <li v-if="contactAddress" class="flex gap-3"><i class="fas fa-map-marker-alt mt-1 text-slate-500"></i><span>{{ contactAddress }}</span></li>
          </ul>
        </div>

        <div>
          <h4 class="text-sm font-black uppercase tracking-[0.24em]" :style="{ color: 'var(--premium-accent)' }">Redes</h4>
          <p class="mt-5 text-sm leading-7 text-slate-300">Canais oficiais integrados ao tema premium com destaque mais direto.</p>

          <div class="mt-5 flex flex-wrap gap-3">
            <a v-if="social.facebook" :href="social.facebook" target="_blank" rel="noopener" aria-label="Facebook" class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-white/8 text-white transition hover:-translate-y-0.5" :style="{ border: '1px solid rgba(255,255,255,0.08)' }"><i class="fab fa-facebook-f"></i></a>
            <a v-if="social.instagram" :href="social.instagram" target="_blank" rel="noopener" aria-label="Instagram" class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-white/8 text-white transition hover:-translate-y-0.5" :style="{ border: '1px solid rgba(255,255,255,0.08)' }"><i class="fab fa-instagram"></i></a>
            <a v-if="social.youtube" :href="social.youtube" target="_blank" rel="noopener" aria-label="YouTube" class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-white/8 text-white transition hover:-translate-y-0.5" :style="{ border: '1px solid rgba(255,255,255,0.08)' }"><i class="fab fa-youtube"></i></a>
            <a v-if="contactWhatsapp" :href="`https://wa.me/${contactWhatsapp}`" target="_blank" rel="noopener" aria-label="WhatsApp" class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-white/8 text-white transition hover:-translate-y-0.5" :style="{ border: '1px solid rgba(255,255,255,0.08)' }"><i class="fab fa-whatsapp"></i></a>
          </div>
        </div>
      </div>

      <div class="mt-12 border-t border-white/8 pt-6 text-sm text-slate-400">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
          <div class="text-left">
            &copy; {{ new Date().getFullYear() }} {{ siteName }} - Todos os direitos reservados
          </div>
          <div class="text-left md:text-right">
            Desenvolvido por <span class="text-white">KDK Host</span> <span class="mx-2 text-slate-600">|</span> Versão {{ appVersion }}
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>
