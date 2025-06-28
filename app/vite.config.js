import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import laravel from 'laravel-vite-plugin'
import { nodePolyfills } from 'vite-plugin-node-polyfills'


console.log('Vite config carregado!');

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/js/app.js'],
      refresh: true,
    }),
    vue(),
    nodePolyfills({
      include: ['crypto'], // Foco no polyfill do crypto
      globals: {
        Buffer: true,
        global: true,
        process: true,
      }
    })
  ],
 server: {
    host: '0.0.0.0',
    port: parseInt(env.VITE_PORT),           
    hmr: {
      host: env.VITE_HOST || 'localhost', 
      protocol: 'ws',
      port: parseInt(env.VITE_PORT)       
    }
  },
  define: {
    global: 'window' // Fix para variáveis globais
  }

  
})