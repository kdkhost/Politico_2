<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

const props = defineProps({
    siteName: { type: String, default: '' },
    siteLogo: { type: String, default: '' },
    siteSlogan: { type: String, default: '' },
    navItems: { type: Array, default: () => [] },
    contactUrl: { type: String, default: '#' },
});

const mobileOpen = ref(false);
const displayNavItems = computed(() => props.navItems || []);

watch(mobileOpen, (isOpen) => {
    document.body.classList.toggle('premium-drawer-open', isOpen);
});

function closeMenu() {
    mobileOpen.value = false;
}

function handleResize() {
    if (window.innerWidth >= 1280) {
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
    document.body.classList.remove('premium-drawer-open');
});
</script>

<template>
  <header class="fixed inset-x-0 top-0 z-50 border-b border-white/10 bg-[color:var(--premium-surface-strong)] backdrop-blur-xl">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-2 sm:px-6 lg:px-8">
      <div class="flex items-center gap-3">
        <button
          type="button"
          class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-white/12 bg-white/[0.08] text-sm text-white shadow-[0_14px_34px_rgba(15,23,42,0.28)] transition hover:bg-white/[0.12]"
          :aria-label="mobileOpen ? 'Fechar menu' : 'Abrir menu'"
          :aria-expanded="mobileOpen"
          @click="mobileOpen = !mobileOpen"
        >
          <i :class="mobileOpen ? 'fas fa-times' : 'fas fa-bars'"></i>
        </button>

        <a href="/" class="flex min-w-0 items-center" :aria-label="siteName">
          <span class="flex h-11 w-32 shrink-0 items-center justify-center overflow-hidden rounded-md px-1.5 sm:h-12 sm:w-36">
            <img :src="siteLogo" :alt="siteName" :title="siteName" class="max-h-8 w-full object-contain sm:max-h-9">
          </span>
        </a>
      </div>

      <div class="hidden items-center xl:flex">
        <span class="rounded-full border border-white/10 bg-white/[0.06] px-3 py-1 text-[11px] font-bold uppercase tracking-[0.22em] text-slate-200">
          {{ siteSlogan || siteName }}
        </span>
      </div>

      <a
        :href="contactUrl"
        class="inline-flex min-w-[164px] items-center justify-center whitespace-nowrap rounded-full px-4 py-2 text-xs font-black text-white shadow-[0_18px_44px_rgba(0,0,0,0.24)] transition hover:-translate-y-0.5 sm:min-w-[178px] sm:px-5 sm:text-sm"
        style="background: linear-gradient(135deg, var(--premium-accent), var(--premium-secondary));"
      >
        <i class="fas fa-user-check me-2"></i>
        Apoie a campanha
      </a>
    </div>
  </header>

  <transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <button
      v-if="mobileOpen"
      type="button"
      class="fixed inset-0 z-50 bg-slate-950/72 backdrop-blur-sm"
      aria-label="Fechar menu"
      @click="closeMenu"
    ></button>
  </transition>

  <transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="-translate-x-full"
    enter-to-class="translate-x-0"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="translate-x-0"
    leave-to-class="-translate-x-full"
  >
    <aside
      v-if="mobileOpen"
      class="fixed inset-y-0 left-0 z-[60] flex w-[min(88vw,360px)] flex-col overflow-hidden border-r border-white/10 bg-[linear-gradient(180deg,var(--premium-surface-strong)_0%,var(--premium-surface)_100%)] text-white shadow-[0_28px_80px_rgba(2,6,23,0.42)]"
    >
      <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
        <a href="/" class="flex min-w-0 items-center" :aria-label="siteName" @click="closeMenu">
          <span class="flex h-12 w-36 items-center justify-center overflow-hidden rounded-md px-1">
            <img :src="siteLogo" :alt="siteName" :title="siteName" class="max-h-9 w-full object-contain">
          </span>
        </a>

        <button
          type="button"
          class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-white/12 bg-white/[0.08] text-sm text-white"
          aria-label="Fechar menu"
          @click="closeMenu"
        >
          <i class="fas fa-times"></i>
        </button>
      </div>

      <div class="border-b border-white/10 px-5 py-4">
        <div class="text-[11px] font-black uppercase tracking-[0.24em] text-slate-300">
          Navegacao
        </div>
        <p class="mt-2 text-sm leading-6 text-slate-200">
          {{ siteSlogan || siteName }}
        </p>
      </div>

      <nav aria-label="Navegacao principal" class="premium-site-drawer-scroll flex-1 overflow-y-auto px-4 py-4">
        <ul class="space-y-2">
          <li v-for="item in displayNavItems" :key="`drawer-${item.label}-${item.url}`">
            <a
              :href="item.url"
              :target="item.target || '_self'"
              :class="[
                'flex items-center justify-between rounded-2xl px-4 py-3 text-sm font-semibold transition',
                item.active
                  ? 'bg-white text-slate-950 shadow-[0_18px_40px_rgba(255,255,255,0.12)]'
                  : 'text-slate-200 hover:bg-white/8 hover:text-white'
              ]"
              @click="closeMenu"
            >
              <span class="flex items-center gap-3">
                <i :class="item.icon || 'fas fa-angle-right'" class="text-xs"></i>
                <span>{{ item.label }}</span>
              </span>
              <i class="fas fa-chevron-right text-[10px] opacity-70"></i>
            </a>
          </li>
        </ul>
      </nav>

      <div class="border-t border-white/10 p-4">
        <a
          :href="contactUrl"
          class="inline-flex w-full items-center justify-center whitespace-nowrap rounded-2xl px-5 py-3 text-sm font-black text-white"
          style="background: linear-gradient(135deg, var(--premium-accent), var(--premium-secondary));"
          @click="closeMenu"
        >
          <i class="fas fa-user-check me-2"></i>
          Apoie a campanha
        </a>
      </div>
    </aside>
  </transition>
</template>
