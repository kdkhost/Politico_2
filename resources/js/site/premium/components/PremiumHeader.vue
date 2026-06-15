<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    siteName: { type: String, default: '' },
    siteLogo: { type: String, default: '' },
    siteSlogan: { type: String, default: '' },
    navItems: { type: Array, default: () => [] },
    contactUrl: { type: String, default: '#' },
});

const mobileOpen = ref(false);

const displayNavItems = computed(() => props.navItems || []);

function closeMenu() {
    mobileOpen.value = false;
}

function handleResize() {
    if (window.innerWidth >= 1024) {
        closeMenu();
    }
}

function handleKeydown(event) {
    if (event.key === 'Escape') {
        closeMenu();
    }
}

onMounted(() => {
    window.addEventListener('resize', handleResize);
    document.addEventListener('keydown', handleKeydown);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize);
    document.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
  <header class="fixed inset-x-0 top-0 z-50 px-4 pt-4 sm:px-6">
    <div class="mx-auto max-w-7xl">
      <div class="rounded-[30px] border border-white/12 bg-slate-950/86 px-4 py-3 shadow-[0_30px_100px_rgba(15,23,42,0.35)] backdrop-blur-2xl sm:px-5 lg:px-6">
        <div class="flex items-center justify-between gap-4">
          <a href="/" class="flex min-w-0 items-center gap-3" :aria-label="siteName">
            <span class="flex h-14 w-36 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white px-4 shadow-[0_18px_40px_rgba(15,23,42,0.18)] sm:h-16 sm:w-44">
              <img :src="siteLogo" :alt="siteName" :title="siteName" class="max-h-10 w-full object-contain sm:max-h-12">
            </span>

            <span class="hidden min-w-0 lg:flex lg:flex-col">
              <strong class="truncate text-base font-black tracking-tight text-white">{{ siteName }}</strong>
              <small class="truncate text-[11px] font-semibold uppercase tracking-[0.20em] text-slate-300">{{ siteSlogan }}</small>
            </span>
          </a>

          <div class="hidden items-center gap-3 lg:flex">
            <nav aria-label="Navegação principal">
              <ul class="flex items-center gap-1 rounded-[22px] border border-white/10 bg-white/6 p-2">
                <li v-for="item in displayNavItems" :key="`${item.label}-${item.url}`">
                  <a
                    :href="item.url"
                    :target="item.target || '_self'"
                    :class="[
                      'inline-flex items-center rounded-2xl px-4 py-3 text-sm font-semibold transition duration-200',
                      item.active
                        ? 'bg-white text-slate-950 shadow-[0_14px_30px_rgba(255,255,255,0.12)]'
                        : 'text-slate-200 hover:bg-white/10 hover:text-white'
                    ]"
                  >
                    {{ item.label }}
                  </a>
                </li>
              </ul>
            </nav>

            <a :href="contactUrl" class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-black text-slate-950 shadow-[0_18px_40px_rgba(255,255,255,0.16)] transition hover:-translate-y-0.5 hover:bg-slate-100">
              <i class="fas fa-user-check me-2"></i>
              Quero participar
            </a>
          </div>

          <button
            type="button"
            class="inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-white/8 text-lg text-white lg:hidden"
            :aria-label="mobileOpen ? 'Fechar menu' : 'Abrir menu'"
            :aria-expanded="mobileOpen"
            @click="mobileOpen = !mobileOpen"
          >
            <i :class="mobileOpen ? 'fas fa-times' : 'fas fa-bars'"></i>
          </button>
        </div>

        <transition
          enter-active-class="transition duration-200 ease-out"
          enter-from-class="opacity-0 -translate-y-2"
          enter-to-class="opacity-100 translate-y-0"
          leave-active-class="transition duration-150 ease-in"
          leave-from-class="opacity-100 translate-y-0"
          leave-to-class="opacity-0 -translate-y-2"
        >
          <div v-if="mobileOpen" class="pt-4 lg:hidden">
            <div class="rounded-[24px] border border-white/10 bg-white/6 p-3">
              <div class="mb-4 flex items-center gap-3 rounded-2xl bg-white/6 p-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/12 text-white">
                  <i class="fas fa-building"></i>
                </span>
                <div class="min-w-0">
                  <strong class="block truncate text-sm font-black text-white">{{ siteName }}</strong>
                  <small class="block truncate text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-300">{{ siteSlogan }}</small>
                </div>
              </div>

              <nav aria-label="Navegação principal mobile">
                <ul class="space-y-2">
                  <li v-for="item in displayNavItems" :key="`mobile-${item.label}-${item.url}`">
                    <a
                      :href="item.url"
                      :target="item.target || '_self'"
                      :class="[
                        'flex items-center justify-between rounded-2xl px-4 py-3 text-sm font-semibold transition',
                        item.active ? 'bg-white text-slate-950' : 'text-slate-200 hover:bg-white/10 hover:text-white'
                      ]"
                      @click="closeMenu"
                    >
                      <span>{{ item.label }}</span>
                      <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                  </li>
                </ul>
              </nav>

              <a :href="contactUrl" class="mt-4 inline-flex w-full items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-black text-slate-950" @click="closeMenu">
                <i class="fas fa-user-check me-2"></i>
                Quero participar
              </a>
            </div>
          </div>
        </transition>
      </div>
    </div>
  </header>
</template>
