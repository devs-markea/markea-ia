# Markea IA (WordPress)

Landing "Markea IA" que reemplaza markeaagency.com. Administrable desde wp-admin sin tocar codigo.

## Reglas
- Nunca editar el core de WordPress; todo por hooks y filtros.
- Logica de datos en el plugin `markea-core` (namespace `Markea\Core`, prefijo `markea_`); el theme `markea-ia` solo presenta (templates, estilos, assets).
- Sin secretos en el repo: `.env` ignorado, `.env.example` versionado.
- Codigo en ingles; labels y ayudas del admin en espanol (es_MX).
- Escapar salida, sanitizar entradas, nonces donde aplique.
- PHP 8.2+, JS/TS con Vite, CSS moderno. Sin jQuery en el front nuevo.
- Sin emojis en textos redactados.

## Estructura
- `wp-content/plugins/markea-core/`: post types (`proyecto`, `estratega`, `lead`), taxonomia `categoria-proyecto`, campos, Site Settings, tracking, formulario/leads, seeder.
- `wp-content/themes/markea-ia/`: `theme.json`, `functions.php` minimo que solo carga `inc/`, `inc/` (`hooks.php`, `template-functions.php`, `enqueue.php`, setup), template de la landing, `template-parts/home/` con un partial por seccion, assets con Vite. Sigue la convencion `inc/` + hooks + template-functions del theme actual.
- `scripts/`: sincronizacion de BD y deploy. `.github/workflows/`: deploy.
- Git excluye core, `wp-config.php`, `.env`, `node_modules`, `vendor`, uploads, caches y todo `wp-content/` salvo plugin y theme propios.

## Base de datos y deploy
- Estructura por codigo; contenido inicial con `wp markea seed` (idempotente).
- Direccion unica: produccion -> local. A produccion solo sube codigo (plugin y theme).
- Respaldo obligatorio antes de cada deploy; purgar cache de SiteGround tras deploy.

## Decisiones tomadas
- ACF Pro con licencia: campos por codigo y Options Page para Site Settings.
- Solo espanol. Pagina de contacto se migra; sitio nuevo por lo demas limpio.
- Formulario a la medida (posible plugin de forms extendido por hooks); siempre guarda lead.
- Entorno local: el WordPress de ServBay en esta misma carpeta (BD `markea_ia`); sin wp-env ni Docker. Deploy por CI/CD de Git (GitHub Actions); sin SSH confirmado en SiteGround.
- Son 3 unidades de negocio.

## Pendiente
Inventario del sitio actual, destino del formulario y texto de consentimiento, redirecciones 301, aviso de cookies, transporte exacto del deploy. Detalle en la memoria del proyecto (`project-markea-ia`).
