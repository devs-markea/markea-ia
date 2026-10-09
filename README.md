# Markea IA

Landing "Markea IA" para markeaagency.com sobre WordPress. Todo el contenido se administra desde wp-admin.

- `wp-content/plugins/markea-core/`: datos (post types, campos ACF, Site Settings, tracking, leads, seeder).
- `wp-content/themes/markea-ia/`: presentacion (templates, estilos, assets con Vite).
- `scripts/`: sincronizacion de BD y deploy.

El repo versiona solo el plugin y el theme propios. El core, `wp-config.php`, uploads y secretos estan ignorados.
Ver `CLAUDE.md` para reglas y decisiones.
