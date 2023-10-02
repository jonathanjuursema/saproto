import { createApp, DefineComponent, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

import '../css/app.css';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const appEnv = import.meta.env.VITE_APP_ENV || 'production';

createInertiaApp({
  title: (title) => `${appEnv !== 'production' ? '[' + appEnv.toUpperCase() + '] ' : ''}${appName} | ${title}`,
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue, Ziggy)
      .mount(el);
  },
  progress: {
    color: '#4B5563',
  },
});
