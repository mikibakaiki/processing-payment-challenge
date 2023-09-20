import axios from 'axios';

const instance = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'https://laravel.test',
  // you can add more configuration here if needed
});

instance.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
instance.defaults.headers.post['Content-Type'] = 'application/json';
instance.defaults.headers.common['Accept'] = 'application/json';


export default instance;
