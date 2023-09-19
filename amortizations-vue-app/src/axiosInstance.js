import axios from 'axios';

const instance = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  // you can add more configuration here if needed
});

instance.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


export default instance;
