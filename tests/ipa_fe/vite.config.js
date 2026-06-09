import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'
import Unfonts from 'unplugin-fonts/vite'
import themeConfig from './theme.config'

const resolve = path.resolve

// https://vitejs.dev/config/
export default defineConfig(({ mode }) => {
  const env = mode === 'production' ? '"production"' : '"development"'

  return {
    build: {
      chunkSizeWarningLimit: 750,
      manifest: true,
      emptyOutDir: true,
      sourcemap: true,
      entry: resolve(__dirname, 'src/App.jsx'),
      rollupOptions: {
        output: {
          assetFileNames: '[name].[ext]',
          entryFileNames: 'index.js',
        },
      },
    },
    css: { devSourcemap: true },
    define: {
      'process.env.NODE_ENV': env,
      'process.env.DISABLE_BLOB_EFFECT': JSON.stringify(process.env.DISABLE_BLOB_EFFECT || 'false')
    },
    resolve: {
      alias: { '@': path.resolve(__dirname, 'src/') },
    },
    plugins: [react(), Unfonts(themeConfig.font.vite)],
    ssr: {
      noExternal: ['react-helmet-async'],
    },
    server: {
      fs: {
        // Allow serving files from one level up to the project root
        allow: ['../../../../'],
      },
      watch: {
        usePolling: true,
      },
    },
  }
})
