import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
  base: process.env.NODE_ENV === 'production' ? '/' : '',
  plugins: [vue()],
  server: {
    port: parseInt(process.env.VITE_APP_PORT, 10) || 5173
  },
  resolve: {
    alias: {
      '@': '/src',
    },
},
})
