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
	css: {
		preprocessorOptions: {
			scss: {
				// Bootstrap 5.3 still uses @import; silence Dart Sass deprecation noise from node_modules.
				quietDeps: true,
				silenceDeprecations: [ 'import', 'global-builtin', 'color-functions' ],
			},
		},
	},
	server: {
		host: 'localhost',
		port: 5173,
		strictPort: true,
		cors: true,
	},
} );
