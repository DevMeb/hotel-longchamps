import { createApp } from 'vue';
import { createPinia } from 'pinia';

import router from '@/router';

import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';

import axios from 'axios';

axios.defaults.withCredentials = true;
axios.defaults.baseURL = import.meta.env.VITE_API_URL;

const app = createApp({});
const pinia = createPinia();

app.use(pinia);

app.use(router);

app.use(Toast);

app.mount('#app');
