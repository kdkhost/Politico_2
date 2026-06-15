import { createApp } from 'vue';
import PremiumHeader from './premium/components/PremiumHeader.vue';
import PremiumFooter from './premium/components/PremiumFooter.vue';
import PremiumHome from './premium/components/PremiumHome.vue';

function parseProps(node) {
    try {
        return JSON.parse(node.dataset.props || '{}');
    } catch (error) {
        console.error('premium props parse error', error);

        return {};
    }
}

function mountComponent(selector, component) {
    document.querySelectorAll(selector).forEach((node) => {
        createApp(component, parseProps(node)).mount(node);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    mountComponent('[data-premium-component="header"]', PremiumHeader);
    mountComponent('[data-premium-component="footer"]', PremiumFooter);
    mountComponent('[data-premium-component="home"]', PremiumHome);
});
