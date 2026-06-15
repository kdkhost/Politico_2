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
  <header class="fixed inset-x-0 top-0 z-50 border-b border-white/8 bg-slate-950/88 backdrop-blur-xl">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
      <a href="/" class="flex min-w-0 items-center gap-3" :aria-label="siteName">
        <span class="flex h-14 w-36 shrink-0 items-center justify-center overflow-hidden rounded-md bg-white px-4 shadow-[0_16px_40px_rgba(15,23,42,0.18)] sm:h-16 sm:w-44">
          <img :src="siteLogo" :alt="siteName" :title="siteName" class="max-h-10 w-full object-contain sm:max-h-12">
        </span>

        <span class="hidden min-w-0 lg:flex lg:flex-col">
          <strong class="truncate text-base font-black tracking-tight text-white">{{ siteName }}</strong>
          <small class="truncate text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-300">{{ siteSlogan }}</small>
        </span>
      </a>

      <div class="hidden items-center gap-8 lg:flex">
        <nav aria-label="Navegação principal">
          <ul class="flex items-center gap-1">
            <li v-for="item in displayNavItems" :key="`${item.label}-${item.url}`">
              <a
                :href="item.url"
                :target="item.target || '_self'"
                :class="[
                  'inline-flex items-center px-4 py-2 text-sm font-semibold transition',
                  item.active
                    ? 'text-white'
                    : 'text-slate-300 hover:text-white'
                ]"
              >
                <span class="relative">
                  {{ item.label }}
                  <span v-if="item.active" class="absolute -bottom-2 left-0 h-0.5 w-full rounded-full" style="background: var(--premium-accent);"></span>
                </span>
              </a>
            </li>
          </ul>
        </nav>

        <a
          :href="contactUrl"
          class="inline-flex items-center justify-center rounded-full px-5 py-3 text-sm font-black text-white shadow-[0_18px_44px_rgba(0,0,0,0.24)] transition hover:-translate-y-0.5"
          style="background: var(--premium-accent);"
        >
          <i class="fas fa-user-check me-2"></i>
          Apoie a campanha
        </a>
      </div>

      <button
        type="button"
        class="inline-flex h-11 w-11 items-center justify-center rounded-md border border-white/10 bg-white/5 text-lg text-white lg:hidden"
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
      <div v-if="mobileOpen" class="border-t border-white/8 bg-slate-950 px-4 py-4 lg:hidden sm:px-6">
        <nav aria-label="Navegação principal mobile">
          <ul class="space-y-2">
            <li v-for="item in displayNavItems" :key="`mobile-${item.label}-${item.url}`">
              <a
                :href="item.url"
                :target="item.target || '_self'"
                :class="[
                  'flex items-center justify-between rounded-md px-4 py-3 text-sm font-semibold transition',
                  item.active ? 'bg-white text-slate-950' : 'text-slate-200 hover:bg-white/8 hover:text-white'
                ]"
                @click="closeMenu"
              >
                <span>{{ item.label }}</span>
                <i class="fas fa-arrow-right text-xs"></i>
              </a>
            </li>
          </ul>
        </nav>

        <a
          :href="contactUrl"
          class="mt-4 inline-flex w-full items-center justify-center rounded-full px-5 py-3 text-sm font-black text-white"
          style="background: var(--premium-accent);"
          @click="closeMenu"
        >
          <i class="fas fa-user-check me-2"></i>
          Apoie a campanha
        </a>
      </div>
    </transition>
  </header>
</template>
