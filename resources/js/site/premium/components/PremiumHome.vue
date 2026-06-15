<script setup>
defineProps({
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
  <div class="relative overflow-hidden pt-28 sm:pt-32">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[42rem] bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.16),_transparent_28%),linear-gradient(135deg,_color-mix(in_srgb,var(--premium-primary)_92%,#020617)_0%,_#0f172a_54%,_color-mix(in_srgb,var(--premium-secondary)_40%,#020617)_100%)]"></div>
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_82%_18%,_rgba(255,255,255,0.12),_transparent_18%),radial-gradient(circle_at_12%_78%,_color-mix(in_srgb,var(--premium-secondary)_14%,transparent),_transparent_24%)]"></div>

    <section class="relative px-4 pb-16 sm:px-6 lg:px-8">
      <div class="mx-auto grid max-w-7xl items-center gap-8 lg:grid-cols-[minmax(0,1.02fr)_minmax(340px,0.98fr)] lg:gap-10">
        <div class="text-white">
          <div class="inline-flex flex-wrap gap-2">
            <span class="rounded-full border border-white/14 bg-white/8 px-4 py-2 text-[11px] font-black uppercase tracking-[0.24em] text-white/90">Excelência</span>
            <span class="rounded-full border border-white/14 bg-white/8 px-4 py-2 text-[11px] font-black uppercase tracking-[0.24em] text-white/90">Resultados</span>
            <span class="rounded-full border border-white/14 bg-white/8 px-4 py-2 text-[11px] font-black uppercase tracking-[0.24em] text-white/90">Transparência</span>
          </div>

          <div class="mt-7">
            <span class="inline-flex items-center gap-3 text-[11px] font-black uppercase tracking-[0.28em] text-white/70">
              <span class="h-px w-10 bg-white/35"></span>
              Institucional premium
            </span>

            <h1 class="premium-font-display mt-5 max-w-4xl text-4xl font-black leading-[0.98] tracking-tight sm:text-5xl xl:text-6xl">
              Um frontend mais elegante,
              <span class="mt-2 block bg-[linear-gradient(135deg,#ffffff_0%,#cbd5e1_38%,#93c5fd_74%,#bfdbfe_100%)] bg-clip-text text-transparent">
                sofisticado e atual
              </span>
            </h1>

            <p class="mt-6 max-w-2xl text-base leading-8 text-slate-200 sm:text-lg">{{ slogan }}</p>
          </div>

          <div class="mt-8 flex flex-col gap-3 sm:flex-row">
            <a :href="urls.propostas" class="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-4 text-sm font-black text-slate-950 shadow-[0_24px_60px_rgba(255,255,255,0.16)] transition hover:-translate-y-0.5 hover:bg-slate-100">
              <i class="fas fa-chalkboard-user me-2"></i>
              Conheça as propostas
            </a>
            <a :href="urls.biografia" class="inline-flex items-center justify-center rounded-2xl border border-white/18 bg-white/8 px-6 py-4 text-sm font-black text-white backdrop-blur-xl transition hover:-translate-y-0.5 hover:bg-white/12">
              <i class="fas fa-user-tie me-2"></i>
              Ver trajetória
            </a>
          </div>

          <div class="mt-10 grid gap-4 sm:grid-cols-3">
            <div class="rounded-[26px] border border-white/12 bg-white/8 p-5 backdrop-blur-xl">
              <div class="text-3xl font-black tracking-tight">{{ stats.projetos }}</div>
              <div class="mt-2 text-sm text-slate-200">Projetos concluídos</div>
            </div>
            <div class="rounded-[26px] border border-white/12 bg-white/8 p-5 backdrop-blur-xl">
              <div class="text-3xl font-black tracking-tight">{{ stats.obras }}</div>
              <div class="mt-2 text-sm text-slate-200">Cidadãos atendidos</div>
            </div>
            <div class="rounded-[26px] border border-white/12 bg-white/8 p-5 backdrop-blur-xl">
              <div class="text-3xl font-black tracking-tight">{{ stats.anos }}</div>
              <div class="mt-2 text-sm text-slate-200">Índice de satisfação</div>
            </div>
          </div>
        </div>

        <div class="relative">
          <div class="overflow-hidden rounded-[34px] border border-white/12 bg-white/8 p-3 shadow-[0_40px_110px_rgba(15,23,42,0.34)] backdrop-blur-xl">
            <div class="absolute left-6 top-6 z-10 inline-flex items-center gap-2 rounded-full bg-slate-950/74 px-4 py-2 text-[11px] font-black uppercase tracking-[0.18em] text-white backdrop-blur-xl">
              <i class="fas fa-award"></i>
              Destaque institucional
            </div>

            <div class="overflow-hidden rounded-[28px] bg-slate-200">
              <img :src="politicianPhoto" :alt="politicianName" class="h-[28rem] w-full object-cover sm:h-[36rem]">
            </div>

            <div class="absolute inset-x-6 bottom-6 rounded-[24px] bg-[linear-gradient(180deg,rgba(15,23,42,0.20),rgba(15,23,42,0.88))] p-5 text-white backdrop-blur-xl">
              <div class="text-[11px] font-black uppercase tracking-[0.20em] text-slate-300">{{ politicianRole }}</div>
              <h2 class="premium-font-display mt-3 text-2xl font-black tracking-tight">{{ politicianName }}</h2>
              <p class="mt-3 text-sm leading-7 text-slate-200">Identidade visual refinada, leitura forte e presença pública com acabamento premium.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="relative px-4 py-16 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-7xl rounded-[32px] border border-slate-200/70 bg-white/94 p-8 shadow-[0_36px_90px_rgba(15,23,42,0.10)] sm:p-10 lg:p-12">
        <div class="mx-auto max-w-3xl text-center">
          <span class="inline-flex items-center gap-3 text-[11px] font-black uppercase tracking-[0.30em] text-slate-500">
            <span class="h-px w-10 bg-slate-300"></span>
            Pilares de atuação
            <span class="h-px w-10 bg-slate-300"></span>
          </span>
          <h2 class="premium-font-display mt-5 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl lg:text-5xl">Agenda visual mais nobre</h2>
          <p class="mt-5 text-base leading-8 text-slate-600">Cards maiores, respiro melhor, contraste correto e um desenho mais sofisticado sem trocar os dados do site.</p>
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
          <article v-for="(proposta, index) in propostas" :key="proposta.id ?? `${proposta.titulo}-${index}`" class="group relative overflow-hidden rounded-[28px] border border-slate-200 bg-slate-50/80 p-6 transition duration-300 hover:-translate-y-1.5 hover:bg-white hover:shadow-[0_30px_80px_rgba(15,23,42,0.12)]">
            <span class="absolute right-5 top-5 text-4xl font-black tracking-tight text-slate-200">{{ String(index + 1).padStart(2, '0') }}</span>
            <div class="relative flex h-16 w-16 items-center justify-center rounded-[22px] bg-[linear-gradient(135deg,color-mix(in_srgb,var(--premium-primary)_12%,#ffffff),color-mix(in_srgb,var(--premium-secondary)_10%,#ffffff))] text-slate-900 shadow-inner">
              <i :class="[proposta.icone || 'fas fa-chart-line', 'text-xl']"></i>
            </div>
            <h3 class="premium-font-display mt-6 text-2xl font-black tracking-tight text-slate-950">{{ proposta.titulo }}</h3>
            <p class="mt-4 text-sm leading-7 text-slate-600">{{ proposta.resumo }}</p>
          </article>
        </div>
      </div>
    </section>

    <section class="relative px-4 pb-16 sm:px-6 lg:px-8">
      <div class="mx-auto grid max-w-7xl gap-6 xl:grid-cols-[minmax(0,1.05fr)_400px]">
        <div class="rounded-[32px] border border-slate-200/70 bg-white/94 p-8 shadow-[0_36px_90px_rgba(15,23,42,0.10)] sm:p-10">
          <span class="inline-flex items-center gap-3 text-[11px] font-black uppercase tracking-[0.30em] text-slate-500">
            <span class="h-px w-10 bg-slate-300"></span>
            Presença pública
          </span>
          <h2 class="premium-font-display mt-5 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl lg:text-5xl">Próximos eventos</h2>
          <p class="mt-5 max-w-2xl text-base leading-8 text-slate-600">Acompanhe a agenda pública com melhor leitura e composição visual.</p>

          <div class="mt-8 rounded-[28px] border border-slate-200 bg-slate-50 p-5 shadow-inner sm:p-6">
            <div v-if="firstEvent" class="grid gap-5 sm:grid-cols-[104px_minmax(0,1fr)] sm:items-start">
              <div class="rounded-[24px] bg-slate-950 px-4 py-5 text-center text-white shadow-[0_24px_70px_rgba(15,23,42,0.24)]">
                <strong class="block text-4xl font-black tracking-tight">{{ firstEvent.day }}</strong>
                <span class="mt-2 block text-xs font-black uppercase tracking-[0.24em] text-slate-300">{{ firstEvent.month }}</span>
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
            <div v-else class="rounded-[24px] bg-white p-8 text-center">
              <h3 class="premium-font-display text-2xl font-black tracking-tight text-slate-950">Nenhum evento agendado</h3>
              <p class="mt-3 text-sm leading-7 text-slate-600">A agenda pública será atualizada em breve.</p>
            </div>
          </div>
        </div>

        <aside class="rounded-[32px] border border-slate-200/70 bg-slate-950 p-8 text-white shadow-[0_36px_90px_rgba(15,23,42,0.22)] sm:p-10">
          <span class="inline-flex rounded-full border border-white/12 bg-white/10 px-4 py-2 text-[11px] font-black uppercase tracking-[0.24em] text-white/80">Canal direto</span>
          <div class="mt-6 flex h-16 w-16 items-center justify-center rounded-[22px] bg-white/10 text-2xl text-white shadow-inner">
            <i class="fas fa-envelope-open-text"></i>
          </div>
          <h3 class="premium-font-display mt-6 text-3xl font-black tracking-tight">Quer falar conosco?</h3>
          <p class="mt-4 text-sm leading-8 text-slate-300">Contato direto, visual limpo e composição moderna para apresentação institucional.</p>
          <a :href="urls.contato" class="mt-8 inline-flex w-full items-center justify-center rounded-2xl bg-white px-6 py-4 text-sm font-black text-slate-950 shadow-[0_24px_60px_rgba(255,255,255,0.16)] transition hover:-translate-y-0.5 hover:bg-slate-100">
            Fale com o gestor
          </a>
        </aside>
      </div>
    </section>

    <section v-if="noticias.length > 0" class="relative px-4 pb-20 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-7xl">
        <div class="text-center">
          <span class="inline-flex items-center gap-3 text-[11px] font-black uppercase tracking-[0.30em] text-slate-500">
            <span class="h-px w-10 bg-slate-300"></span>
            Publicações oficiais
            <span class="h-px w-10 bg-slate-300"></span>
          </span>
          <h2 class="premium-font-display mt-5 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl lg:text-5xl">Últimas publicações</h2>
        </div>

        <div class="mt-10 grid gap-6 lg:grid-cols-3">
          <article v-for="(post, index) in noticias" :key="post.id ?? `${post.titulo}-${index}`" class="overflow-hidden rounded-[28px] border border-slate-200/80 bg-white shadow-[0_32px_80px_rgba(15,23,42,0.10)] transition duration-300 hover:-translate-y-1.5">
            <img :src="post.imagem" :alt="post.titulo" class="h-64 w-full object-cover" loading="lazy">
            <div class="p-6 sm:p-7">
              <span v-if="post.categoria" class="inline-flex rounded-full bg-slate-100 px-4 py-2 text-[11px] font-black uppercase tracking-[0.2em] text-slate-700">{{ post.categoria }}</span>
              <h3 class="premium-font-display mt-5 text-2xl font-black tracking-tight text-slate-950">{{ post.titulo }}</h3>
              <p class="mt-4 text-sm leading-7 text-slate-600">{{ post.resumo }}</p>
              <a :href="post.url" class="mt-6 inline-flex items-center text-sm font-black text-slate-950 transition hover:text-slate-700">
                Ler mais <i class="fas fa-arrow-right ms-2"></i>
              </a>
            </div>
          </article>
        </div>
      </div>
    </section>
  </div>
</template>
