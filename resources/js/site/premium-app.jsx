import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';

function parseProps(node) {
    try {
        return JSON.parse(node.dataset.props || '{}');
    } catch (error) {
        console.error('premium props parse error', error);

        return {};
    }
}

function cx(...classes) {
    return classes.filter(Boolean).join(' ');
}

function PremiumHeader({ siteName, siteLogo, siteSlogan, navItems = [], contactUrl }) {
    const [mobileOpen, setMobileOpen] = useState(false);

    useEffect(() => {
        const closeOnResize = () => {
            if (window.innerWidth >= 1024) {
                setMobileOpen(false);
            }
        };

        const closeOnEscape = (event) => {
            if (event.key === 'Escape') {
                setMobileOpen(false);
            }
        };

        window.addEventListener('resize', closeOnResize);
        document.addEventListener('keydown', closeOnEscape);

        return () => {
            window.removeEventListener('resize', closeOnResize);
            document.removeEventListener('keydown', closeOnEscape);
        };
    }, []);

    return (
        <header className="fixed inset-x-0 top-0 z-50 px-4 pt-4 sm:px-6">
            <div className="mx-auto max-w-7xl">
                <div className="rounded-[28px] border border-white/12 bg-slate-950/88 px-4 py-3 shadow-[0_24px_80px_rgba(15,23,42,0.34)] backdrop-blur-2xl sm:px-5 lg:px-6">
                    <div className="flex items-center justify-between gap-4">
                        <a href="/" className="flex min-w-0 items-center gap-3" aria-label={siteName}>
                            <span className="flex h-14 w-36 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white px-4 shadow-[0_16px_40px_rgba(15,23,42,0.16)] sm:h-16 sm:w-44">
                                <img
                                    src={siteLogo}
                                    alt={siteName}
                                    title={siteName}
                                    className="max-h-10 w-full object-contain sm:max-h-12"
                                />
                            </span>

                            <span className="hidden min-w-0 lg:flex lg:flex-col">
                                <strong className="truncate text-base font-black tracking-tight text-white">{siteName}</strong>
                                <small className="truncate text-[11px] font-semibold uppercase tracking-[0.20em] text-slate-300">
                                    {siteSlogan}
                                </small>
                            </span>
                        </a>

                        <div className="hidden items-center gap-3 lg:flex">
                            <nav aria-label="Navegação principal">
                                <ul className="flex items-center gap-1 rounded-[22px] border border-white/10 bg-white/6 p-2">
                                    {navItems.map((item) => (
                                        <li key={`${item.label}-${item.url}`}>
                                            <a
                                                className={cx(
                                                    'inline-flex items-center rounded-2xl px-4 py-3 text-sm font-semibold text-slate-200 transition duration-200 hover:bg-white/10 hover:text-white',
                                                    item.active && 'bg-white text-slate-950 shadow-[0_14px_30px_rgba(255,255,255,0.12)]',
                                                )}
                                                href={item.url}
                                                target={item.target || '_self'}
                                            >
                                                {item.label}
                                            </a>
                                        </li>
                                    ))}
                                </ul>
                            </nav>

                            <a
                                href={contactUrl}
                                className="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-black text-slate-950 shadow-[0_18px_40px_rgba(255,255,255,0.16)] transition hover:-translate-y-0.5 hover:bg-slate-100"
                            >
                                <i className="fas fa-user-check me-2"></i>
                                Quero participar
                            </a>
                        </div>

                        <button
                            type="button"
                            className="inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-white/8 text-lg text-white lg:hidden"
                            aria-label={mobileOpen ? 'Fechar menu' : 'Abrir menu'}
                            aria-expanded={mobileOpen}
                            onClick={() => setMobileOpen((value) => !value)}
                        >
                            <i className={mobileOpen ? 'fas fa-times' : 'fas fa-bars'}></i>
                        </button>
                    </div>

                    {mobileOpen && (
                        <div className="pt-4 lg:hidden">
                            <div className="rounded-[24px] border border-white/10 bg-white/6 p-3">
                                <div className="mb-4 flex items-center gap-3 rounded-2xl bg-white/6 p-3">
                                    <span className="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/12 text-white">
                                        <i className="fas fa-building"></i>
                                    </span>
                                    <div className="min-w-0">
                                        <strong className="block truncate text-sm font-black text-white">{siteName}</strong>
                                        <small className="block truncate text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-300">
                                            {siteSlogan}
                                        </small>
                                    </div>
                                </div>

                                <nav aria-label="Navegação principal mobile">
                                    <ul className="space-y-2">
                                        {navItems.map((item) => (
                                            <li key={`mobile-${item.label}-${item.url}`}>
                                                <a
                                                    className={cx(
                                                        'flex items-center justify-between rounded-2xl px-4 py-3 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white',
                                                        item.active && 'bg-white text-slate-950',
                                                    )}
                                                    href={item.url}
                                                    target={item.target || '_self'}
                                                    onClick={() => setMobileOpen(false)}
                                                >
                                                    <span>{item.label}</span>
                                                    <i className="fas fa-arrow-right text-xs"></i>
                                                </a>
                                            </li>
                                        ))}
                                    </ul>
                                </nav>

                                <a
                                    href={contactUrl}
                                    className="mt-4 inline-flex w-full items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-black text-slate-950"
                                    onClick={() => setMobileOpen(false)}
                                >
                                    <i className="fas fa-user-check me-2"></i>
                                    Quero participar
                                </a>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </header>
    );
}

function PremiumFooter({ siteName, siteLogo, siteSlogan, contactEmail, contactPhone, contactAddress, contactWhatsapp, urls, social }) {
    return (
        <footer className="relative px-4 pb-8 pt-16 sm:px-6 lg:px-8">
            <div className="mx-auto max-w-7xl overflow-hidden rounded-[32px] border border-slate-200/70 bg-white shadow-[0_36px_90px_rgba(15,23,42,0.10)]">
                <div className="bg-[radial-gradient(circle_at_top_right,_rgba(15,23,42,0.05),_transparent_34%),linear-gradient(135deg,_rgba(255,255,255,0.98),_rgba(248,250,252,0.96))] px-6 py-10 sm:px-8 lg:px-10">
                    <div className="grid gap-10 lg:grid-cols-[1.15fr_0.8fr_0.95fr_0.85fr]">
                        <div className="space-y-5">
                            <div className="inline-flex h-20 w-48 items-center justify-center rounded-[24px] bg-slate-950 px-5 shadow-[0_24px_70px_rgba(15,23,42,0.18)]">
                                <img src={siteLogo} alt={siteName} title={siteName} loading="lazy" className="max-h-12 w-full object-contain" />
                            </div>

                            <div>
                                <h3 className="premium-font-display text-2xl font-black tracking-tight text-slate-950">{siteName}</h3>
                                <p className="mt-3 max-w-md text-sm leading-7 text-slate-600">{siteSlogan}</p>
                            </div>
                        </div>

                        <div>
                            <h4 className="text-sm font-black uppercase tracking-[0.22em] text-slate-500">Institucional</h4>
                            <ul className="mt-5 space-y-3 text-sm font-medium text-slate-700">
                                <li><a className="transition hover:text-slate-950" href={urls.home}>Início</a></li>
                                <li><a className="transition hover:text-slate-950" href={urls.biografia}>Biografia</a></li>
                                <li><a className="transition hover:text-slate-950" href={urls.propostas}>Propostas</a></li>
                                <li><a className="transition hover:text-slate-950" href={urls.transparencia}>Transparência</a></li>
                            </ul>
                        </div>

                        <div>
                            <h4 className="text-sm font-black uppercase tracking-[0.22em] text-slate-500">Contato</h4>
                            <ul className="mt-5 space-y-4 text-sm leading-6 text-slate-600">
                                {contactEmail && <li className="flex gap-3"><i className="fas fa-envelope mt-1 text-slate-400"></i><span>{contactEmail}</span></li>}
                                {contactPhone && <li className="flex gap-3"><i className="fas fa-phone mt-1 text-slate-400"></i><span>{contactPhone}</span></li>}
                                {contactAddress && <li className="flex gap-3"><i className="fas fa-map-marker-alt mt-1 text-slate-400"></i><span>{contactAddress}</span></li>}
                            </ul>
                        </div>

                        <div>
                            <h4 className="text-sm font-black uppercase tracking-[0.22em] text-slate-500">Canais</h4>
                            <p className="mt-5 text-sm leading-7 text-slate-600">Acompanhe os canais oficiais e receba atualizações.</p>

                            <div className="mt-5 flex flex-wrap gap-3">
                                {social.facebook && <a href={social.facebook} target="_blank" rel="noopener" aria-label="Facebook" className="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-950 hover:text-white"><i className="fab fa-facebook-f"></i></a>}
                                {social.instagram && <a href={social.instagram} target="_blank" rel="noopener" aria-label="Instagram" className="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-950 hover:text-white"><i className="fab fa-instagram"></i></a>}
                                {social.youtube && <a href={social.youtube} target="_blank" rel="noopener" aria-label="YouTube" className="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-950 hover:text-white"><i className="fab fa-youtube"></i></a>}
                                {contactWhatsapp && <a href={`https://wa.me/${contactWhatsapp}`} target="_blank" rel="noopener" aria-label="WhatsApp" className="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-950 hover:text-white"><i className="fab fa-whatsapp"></i></a>}
                            </div>
                        </div>
                    </div>
                </div>

                <div className="border-t border-slate-200/80 bg-slate-50 px-6 py-5 text-center text-sm text-slate-500 sm:px-8 lg:px-10">
                    &copy; {new Date().getFullYear()} {siteName} - Todos os direitos reservados
                </div>
            </div>
        </footer>
    );
}

function PremiumHome(props) {
    const {
        politicianName,
        politicianPhoto,
        politicianRole,
        slogan,
        stats,
        propostas,
        noticias,
        firstEvent,
        urls,
    } = props;

    return (
        <div className="relative overflow-hidden pt-28 sm:pt-32">
            <div className="pointer-events-none absolute inset-x-0 top-0 h-[40rem] bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.16),_transparent_28%),linear-gradient(135deg,_color-mix(in_srgb,var(--premium-primary)_90%,#020617)_0%,_#0f172a_52%,_color-mix(in_srgb,var(--premium-secondary)_42%,#020617)_100%)]"></div>
            <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_82%_18%,_rgba(255,255,255,0.12),_transparent_18%),radial-gradient(circle_at_12%_78%,_color-mix(in_srgb,var(--premium-secondary)_14%,transparent),_transparent_24%)]"></div>

            <section className="relative px-4 pb-14 sm:px-6 lg:px-8">
                <div className="mx-auto grid max-w-7xl items-center gap-8 lg:grid-cols-[minmax(0,1.05fr)_minmax(320px,0.95fr)] lg:gap-10">
                    <div className="text-white">
                        <div className="inline-flex flex-wrap gap-2">
                            {['Excelência', 'Resultados', 'Transparência'].map((tag) => (
                                <span key={tag} className="rounded-full border border-white/14 bg-white/8 px-4 py-2 text-[11px] font-black uppercase tracking-[0.24em] text-white/90">
                                    {tag}
                                </span>
                            ))}
                        </div>

                        <div className="mt-7">
                            <span className="inline-flex items-center gap-3 text-[11px] font-black uppercase tracking-[0.28em] text-white/70">
                                <span className="h-px w-10 bg-white/35"></span>
                                Gestão pública premium
                            </span>

                            <h1 className="premium-font-display mt-5 max-w-4xl text-4xl font-black leading-[0.98] tracking-tight sm:text-5xl xl:text-6xl">
                                Presença pública forte,
                                <span className="mt-2 block bg-[linear-gradient(135deg,#ffffff_0%,#cbd5e1_38%,#93c5fd_74%,#bfdbfe_100%)] bg-clip-text text-transparent">
                                    layout premium de verdade
                                </span>
                            </h1>

                            <p className="mt-6 max-w-2xl text-base leading-8 text-slate-200 sm:text-lg">{slogan}</p>
                        </div>

                        <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href={urls.propostas} className="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-4 text-sm font-black text-slate-950 shadow-[0_24px_60px_rgba(255,255,255,0.16)] transition hover:-translate-y-0.5 hover:bg-slate-100">
                                <i className="fas fa-chalkboard-user me-2"></i>
                                Conheça as propostas
                            </a>
                            <a href={urls.biografia} className="inline-flex items-center justify-center rounded-2xl border border-white/18 bg-white/8 px-6 py-4 text-sm font-black text-white backdrop-blur-xl transition hover:-translate-y-0.5 hover:bg-white/12">
                                <i className="fas fa-user-tie me-2"></i>
                                Ver trajetória
                            </a>
                        </div>

                        <div className="mt-10 grid gap-4 sm:grid-cols-3">
                            <div className="rounded-[26px] border border-white/12 bg-white/8 p-5 backdrop-blur-xl">
                                <div className="text-3xl font-black tracking-tight">{stats.projetos}</div>
                                <div className="mt-2 text-sm text-slate-200">Projetos concluídos</div>
                            </div>
                            <div className="rounded-[26px] border border-white/12 bg-white/8 p-5 backdrop-blur-xl">
                                <div className="text-3xl font-black tracking-tight">{stats.obras}</div>
                                <div className="mt-2 text-sm text-slate-200">Cidadãos atendidos</div>
                            </div>
                            <div className="rounded-[26px] border border-white/12 bg-white/8 p-5 backdrop-blur-xl">
                                <div className="text-3xl font-black tracking-tight">{stats.anos}</div>
                                <div className="mt-2 text-sm text-slate-200">Índice de satisfação</div>
                            </div>
                        </div>
                    </div>

                    <div className="relative">
                        <div className="overflow-hidden rounded-[32px] border border-white/12 bg-white/8 p-3 shadow-[0_40px_110px_rgba(15,23,42,0.34)] backdrop-blur-xl">
                            <div className="absolute left-6 top-6 z-10 inline-flex items-center gap-2 rounded-full bg-slate-950/74 px-4 py-2 text-[11px] font-black uppercase tracking-[0.18em] text-white backdrop-blur-xl">
                                <i className="fas fa-award"></i>
                                Destaque institucional
                            </div>

                            <div className="overflow-hidden rounded-[26px] bg-slate-200">
                                <img src={politicianPhoto} alt={politicianName} className="h-[28rem] w-full object-cover sm:h-[34rem]" />
                            </div>

                            <div className="absolute inset-x-6 bottom-6 rounded-[24px] bg-[linear-gradient(180deg,rgba(15,23,42,0.20),rgba(15,23,42,0.86))] p-5 text-white backdrop-blur-xl">
                                <div className="text-[11px] font-black uppercase tracking-[0.20em] text-slate-300">{politicianRole}</div>
                                <h2 className="premium-font-display mt-3 text-2xl font-black tracking-tight">{politicianName}</h2>
                                <p className="mt-3 text-sm leading-7 text-slate-200">Comunicação clara, presença institucional e leitura visual consistente em todas as telas.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section className="relative px-4 py-16 sm:px-6 lg:px-8">
                <div className="mx-auto max-w-7xl rounded-[32px] border border-slate-200/70 bg-white/94 p-8 shadow-[0_36px_90px_rgba(15,23,42,0.10)] sm:p-10 lg:p-12">
                    <div className="mx-auto max-w-3xl text-center">
                        <span className="inline-flex items-center gap-3 text-[11px] font-black uppercase tracking-[0.30em] text-slate-500">
                            <span className="h-px w-10 bg-slate-300"></span>
                            Diretrizes da atuação
                            <span className="h-px w-10 bg-slate-300"></span>
                        </span>
                        <h2 className="premium-font-display mt-5 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl lg:text-5xl">Pilares da gestão</h2>
                        <p className="mt-5 text-base leading-8 text-slate-600">Quatro compromissos que sustentam uma comunicação pública mais forte, organizada e confiável.</p>
                    </div>

                    <div className="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                        {propostas.map((proposta, index) => (
                            <article key={proposta.id ?? `${proposta.titulo}-${index}`} className="group relative overflow-hidden rounded-[28px] border border-slate-200 bg-slate-50/80 p-6 transition duration-300 hover:-translate-y-1.5 hover:bg-white hover:shadow-[0_30px_80px_rgba(15,23,42,0.12)]">
                                <span className="absolute right-5 top-5 text-4xl font-black tracking-tight text-slate-200">{String(index + 1).padStart(2, '0')}</span>
                                <div className="relative flex h-16 w-16 items-center justify-center rounded-[22px] bg-[linear-gradient(135deg,color-mix(in_srgb,var(--premium-primary)_12%,#ffffff),color-mix(in_srgb,var(--premium-secondary)_10%,#ffffff))] text-slate-900 shadow-inner">
                                    <i className={cx(proposta.icone || 'fas fa-chart-line', 'text-xl')}></i>
                                </div>
                                <h3 className="premium-font-display mt-6 text-2xl font-black tracking-tight text-slate-950">{proposta.titulo}</h3>
                                <p className="mt-4 text-sm leading-7 text-slate-600">{proposta.resumo}</p>
                            </article>
                        ))}
                    </div>
                </div>
            </section>

            <section className="relative px-4 pb-16 sm:px-6 lg:px-8">
                <div className="mx-auto grid max-w-7xl gap-6 xl:grid-cols-[minmax(0,1.05fr)_400px]">
                    <div className="rounded-[32px] border border-slate-200/70 bg-white/94 p-8 shadow-[0_36px_90px_rgba(15,23,42,0.10)] sm:p-10">
                        <span className="inline-flex items-center gap-3 text-[11px] font-black uppercase tracking-[0.30em] text-slate-500">
                            <span className="h-px w-10 bg-slate-300"></span>
                            Participação e presença
                        </span>
                        <h2 className="premium-font-display mt-5 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl lg:text-5xl">Próximos eventos</h2>
                        <p className="mt-5 max-w-2xl text-base leading-8 text-slate-600">Acompanhe a agenda pública, os encontros institucionais e os compromissos abertos à população.</p>

                        <div className="mt-8 rounded-[28px] border border-slate-200 bg-slate-50 p-5 shadow-inner sm:p-6">
                            {firstEvent ? (
                                <div className="grid gap-5 sm:grid-cols-[104px_minmax(0,1fr)] sm:items-start">
                                    <div className="rounded-[24px] bg-slate-950 px-4 py-5 text-center text-white shadow-[0_24px_70px_rgba(15,23,42,0.24)]">
                                        <strong className="block text-4xl font-black tracking-tight">{firstEvent.day}</strong>
                                        <span className="mt-2 block text-xs font-black uppercase tracking-[0.24em] text-slate-300">{firstEvent.month}</span>
                                    </div>
                                    <div className="min-w-0">
                                        <h3 className="premium-font-display text-2xl font-black tracking-tight text-slate-950">{firstEvent.titulo}</h3>
                                        <p className="mt-3 text-sm leading-7 text-slate-600">{firstEvent.local || 'Evento público com participação da população.'}</p>
                                        <div className="mt-5 flex flex-wrap gap-3 text-sm font-medium text-slate-500">
                                            <span className="inline-flex items-center rounded-full bg-white px-4 py-2 shadow-sm"><i className="far fa-clock me-2"></i>{firstEvent.time}</span>
                                            {firstEvent.local && <span className="inline-flex items-center rounded-full bg-white px-4 py-2 shadow-sm"><i className="fas fa-location-dot me-2"></i>{firstEvent.local}</span>}
                                        </div>
                                    </div>
                                </div>
                            ) : (
                                <div className="rounded-[24px] bg-white p-8 text-center">
                                    <h3 className="premium-font-display text-2xl font-black tracking-tight text-slate-950">Nenhum evento agendado</h3>
                                    <p className="mt-3 text-sm leading-7 text-slate-600">A agenda pública será atualizada em breve.</p>
                                </div>
                            )}
                        </div>
                    </div>

                    <aside className="rounded-[32px] border border-slate-200/70 bg-slate-950 p-8 text-white shadow-[0_36px_90px_rgba(15,23,42,0.22)] sm:p-10">
                        <span className="inline-flex rounded-full border border-white/12 bg-white/10 px-4 py-2 text-[11px] font-black uppercase tracking-[0.24em] text-white/80">Canal direto</span>
                        <div className="mt-6 flex h-16 w-16 items-center justify-center rounded-[22px] bg-white/10 text-2xl text-white shadow-inner">
                            <i className="fas fa-envelope-open-text"></i>
                        </div>
                        <h3 className="premium-font-display mt-6 text-3xl font-black tracking-tight">Quer falar conosco?</h3>
                        <p className="mt-4 text-sm leading-8 text-slate-300">Sua opinião é fundamental para construirmos uma cidade melhor com diálogo, clareza e retorno rápido.</p>
                        <a href={urls.contato} className="mt-8 inline-flex w-full items-center justify-center rounded-2xl bg-white px-6 py-4 text-sm font-black text-slate-950 shadow-[0_24px_60px_rgba(255,255,255,0.16)] transition hover:-translate-y-0.5 hover:bg-slate-100">
                            Fale com o gestor
                        </a>
                    </aside>
                </div>
            </section>

            {noticias.length > 0 && (
                <section className="relative px-4 pb-20 sm:px-6 lg:px-8">
                    <div className="mx-auto max-w-7xl">
                        <div className="text-center">
                            <span className="inline-flex items-center gap-3 text-[11px] font-black uppercase tracking-[0.30em] text-slate-500">
                                <span className="h-px w-10 bg-slate-300"></span>
                                Atualizações oficiais
                                <span className="h-px w-10 bg-slate-300"></span>
                            </span>
                            <h2 className="premium-font-display mt-5 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl lg:text-5xl">Últimas publicações</h2>
                        </div>

                        <div className="mt-10 grid gap-6 lg:grid-cols-3">
                            {noticias.map((post, index) => (
                                <article key={post.id ?? `${post.titulo}-${index}`} className="overflow-hidden rounded-[28px] border border-slate-200/80 bg-white shadow-[0_32px_80px_rgba(15,23,42,0.10)] transition duration-300 hover:-translate-y-1.5">
                                    <img src={post.imagem} alt={post.titulo} className="h-64 w-full object-cover" loading="lazy" />
                                    <div className="p-6 sm:p-7">
                                        {post.categoria && <span className="inline-flex rounded-full bg-slate-100 px-4 py-2 text-[11px] font-black uppercase tracking-[0.2em] text-slate-700">{post.categoria}</span>}
                                        <h3 className="premium-font-display mt-5 text-2xl font-black tracking-tight text-slate-950">{post.titulo}</h3>
                                        <p className="mt-4 text-sm leading-7 text-slate-600">{post.resumo}</p>
                                        <a href={post.url} className="mt-6 inline-flex items-center text-sm font-black text-slate-950 transition hover:text-slate-700">
                                            Ler mais <i className="fas fa-arrow-right ms-2"></i>
                                        </a>
                                    </div>
                                </article>
                            ))}
                        </div>
                    </div>
                </section>
            )}
        </div>
    );
}

function mountComponent(selector, Component) {
    document.querySelectorAll(selector).forEach((node) => {
        createRoot(node).render(<Component {...parseProps(node)} />);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    mountComponent('[data-premium-component="header"]', PremiumHeader);
    mountComponent('[data-premium-component="footer"]', PremiumFooter);
    mountComponent('[data-premium-component="home"]', PremiumHome);
});
