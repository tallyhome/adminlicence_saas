import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Configuration de Laravel Echo pour les WebSockets
 * Ceci permet de recevoir les notifications en temps réel
 */
import './notifications';
