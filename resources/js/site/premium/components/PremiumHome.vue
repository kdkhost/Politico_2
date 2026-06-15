<script setup>
const props = defineProps({
    politicianName: { type: String, default: '' },
    politicianPhoto: { type: String, default: '' },
    politicianRole: { type: String, default: '' },
    slogan: { type: String, default: '' },
    stats: { type: Object, default: () => ({}) },
    propostas: { type: Array, default: () => [] },
    noticias: { type: Array, default: () => [] },
    firstEvent: { type: Object, default: null },
    urls: { type: Object, default: () => ({}) },
});
</script>

<template>
  <div class="relative overflow-hidden bg-white pt-24 sm:pt-28">
    <section class="relative overflow-hidden bg-slate-950">
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_12%_18%,rgba(255,255,255,0.12),transparent_22%),linear-gradient(90deg,rgba(2,6,23,0.95)_0%,rgba(15,23,42,0.88)_45%,rgba(15,23,42,0.58)_65%,rgba(15,23,42,0.16)_100%)]"></div>
      <div class="absolute inset-y-0 right-0 hidden w-[55%] overflow-hidden lg:block">
        <div class="absolute inset-0 rounded-bl-[180px] border-l-[10px] border-b-[10px]" style="border-color: var(--premium-accent);">
          <img :src="politicianPhoto" :alt="politicianName" class="h-full w-full object-cover">
          <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(2,6,23,0.06),rgba(2,6,23,0.34))]"></div>
        </div>
      </div>

      <div class="relative mx-auto grid min-h-[720px] max-w-7xl items-center gap-10 px-4 py-20 sm:px-6 lg:grid-cols-[minmax(0,0.95fr)_minmax(360px,0.85fr)] lg:px-8">
        <div class="max-w-2xl text-white">
          <span class="inline-flex items-center rounded-full border border-white/12 bg-white/8 px-4 py-2 text-[11px] font-black uppercase tracking-[0.24em] text-white/88">
            Liderança e presença pública
          </span>

          <h1 class="premium-font-display mt-6 text-4xl font-black leading-[0.96] tracking-tight sm:text-5xl xl:text-[4.4rem]">
            Voz forte para uma
            <span class="block" :style="{ color: 'var(--premium-accent)' }">campanha mais confiável</span>
          </h1>

          <p class="mt-6 max-w-xl text-base leading-8 text-slate-200 sm:text-lg">
            {{ slogan }}
          </p>

          <div class="mt-8 flex flex-col gap-3 sm:flex-row">
            <a
              :href="urls.propostas"
              class="inline-flex items-center justify-center rounded-full px-6 py-4 text-sm font-black text-white shadow-[0_20px_50px_rgba(0,0,0,0.26)] transition hover:-translate-y-0.5"
              style="background: var(--premium-accent);"
            >
              <i class="fas fa-bullhorn me-2"></i>
              Conheça as propostas
            </a>
            <a :href="urls.biografia" class="inline-flex items-center justify-center rounded-full border border-white/18 bg-white/8 px-6 py-4 text-sm font-black text-white transition hover:bg-white/12">
              <i class="fas fa-user-tie me-2"></i>
              Ver trajetória
            </a>
          </div>
        </div>

        <div class="relative lg:hidden">
          <div class="overflow-hidden rounded-[28px] border-4 shadow-[0_30px_80px_rgba(15,23,42,0.30)]" style="border-color: var(--premium-accent);">
            <img :src="politicianPhoto" :alt="politicianName" class="h-[28rem] w-full object-cover">
          </div>
        </div>
      </div>
    </section>

    <section class="relative z-10 -mt-14 px-4 sm:px-6 lg:px-8">
      <div class="mx-auto grid max-w-7xl gap-4 md:grid-cols-3">
        <div class="rounded-md border border-slate-200 bg-white p-6 shadow-[0_24px_60px_rgba(15,23,42,0.08)]">
          <div class="text-sm font-bold uppercase tracking-[0.18em] text-slate-500">Projetos</div>
          <div class="mt-3 text-4xl font-black text-slate-950">{{ stats.projetos }}</div>
          <p class="mt-2 text-sm text-slate-600">Iniciativas entregues com resultado concreto.</p>
        </div>
        <div class="rounded-md border border-slate-200 bg-white p-6 shadow-[0_24px_60px_rgba(15,23,42,0.08)]">
          <div class="text-sm font-bold uppercase tracking-[0.18em] text-slate-500">Alcance</div>
          <div class="mt-3 text-4xl font-black text-slate-950">{{ stats.obras }}</div>
          <p class="mt-2 text-sm text-slate-600">Pessoas impactadas por ações e presença pública.</p>
        </div>
        <div class="rounded-md border border-slate-200 bg-white p-6 shadow-[0_24px_60px_rgba(15,23,42,0.08)]">
          <div class="text-sm font-bold uppercase tracking-[0.18em] text-slate-500">Aprovação</div>
          <div class="mt-3 text-4xl font-black text-slate-950">{{ stats.anos }}</div>
          <p class="mt-2 text-sm text-slate-600">Indicador forte de confiança e credibilidade.</p>
        </div>
      </div>
    </section>

    <section class="px-4 py-18 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-7xl">
        <div class="mb-10 max-w-3xl">
          <span class="text-sm font-black uppercase tracking-[0.24em]" :style="{ color: 'var(--premium-accent)' }">Plano e prioridades</span>
          <h2 class="premium-font-display mt-3 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Por que as pessoas apoiam esta campanha</h2>
          <p class="mt-4 text-base leading-8 text-slate-600">Uma leitura mais institucional, com blocos claros, ritmo melhor e destaque real para as propostas.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
          <article
            v-for="(proposta, index) in propostas"
            :key="proposta.id ?? `${proposta.titulo}-${index}`"
            class="group rounded-md border border-slate-200 bg-white p-6 shadow-[0_20px_50px_rgba(15,23,42,0.06)] transition duration-300 hover:-translate-y-1.5 hover:shadow-[0_30px_80px_rgba(15,23,42,0.12)]"
          >
            <div class="flex items-center justify-between">
              <div class="flex h-14 w-14 items-center justify-center rounded-md text-white shadow-lg" :style="{ background: 'linear-gradient(135deg, var(--premium-accent), color-mix(in srgb, var(--premium-accent) 70%, #000000))' }">
                <i :class="[proposta.icone || 'fas fa-chart-line', 'text-lg']"></i>
              </div>
              <span class="text-sm font-black text-slate-300">{{ String(index + 1).padStart(2, '0') }}</span>
            </div>
            <h3 class="premium-font-display mt-5 text-2xl font-black tracking-tight text-slate-950">{{ proposta.titulo }}</h3>
            <p class="mt-4 text-sm leading-7 text-slate-600">{{ proposta.resumo }}</p>
          </article>
        </div>
      </div>
    </section>

    <section class="bg-slate-50 px-4 py-18 sm:px-6 lg:px-8">
      <div class="mx-auto grid max-w-7xl gap-6 xl:grid-cols-[minmax(0,1fr)_380px]">
        <div class="rounded-md border border-slate-200 bg-white p-8 shadow-[0_28px_80px_rgba(15,23,42,0.08)] sm:p-10">
          <span class="text-sm font-black uppercase tracking-[0.24em]" :style="{ color: 'var(--premium-accent)' }">Agenda pública</span>
          <h2 class="premium-font-display mt-3 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Próximos eventos</h2>
          <p class="mt-4 max-w-2xl text-base leading-8 text-slate-600">Compromissos e encontros destacados com uma estrutura mais clara e mais premium.</p>

          <div class="mt-8">
            <div v-if="firstEvent" class="grid gap-5 rounded-md border border-slate-200 bg-slate-50 p-5 sm:grid-cols-[108px_minmax(0,1fr)] sm:p-6">
              <div class="rounded-md px-4 py-5 text-center text-white shadow-[0_20px_50px_rgba(15,23,42,0.20)]" style="background: linear-gradient(180deg, var(--premium-accent), color-mix(in srgb, var(--premium-accent) 70%, #000000));">
                <strong class="block text-4xl font-black tracking-tight">{{ firstEvent.day }}</strong>
                <span class="mt-2 block text-xs font-black uppercase tracking-[0.24em] text-white/88">{{ firstEvent.month }}</span>
              </div>
              <div class="min-w-0">
                <h3 class="premium-font-display text-2xl font-black tracking-tight text-slate-950">{{ firstEvent.titulo }}</h3>
                <p class="mt-3 text-sm leading-7 text-slate-600">{{ firstEvent.local || 'Evento público com participação da população.' }}</p>
                <div class="mt-5 flex flex-wrap gap-3 text-sm font-medium text-slate-500">
                  <span class="inline-flex items-center rounded-full bg-white px-4 py-2 shadow-sm"><i class="far fa-clock me-2"></i>{{ firstEvent.time }}</span>
                  <span v-if="firstEvent.local" class="inline-flex items-center rounded-full bg-white px-4 py-2 shadow-sm"><i class="fas fa-location-dot me-2"></i>{{ firstEvent.local }}</span>
                </div>
              </div>
            </div>
            <div v-else class="rounded-md border border-slate-200 bg-white p-8 text-center">
              <h3 class="premium-font-display text-2xl font-black tracking-tight text-slate-950">Nenhum evento agendado</h3>
              <p class="mt-3 text-sm leading-7 text-slate-600">A agenda pública será atualizada em breve.</p>
            </div>
          </div>
        </div>

        <aside class="overflow-hidden rounded-md bg-slate-950 text-white shadow-[0_28px_80px_rgba(15,23,42,0.22)]">
          <div class="h-full p-6 sm:p-7">
            <span class="inline-flex rounded-full border border-white/12 bg-white/10 px-3.5 py-1.5 text-[10px] font-black uppercase tracking-[0.22em] text-white/80">Canal direto</span>
            <div class="mt-5 flex h-14 w-14 items-center justify-center rounded-md bg-white/10 text-xl text-white">
              <i class="fas fa-envelope-open-text"></i>
            </div>
            <h3 class="premium-font-display mt-5 text-2xl font-black tracking-tight sm:text-[1.75rem]">Participe e acompanhe</h3>
            <p class="mt-4 text-sm leading-8 text-slate-300">Uma área de contato mais firme visualmente, sem ruído e com chamada mais forte para ação.</p>
            <a
              :href="urls.contato"
              class="mt-6 inline-flex w-full items-center justify-center rounded-full px-5 py-3 text-sm font-black text-white transition hover:-translate-y-0.5 sm:w-auto"
              style="background: var(--premium-accent);"
            >
              Fale com o gestor
            </a>
          </div>
        </aside>
      </div>
    </section>

    <section v-if="noticias.length > 0" class="px-4 py-18 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-7xl">
        <div class="mb-10 max-w-3xl">
          <span class="text-sm font-black uppercase tracking-[0.24em]" :style="{ color: 'var(--premium-accent)' }">Atualizações</span>
          <h2 class="premium-font-display mt-3 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Últimas publicações</h2>
          <p class="mt-4 text-base leading-8 text-slate-600">Notícias e comunicados com apresentação mais editorial e menos genérica.</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
          <article
            v-for="(post, index) in noticias"
            :key="post.id ?? `${post.titulo}-${index}`"
            class="overflow-hidden rounded-md border border-slate-200/80 bg-white shadow-[0_24px_70px_rgba(15,23,42,0.08)] transition duration-300 hover:-translate-y-1.5 hover:shadow-[0_34px_90px_rgba(15,23,42,0.14)]"
          >
            <img :src="post.imagem" :alt="post.titulo" class="h-64 w-full object-cover" loading="lazy">
            <div class="p-6 sm:p-7">
              <span v-if="post.categoria" class="inline-flex rounded-full px-4 py-2 text-[11px] font-black uppercase tracking-[0.2em] text-white" :style="{ background: 'var(--premium-accent)' }">{{ post.categoria }}</span>
              <h3 class="premium-font-display mt-5 text-2xl font-black tracking-tight text-slate-950">{{ post.titulo }}</h3>
              <p class="mt-4 text-sm leading-7 text-slate-600">{{ post.resumo }}</p>
              <a :href="post.url" class="mt-6 inline-flex items-center text-sm font-black transition" :style="{ color: 'var(--premium-accent)' }">
                Ler mais <i class="fas fa-arrow-right ms-2"></i>
              </a>
            </div>
          </article>
        </div>
      </div>
    </section>
  </div>
</template>
