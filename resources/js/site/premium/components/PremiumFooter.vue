<script setup>
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
  <footer class="relative px-4 pb-8 pt-16 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl overflow-hidden rounded-[32px] border border-slate-200/70 bg-white shadow-[0_36px_90px_rgba(15,23,42,0.10)]">
      <div class="bg-[radial-gradient(circle_at_top_right,_rgba(15,23,42,0.05),_transparent_34%),linear-gradient(135deg,_rgba(255,255,255,0.98),_rgba(248,250,252,0.96))] px-6 py-10 sm:px-8 lg:px-10">
        <div class="grid gap-10 lg:grid-cols-[1.15fr_0.8fr_0.95fr_0.85fr]">
          <div class="space-y-5">
            <div class="inline-flex h-20 w-48 items-center justify-center rounded-[24px] bg-slate-950 px-5 shadow-[0_24px_70px_rgba(15,23,42,0.18)]">
              <img :src="siteLogo" :alt="siteName" :title="siteName" loading="lazy" class="max-h-12 w-full object-contain">
            </div>

            <div>
              <h3 class="premium-font-display text-2xl font-black tracking-tight text-slate-950">{{ siteName }}</h3>
              <p class="mt-3 max-w-md text-sm leading-7 text-slate-600">{{ siteSlogan }}</p>
            </div>
          </div>

          <div>
            <h4 class="text-sm font-black uppercase tracking-[0.22em] text-slate-500">Institucional</h4>
            <ul class="mt-5 space-y-3 text-sm font-medium text-slate-700">
              <li><a class="transition hover:text-slate-950" :href="urls.home">Início</a></li>
              <li><a class="transition hover:text-slate-950" :href="urls.biografia">Biografia</a></li>
              <li><a class="transition hover:text-slate-950" :href="urls.propostas">Propostas</a></li>
              <li><a class="transition hover:text-slate-950" :href="urls.transparencia">Transparência</a></li>
            </ul>
          </div>

          <div>
            <h4 class="text-sm font-black uppercase tracking-[0.22em] text-slate-500">Contato</h4>
            <ul class="mt-5 space-y-4 text-sm leading-6 text-slate-600">
              <li v-if="contactEmail" class="flex gap-3"><i class="fas fa-envelope mt-1 text-slate-400"></i><span>{{ contactEmail }}</span></li>
              <li v-if="contactPhone" class="flex gap-3"><i class="fas fa-phone mt-1 text-slate-400"></i><span>{{ contactPhone }}</span></li>
              <li v-if="contactAddress" class="flex gap-3"><i class="fas fa-map-marker-alt mt-1 text-slate-400"></i><span>{{ contactAddress }}</span></li>
            </ul>
          </div>

          <div>
            <h4 class="text-sm font-black uppercase tracking-[0.22em] text-slate-500">Canais</h4>
            <p class="mt-5 text-sm leading-7 text-slate-600">Acompanhe os canais oficiais e receba atualizações.</p>

            <div class="mt-5 flex flex-wrap gap-3">
              <a v-if="social.facebook" :href="social.facebook" target="_blank" rel="noopener" aria-label="Facebook" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-950 hover:text-white"><i class="fab fa-facebook-f"></i></a>
              <a v-if="social.instagram" :href="social.instagram" target="_blank" rel="noopener" aria-label="Instagram" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-950 hover:text-white"><i class="fab fa-instagram"></i></a>
              <a v-if="social.youtube" :href="social.youtube" target="_blank" rel="noopener" aria-label="YouTube" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-950 hover:text-white"><i class="fab fa-youtube"></i></a>
              <a v-if="contactWhatsapp" :href="`https://wa.me/${contactWhatsapp}`" target="_blank" rel="noopener" aria-label="WhatsApp" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-950 hover:text-white"><i class="fab fa-whatsapp"></i></a>
            </div>
          </div>
        </div>
      </div>

      <div class="border-t border-slate-200/80 bg-slate-50 px-6 py-5 text-center text-sm text-slate-500 sm:px-8 lg:px-10">
        &copy; {{ new Date().getFullYear() }} {{ siteName }} - Todos os direitos reservados
      </div>
    </div>
  </footer>
</template>
