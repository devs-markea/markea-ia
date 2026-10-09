import { defineConfig } from 'vite';

export default defineConfig( {
	base: './',
	build: {
		outDir: 'assets/dist',
		emptyOutDir: true,
		manifest: true,
		rollupOptions: {
			input: 'assets/src/js/main.js',
		},
	},
	server: {
		host: 'localhost',
		port: 5173,
		strictPort: true,
		cors: true,
	},
} );
