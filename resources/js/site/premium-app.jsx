import React from 'react';
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

function PremiumHeader({ siteName, siteLogo, siteSlogan, navItems, contactUrl }) {
    return (
        <header className="fixed inset-x-0 top-0 z-50 px-3 pt-3 sm:px-5">
            <nav className="mx-auto max-w-7xl rounded-[28px] border border-white/15 bg-slate-950/70 px-4 py-3 shadow-[0_24px_70px_rgba(15,23,42,0.28)] backdrop-blur-2xl sm:px-6">
                <div className="flex items-center justify-between gap-3">
                    <a href="/" className="flex min-w-0 items-center gap-3" aria-label={siteName}>
                        <span className="flex h-14 w-28 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white px-3 shadow-lg shadow-slate-950/20 sm:h-16 sm:w-36">
                            <img src={siteLogo} alt={siteName} title={siteName} className="max-h-10 w-full object-contain sm:max-h-12" />
                        </span>
                        <span className="hidden min-w-0 xl:flex xl:flex-col">
                            <strong className="truncate text-sm font-extrabold tracking-tight text-white">{siteName}</strong>
                            <small className="truncate text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-300">{siteSlogan}</small>
                        </span>
                    </a>

                    <button className="navbar-toggler inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-white/10 text-white lg:hidden" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Abrir menu">
                        <span className="navbar-toggler-icon"></span>
                    </button>

                    <div className="collapse navbar-collapse lg:!visible lg:!static lg:!block lg:!translate-x-0 lg:!opacity-100 lg:!w-auto lg:!bg-transparent lg:!shadow-none lg:!p-0" id="navbarMain">
                        <div className="navbar-mobile-head d-lg-none">
                            <a className="navbar-mobile-brand d-flex align-items-center" href="/" aria-label={siteName}>
                                <img src={siteLogo} alt={siteName} title={siteName} loading="eager" width="220" height="64" />
                            </a>
                            <button className="navbar-mobile-close" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Fechar menu">
                                <i className="fas fa-times"></i>
                            </button>
                        </div>

                        <div className="flex flex-col gap-6 lg:flex-row lg:items-center lg:gap-4">
                            <ul className="navbar-nav mx-0 flex flex-col gap-2 rounded-[26px] border border-white/10 bg-white/8 p-3 lg:flex-row lg:items-center lg:gap-1 lg:border-white/10 lg:bg-white/5">
                                {navItems.map((item) => (
                                    <li className="nav-item" key={`${item.label}-${item.url}`}>
                                        <a
                                            className={cx(
                                                'nav-link inline-flex items-center rounded-2xl px-4 py-3 text-sm font-semibold text-slate-200 transition duration-200 hover:bg-white/12 hover:text-white lg:px-5 lg:py-3',
                                                item.active && 'active bg-white text-slate-950 shadow-xl shadow-slate-950/10',
                                            )}
                                            href={item.url}
                                            target={item.target || '_self'}
                                        >
                                            {item.label}
                                        </a>
                                    </li>
                                ))}
                            </ul>

                            <div className="flex items-center gap-3 lg:pl-2">
                                <a href={contactUrl} className="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-bold text-slate-950 shadow-[0_20px_50px_rgba(255,255,255,0.18)] transition hover:-translate-y-0.5 hover:bg-slate-100">
                                    <i className="fas fa-user-check me-2"></i>
                                    Quero participar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </header>
    );
}

function PremiumFooter({ siteName, siteLogo, siteSlogan, contactEmail, contactPhone, contactAddress, contactWhatsapp, urls, social }) {
    return (
        <footer className="relative overflow-hidden px-4 pb-6 pt-14 sm:px-6 lg:px-8">
            <div className="mx-auto max-w-7xl overflow-hidden rounded-[32px] border border-slate-200/70 bg-white shadow-[0_36px_90px_rgba(15,23,42,0.12)]">
                <div className="relative bg-[radial-gradient(circle_at_top_right,_rgba(15,23,42,0.06),_transparent_34%),linear-gradient(135deg,_rgba(255,255,255,0.96),_rgba(241,245,249,0.96))] px-6 py-10 sm:px-10 lg:px-12">
                    <div className="grid gap-10 lg:grid-cols-[1.2fr_0.8fr_0.9fr_0.9fr]">
                        <div className="space-y-5">
                            <div className="inline-flex h-20 w-44 items-center justify-center rounded-[24px] bg-slate-950 px-5 shadow-[0_24px_70px_rgba(15,23,42,0.22)]">
                                <img src={siteLogo} alt={siteName} title={siteName} loading="lazy" className="max-h-12 w-full object-contain" />
                            </div>
                            <div>
                                <h3 className="premium-font-display text-2xl font-black tracking-tight text-slate-950">{siteName}</h3>
                                <p className="mt-3 max-w-sm text-sm leading-7 text-slate-600">{siteSlogan}</p>
                            </div>
                        </div>

                        <div>
                            <h4 className="text-sm font-black uppercase tracking-[0.24em] text-slate-500">Institucional</h4>
                            <ul className="mt-5 space-y-3 text-sm font-medium text-slate-700">
                                <li><a className="transition hover:text-slate-950" href={urls.home}>Início</a></li>
                                <li><a className="transition hover:text-slate-950" href={urls.biografia}>Biografia</a></li>
                                <li><a className="transition hover:text-slate-950" href={urls.propostas}>Propostas</a></li>
                                <li><a className="transition hover:text-slate-950" href={urls.transparencia}>Transparência</a></li>
                            </ul>
                        </div>

                        <div>
                            <h4 className="text-sm font-black uppercase tracking-[0.24em] text-slate-500">Contato</h4>
                            <ul className="mt-5 space-y-4 text-sm leading-6 text-slate-600">
                                {contactEmail && <li className="flex gap-3"><i className="fas fa-envelope mt-1 text-slate-400"></i><span>{contactEmail}</span></li>}
                                {contactPhone && <li className="flex gap-3"><i className="fas fa-phone mt-1 text-slate-400"></i><span>{contactPhone}</span></li>}
                                {contactAddress && <li className="flex gap-3"><i className="fas fa-map-marker-alt mt-1 text-slate-400"></i><span>{contactAddress}</span></li>}
                            </ul>
                        </div>

                        <div>
                            <h4 className="text-sm font-black uppercase tracking-[0.24em] text-slate-500">Conexões</h4>
                            <p className="mt-5 text-sm leading-7 text-slate-600">Acompanhe os canais oficiais e receba atualizações em tempo real.</p>
                            <div className="mt-5 flex flex-wrap gap-3">
                                {social.facebook && <a href={social.facebook} target="_blank" rel="noopener" aria-label="Facebook" className="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-950 hover:text-white"><i className="fab fa-facebook-f"></i></a>}
                                {social.instagram && <a href={social.instagram} target="_blank" rel="noopener" aria-label="Instagram" className="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-950 hover:text-white"><i className="fab fa-instagram"></i></a>}
                                {social.youtube && <a href={social.youtube} target="_blank" rel="noopener" aria-label="YouTube" className="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-950 hover:text-white"><i className="fab fa-youtube"></i></a>}
                                {contactWhatsapp && <a href={`https://wa.me/${contactWhatsapp}`} target="_blank" rel="noopener" aria-label="WhatsApp" className="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-950 hover:text-white"><i className="fab fa-whatsapp"></i></a>}
                            </div>
                        </div>
                    </div>
                </div>

                <div className="border-t border-slate-200/80 bg-slate-50 px-6 py-5 text-center text-sm text-slate-500 sm:px-10 lg:px-12">
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
            <div className="pointer-events-none absolute inset-x-0 top-0 h-[46rem] bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.18),_transparent_28%),linear-gradient(135deg,_color-mix(in_srgb,var(--premium-primary)_90%,#020617)_0%,_#0f172a_48%,_color-mix(in_srgb,var(--premium-secondary)_48%,#020617)_100%)]"></div>
            <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_85%_18%,_rgba(255,255,255,0.14),_transparent_18%),radial-gradient(circle_at_12%_78%,_color-mix(in_srgb,var(--premium-secondary)_18%,transparent),_transparent_24%)]"></div>

            <section className="relative px-4 pb-16 sm:px-6 lg:px-8">
                <div className="mx-auto grid max-w-7xl items-center gap-10 lg:grid-cols-[minmax(0,1.08fr)_minmax(360px,0.92fr)]">
                    <div className="text-white">
                        <div className="inline-flex flex-wrap gap-3">
                            {['Excelência', 'Resultados', 'Transparência'].map((tag) => (
                                <span key={tag} className="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-extrabold uppercase tracking-[0.24em] text-white/90">{tag}</span>
                            ))}
                        </div>

                        <div className="mt-8">
                            <span className="inline-flex items-center gap-3 text-xs font-black uppercase tracking-[0.32em] text-white/70">
                                <span className="h-px w-10 bg-white/40"></span>
                                Gestão pública premium
                            </span>
                            <h1 className="premium-font-display mt-5 max-w-4xl text-5xl font-black leading-[0.94] tracking-tight sm:text-6xl xl:text-7xl">
                                Um tema público
                                <span className="block bg-[linear-gradient(135deg,#ffffff_0%,#cbd5e1_42%,#93c5fd_74%,#bfdbfe_100%)] bg-clip-text text-transparent">realmente premium</span>
                            </h1>
                            <p className="mt-6 max-w-2xl text-base leading-8 text-slate-200 sm:text-lg">{slogan}</p>
                        </div>

                        <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href={urls.propostas} className="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-4 text-sm font-black text-slate-950 shadow-[0_24px_60px_rgba(255,255,255,0.18)] transition hover:-translate-y-0.5 hover:bg-slate-100">
                                <i className="fas fa-chalkboard-user me-2"></i>Conheça as propostas
                            </a>
                            <a href={urls.biografia} className="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-white/8 px-6 py-4 text-sm font-black text-white backdrop-blur-xl transition hover:-translate-y-0.5 hover:bg-white/12">
                                <i className="fas fa-user-tie me-2"></i>Ver trajetória
                            </a>
                        </div>

                        <div className="mt-10 grid gap-4 sm:grid-cols-3">
                            <div className="rounded-[28px] border border-white/12 bg-white/10 p-5 backdrop-blur-xl">
                                <div className="text-3xl font-black tracking-tight">{stats.projetos}</div>
                                <div className="mt-2 text-sm text-slate-200">Projetos concluídos</div>
                            </div>
                            <div className="rounded-[28px] border border-white/12 bg-white/10 p-5 backdrop-blur-xl">
                                <div className="text-3xl font-black tracking-tight">{stats.obras}</div>
                                <div className="mt-2 text-sm text-slate-200">Cidadãos atendidos</div>
                            </div>
                            <div className="rounded-[28px] border border-white/12 bg-white/10 p-5 backdrop-blur-xl">
                                <div className="text-3xl font-black tracking-tight">{stats.anos}</div>
                                <div className="mt-2 text-sm text-slate-200">Índice de satisfação</div>
                            </div>
                        </div>
                    </div>

                    <div className="relative lg:pl-6">
                        <div className="relative overflow-hidden rounded-[36px] border border-white/12 bg-white/10 p-3 shadow-[0_40px_120px_rgba(15,23,42,0.34)] backdrop-blur-xl">
                            <div className="absolute left-5 top-5 z-10 inline-flex items-center gap-2 rounded-full bg-slate-950/72 px-4 py-2 text-xs font-black uppercase tracking-[0.2em] text-white backdrop-blur-xl">
                                <i className="fas fa-award"></i>
                                Destaque institucional
                            </div>
                            <div className="overflow-hidden rounded-[28px] bg-slate-200">
                                <img src={politicianPhoto} alt={politicianName} className="h-[30rem] w-full object-cover sm:h-[38rem]" />
                            </div>
                            <div className="absolute inset-x-5 bottom-5 rounded-[28px] bg-[linear-gradient(180deg,rgba(15,23,42,0.22),rgba(15,23,42,0.86))] p-5 text-white backdrop-blur-xl">
                                <div className="text-[11px] font-black uppercase tracking-[0.22em] text-slate-300">{politicianRole}</div>
                                <div className="mt-2 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                                    <div>
                                        <h2 className="premium-font-display text-2xl font-black tracking-tight">{politicianName}</h2>
                                        <p className="mt-2 text-sm leading-7 text-slate-200">Presença pública, comunicação clara e posicionamento institucional forte.</p>
                                    </div>
                                    <span className="inline-flex items-center rounded-full border border-white/12 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-white/90">Atuação ativa</span>
                                </div>
                            </div>
                        </div>

                        <div className="mt-5 grid gap-4 sm:grid-cols-2">
                            <div className="rounded-[28px] border border-slate-200/70 bg-white/92 p-5 shadow-[0_24px_70px_rgba(15,23,42,0.12)]">
                                <div className="text-sm font-black uppercase tracking-[0.24em] text-slate-500">Agenda aberta</div>
                                <p className="mt-3 text-sm leading-7 text-slate-600">Compromissos públicos, encontros institucionais e participação social com leitura rápida.</p>
                            </div>
                            <div className="rounded-[28px] border border-slate-200/70 bg-white/92 p-5 shadow-[0_24px_70px_rgba(15,23,42,0.12)]">
                                <div className="text-sm font-black uppercase tracking-[0.24em] text-slate-500">Comunicação clara</div>
                                <p className="mt-3 text-sm leading-7 text-slate-600">Visual refinado, hierarquia forte e dados do site preservados sem trocar o conteúdo.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section className="relative px-4 py-16 sm:px-6 lg:px-8">
                <div className="mx-auto max-w-7xl rounded-[36px] border border-slate-200/70 bg-white/92 p-8 shadow-[0_40px_100px_rgba(15,23,42,0.10)] backdrop-blur-xl sm:p-10 lg:p-12">
                    <div className="mx-auto max-w-3xl text-center">
                        <span className="inline-flex items-center gap-3 text-xs font-black uppercase tracking-[0.32em] text-slate-500">
                            <span className="h-px w-10 bg-slate-300"></span>
                            Diretrizes da atuação
                            <span className="h-px w-10 bg-slate-300"></span>
                        </span>
                        <h2 className="premium-font-display mt-5 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">Pilares da gestão</h2>
                        <p className="mt-5 text-base leading-8 text-slate-600">Quatro compromissos que sustentam uma comunicação pública mais forte, organizada e confiável.</p>
                    </div>

                    <div className="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                        {propostas.map((proposta, index) => (
                            <article key={proposta.id ?? `${proposta.titulo}-${index}`} className="group relative overflow-hidden rounded-[32px] border border-slate-200 bg-slate-50/70 p-6 transition duration-300 hover:-translate-y-1.5 hover:bg-white hover:shadow-[0_30px_80px_rgba(15,23,42,0.12)]">
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
                <div className="mx-auto grid max-w-7xl gap-6 xl:grid-cols-[minmax(0,1.05fr)_420px]">
                    <div className="rounded-[36px] border border-slate-200/70 bg-white/94 p-8 shadow-[0_40px_100px_rgba(15,23,42,0.10)] sm:p-10">
                        <span className="inline-flex items-center gap-3 text-xs font-black uppercase tracking-[0.32em] text-slate-500">
                            <span className="h-px w-10 bg-slate-300"></span>
                            Participação e presença
                        </span>
                        <h2 className="premium-font-display mt-5 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">Próximos eventos</h2>
                        <p className="mt-5 max-w-2xl text-base leading-8 text-slate-600">Acompanhe a agenda pública, os encontros institucionais e os compromissos abertos à população.</p>

                        <div className="mt-8 rounded-[32px] border border-slate-200 bg-slate-50 p-5 shadow-inner sm:p-6">
                            {firstEvent ? (
                                <div className="grid gap-5 sm:grid-cols-[104px_minmax(0,1fr)] sm:items-start">
                                    <div className="rounded-[28px] bg-slate-950 px-4 py-5 text-center text-white shadow-[0_24px_70px_rgba(15,23,42,0.24)]">
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
                                <div className="rounded-[28px] bg-white p-8 text-center">
                                    <h3 className="premium-font-display text-2xl font-black tracking-tight text-slate-950">Nenhum evento agendado</h3>
                                    <p className="mt-3 text-sm leading-7 text-slate-600">A agenda pública será atualizada em breve.</p>
                                </div>
                            )}
                        </div>
                    </div>

                    <aside className="rounded-[36px] border border-slate-200/70 bg-slate-950 p-8 text-white shadow-[0_40px_100px_rgba(15,23,42,0.24)] sm:p-10">
                        <span className="inline-flex rounded-full border border-white/12 bg-white/10 px-4 py-2 text-[11px] font-black uppercase tracking-[0.24em] text-white/80">Canal direto</span>
                        <div className="mt-6 flex h-16 w-16 items-center justify-center rounded-[24px] bg-white/10 text-2xl text-white shadow-inner">
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
                            <span className="inline-flex items-center gap-3 text-xs font-black uppercase tracking-[0.32em] text-slate-500">
                                <span className="h-px w-10 bg-slate-300"></span>
                                Atualizações oficiais
                                <span className="h-px w-10 bg-slate-300"></span>
                            </span>
                            <h2 className="premium-font-display mt-5 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">Últimas publicações</h2>
                        </div>

                        <div className="mt-10 grid gap-6 lg:grid-cols-3">
                            {noticias.map((post, index) => (
                                <article key={post.id ?? `${post.titulo}-${index}`} className="overflow-hidden rounded-[32px] border border-slate-200/80 bg-white shadow-[0_32px_80px_rgba(15,23,42,0.10)] transition duration-300 hover:-translate-y-1.5">
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
