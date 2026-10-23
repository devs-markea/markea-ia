# Changelog

Cambios notables de la landing Markea IA. El plugin `markea-core` y el theme `markea-ia` comparten version.

El formato se basa en [Keep a Changelog](https://keepachangelog.com/es-ES/1.1.0/) y el proyecto sigue [Versionado Semantico](https://semver.org/lang/es/). Mientras la version sea 0.x, un cambio menor (0.Y.0) puede incluir cambios de comportamiento.

Fechas en AAAA-MM-DD. Los tipos de cambio son Agregado, Cambiado, Obsoleto, Eliminado, Corregido y Seguridad; los que no aplican a una version se omiten.

## [Sin publicar]

## [0.3.0] - 2026-10-23

### Agregado
- Gestion del sitio por agente: 27 capacidades `markea/*` de la Abilities API del core. Cubren la landing (leer, editar campos, agregar filas y secciones), Site Settings, contenido de paginas, entradas, proyectos y estrategas (crear, editar, publicar, programar, papelera y restaurar), medios (subir desde una URL https y editar el texto alternativo), menus (editar y agregar enlaces, asignar ubicacion), SEO de Yoast, terminos, purga de cache y resumen del sitio.
- Servidor MCP en `/wp-json/mcp/markea` con el plugin oficial MCP Adapter, que se instala aparte y no se versiona. Solo entran usuarios con rol Editor o superior, y cada herramienta valida su esquema y los permisos de WordPress del usuario.
- Bitacora de cambios (`markea/audit-log`) con quien, cuando, que y el valor anterior, para poder deshacer cualquier edicion.
- Autenticacion con contrasena de aplicacion tambien por la cabecera `X-API-Key`, solo en las rutas del MCP y de `wp-abilities/v1`. Los conectores de claude.ai solo aceptan nombres de cabecera aprobados y reservan `Authorization` para OAuth.
- Modo mantenimiento: los visitantes ven un aviso 503 y los editores con sesion ven la landing; el titulo y el mensaje se editan en Site Settings > Mantenimiento. Ya estaba activo en produccion desde 2026-10-15.
- Textos de los pilares 2 a 4 de Marketing Operating System y de los puntos 2 y 3 de Relevancia sectorial (en el seeder).

### Seguridad
- La gestion por agente no puede borrar de forma definitiva ni tocar usuarios, plugins, tema, tracking, mantenimiento, el correo destino del formulario ni los leads.
- Cada valor se valida por tipo de campo y no se escribe nada si alguno es invalido; las URLs de medios deben ser https y solo se admiten imagenes (jpg, png, gif, webp, avif), video mp4 o webm y PDF de hasta 25 MB, sin SVG.

## [0.2.0] - 2026-10-15

Desplegado a produccion en tres entregas.

### Agregado
- Carruseles Swiper 11 solo en movil (problema, como trabajamos, estrategas); el HTML sigue siendo una grilla normal fuera del carrusel. Reemplazan a Splide.
- Barra CTA fija en movil, que se oculta sobre el hero, el formulario y el footer.
- Panel de menu a pantalla completa y footer en dos columnas con boton de volver arriba, al estilo de mdx.so.
- Campo opcional "Ancla del menu" en cada seccion de la landing, para enlazar con `#servicios`, `#contacto`, etc. (el ancla queda en el borde superior de la seccion).
- Flecha que se intercambia al hover en todos los CTA (`.btn__arrow`).
- Tokens compartidos de tarjetas: `--radius-card` y `--shadow-flat`, `--shadow-raised`, `--shadow-hover`.

### Cambiado
- Escala tipografica movil unificada (textos de 16 px, texto de tarjeta de 15 px) y escala de titulos en dos niveles (h2 30 px, h3 22 px).
- Entradas moviles: inputs de 16 px, objetivos tactiles de 44 px, texto minimo de 12 px, formulario con campos en dos columnas.
- Acordeon de relevancia con animacion de apertura y cierre.
- Imagenes de proyectos mas bajas en movil; mejor espacio entre icono, titulo y descripcion en "Como trabajamos"; titulo del orbe del caso con mas aire.
- Fotos rotadas (quienes somos, relevancia) reposicionadas para que no se corten en pantallas pequenas.
- Glows de seccion mas pequenos y ligeros en movil; el fade del marquee de herramientas ahora es una mascara.
- Menu inicial (seed): "Contacto" (`/#contacto`) reemplaza a "Nosotros".

### Eliminado
- Enlace "Hablemos" del header (vive dentro del menu).
- Dependencia Splide.

### Corregido
- Sombras de las tarjetas cortadas dentro de los carruseles.
- Doble borde en las tarjetas del Marketing Operating System (el borde solo aparece cuando la tarjeta esta inactiva).
- Hover de los botones principales del mismo color que el fondo oscuro: se quita el circulo (queda solo en los filtros) y se aclara el hover en secciones oscuras.
- El cubo 3D del hero ya no captura el touch en movil: la pagina hace scroll normal (en escritorio no cambia).

## [0.1.0] - 2026-10-11

Primera version: la landing completa segun el Figma.

### Agregado
- Plugin `markea-core`: tipos de contenido `proyecto`, `estratega` y `lead` y la taxonomia `categoria-proyecto`; campos ACF PRO por codigo, con la landing como un solo Flexible Content (`markea_sections`) de 14 secciones; Site Settings (contacto, redes, marca, colores, formulario, menu, animaciones, footer y newsletter, con la pestana de tracking solo para administradores); tracking de GTM, GA4 y Meta Pixel solo en produccion; endpoint `markea/v1/leads` con honeypot y limite de envios que guarda el lead antes de enviar el correo; seeder `wp markea seed` idempotente con el contenido del Figma. Sin HubSpot y con los permisos nativos de WordPress.
- Theme `markea-ia`: header y menu superpuesto accesibles, las 14 secciones del Figma, footer con wordmark, build con Vite, grid de Bootstrap 5.3.3 (solo row y col), tipografias Montserrat y Poppins autoalojadas, colores tomados de Site Settings y glows decorativos con degradados radiales.
- Motion con GSAP, ScrollTrigger y Lenis en un chunk aparte, solo si no hay `prefers-reduced-motion` ni "Desactivar animaciones": entradas por scroll, intro del hero, efectos del menu y de los botones, pantalla de carga opcional, filtros animados de proyectos, pila de tarjetas de unidades de negocio, wordmark del footer por letras, contenido de las tarjetas que entra escalonado, acordeon de Marketing Operating System por clic con badge, destellos en las tarjetas de Oferta, profundidad al hover, elipse y disco de la linea de tiempo en Proximos pasos, y contador de anos.

[Sin publicar]: https://github.com/devs-markea/markea-ia/compare/v0.3.0...HEAD
[0.3.0]: https://github.com/devs-markea/markea-ia/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/devs-markea/markea-ia/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/devs-markea/markea-ia/releases/tag/v0.1.0
