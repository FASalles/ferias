import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
      ],
      refresh: true,
    }),
  ],
  // Configuração para resolver problemas comuns (opcional)
  resolve: {
    alias: {
      // Ajusta o caminho para facilitar imports, se precisar
      '@': '/resources/js',
    },
  },
  css: {
    postcss: './postcss.config.js', // usa o arquivo postcss.config.js se existir
  },
});
