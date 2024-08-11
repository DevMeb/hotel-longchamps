import { createApp } from 'vue';
import { createPinia } from 'pinia';

import router from '@/router';

import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';

import axios from 'axios';

window.axios = axios;
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.baseURL = import.meta.env.VITE_API_URL;

if (localStorage.getItem('token')) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('token')}`;
}

const app = createApp({});
const pinia = createPinia();

app.use(pinia);

app.use(router);

app.use(Toast);

app.mount('#app');
