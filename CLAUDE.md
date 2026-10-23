# Markea IA (WordPress)

Landing "Markea IA" que reemplaza markeaagency.com. Administrable desde wp-admin sin tocar codigo.
Entorno local: el WordPress de ServBay en esta carpeta (BD `markea_ia`, `WP_ENVIRONMENT_TYPE=local`).

## Reglas
- Nunca editar el core de WordPress; todo por hooks y filtros.
- Datos en el plugin `markea-core` (namespace `Markea\Core`, prefijo `markea_`); el theme `markea-ia` solo presenta.
- Sin secretos en el repo: `.env` ignorado, `.env.example` versionado.
- Codigo en ingles; labels y ayudas del admin en espanol (es_MX). Sin emojis en textos redactados.
- Escapar salida, sanitizar entradas. PHP 8.2+, Vite, CSS moderno. Sin jQuery ni Bootstrap JS en el front.
- Los post types usan los permisos nativos de WordPress (sin roles propios). Sin HubSpot.

## Estructura
- `wp-content/plugins/markea-core/`: CPT (`proyecto`, `estratega`, `lead`) y taxonomia `categoria-proyecto`; campos ACF PRO por codigo (la landing es UN Flexible Content `markea_sections` con un layout por seccion); Site Settings (opciones ACF); tracking (GTM/GA4/Pixel, solo produccion); endpoint REST `markea/v1/leads`; seeder `wp markea seed` (contenido del Figma, sin pisar ediciones; adjunta imagenes de `assets/seed/`).
- Gestion por agente (`markea-core/src/Abilities/`): 27 capacidades `markea/*` de la Abilities API del core (landing por seccion con `update_sub_field`, Site Settings, contenido de page/post/proyecto/estratega, medios, menus, SEO Yoast, purga de cache, bitacora). Politica: editar, publicar y papelera; sin borrado definitivo, sin usuarios/plugins/tema ni tracking, mantenimiento o correo destino del formulario, sin leads. Cada capacidad valida con el esquema y con `current_user_can`; toda escritura queda en `Audit` (opcion `markea_mcp_audit`, con el valor anterior). Se exponen por REST (`wp-json/wp-abilities/v1`) y, con el plugin oficial MCP Adapter (WordPress/mcp-adapter, NO versionado: instalarlo aparte), por MCP en `/wp-json/mcp/markea` (clase `McpServer`, solo Editor o superior; autenticacion con contrasena de aplicacion; `HeaderAuth` acepta la misma credencial Basic en la cabecera `X-API-Key` solo en las rutas del MCP y de `wp-abilities/v1`, porque los conectores de claude.ai solo permiten nombres de cabecera aprobados y reservan `Authorization` para OAuth; en el conector va con autenticacion "Sin inicio de sesion" y valor `Basic <base64 de usuario:contrasena de aplicacion>`). Filtro `markea_mcp_post_types` para ampliar tipos. Pruebas locales: scripts con `wp-load.php` y `wp_get_ability()->execute()`. Solo local por ahora (decision 2026-10-23).
- `wp-content/themes/markea-ia/`: `functions.php` minimo que carga `inc/` (setup, enqueue, icons, hooks, template-functions); `templates/landing.php`; `template-parts/home/section-*.php` (la fila del layout llega en `$args['row']`); `template-parts/menu.php` (overlay del menu); `assets/src` (Vite): `css/grid.scss` (grid de Bootstrap 5.3.3 SOLO row/col, margenes 16/32/120, gutter 16) y `css/main.css` (indice al inicio, una seccion por bloque numerado); `assets/icons` e `assets/images` (SVG/imagenes exportados del Figma); `assets/dist` se compila y NO se versiona.
- Motion (referencia: mdx.so): GSAP + ScrollTrigger + Lenis en un chunk aparte (`assets/src/js/motion/`), que solo carga si `html.has-motion` (la pone un script en `<head>` cuando no hay `prefers-reduced-motion` ni "Desactivar animaciones" en Site Settings > Animaciones). Easing unico `0.6, 0.08, 0.02, 0.99` (`--ease-markea` en CSS, `markea` en GSAP). Entradas por scroll sobre los hijos de `.container` de cada seccion (opcional `data-reveal`), sin snap de scroll ni secciones a pantalla completa (se descartaron: entorpecian y alejaban del Figma), menu y boton del menu con transiciones CSS (solo cuando `html[data-motion=ready]`), flecha que se intercambia al hover en los botones (`.btn__arrow`), circulo solo en los filtros, loader opcional (apagado por defecto). Los selectores ocultos en CSS (seccion 33) deben coincidir con `reveal.js`.
- Carruseles: Swiper 11 (`assets/src/js/carousel.js`, se carga solo cuando el media query de `data-swiper-mq` coincide; en movil: problema, como trabajamos y estrategas; el HTML es una grilla normal fuera del carrusel). Barra CTA fija en movil (`sticky-cta.js`).
- Compilar: `cd wp-content/themes/markea-ia && npm install && npm run build` (o `npm run dev` con `MARKEA_VITE_DEV` en wp-config).

## Diseno
- Fuente de verdad: Figma `YKKmmuL5veJsw4wWWNu3xa` ("Propuestas de Disenos - Markea IA"), frame `Markea IA Homepage - Type A Future v2`. Lo OCULTO en Figma no es diseno.
- Tokens: colores base en Site Settings > Colores (navy, electric, white, cyan, purple, indigo, dark); transparencias calculadas en CSS. Tipografia: Montserrat (titulos) y Poppins (texto), autoalojadas.
- Cada seccion de `main.css` documenta sus medidas del Figma.

## Base de datos y deploy
- Estructura por codigo; contenido inicial con `wp markea seed` (idempotente). Direccion unica: produccion -> local.
- Deploy en pausa (se trabaja en local). Plan: GitHub Actions + rsync solo de plugin y theme, compilando `assets/dist` en CI; respaldo antes de cada deploy; purgar cache de SiteGround.

## Pendiente
Ver la memoria del proyecto (`project-markea-ia`): URL del video, redes, newsletter, aviso de cookies, destino/consentimiento del formulario, Lighthouse, workflow de deploy.
