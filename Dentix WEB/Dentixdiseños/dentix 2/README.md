# DentixWebCLD — Frontend Mockup

**Dentix Productos Dentales, S.L.**  
Tienda online B2B de instrumental odontológico profesional

---

## Descripción

Este repositorio contiene el frontend completo de la tienda online de Dentix: 
todas las páginas HTML del mockup de referencia, el CSS compartido, el JavaScript 
y la documentación técnica del proyecto.

**Stack de producción:** WordPress + WooCommerce + SAGE 50 Cloud (vía Conecta HUB)  
**Servidor:** VPS Dinahosting (NVMe · PHP 8.5 · MySQL 8)  
**Repositorio:** `DentixWebCLD` en GitHub

---

## Estructura del proyecto

```
DentixWebCLD/
│
├── 📄 index.html                   Homepage con carrusel hero
├── 📄 tienda.html                  Catálogo con filtros laterales
├── 📄 producto.html                Ficha de producto B2B
├── 📄 carrito.html                 Cesta de la compra
├── 📄 checkout.html                Proceso de pago (NIF/CIF B2B)
├── 📄 confirmacion.html            Pedido confirmado
├── 📄 mi-cuenta.html               Área privada del cliente
├── 📄 pedidos.html                 Historial de pedidos
├── 📄 pedido-detalle.html          Detalle de un pedido
├── 📄 login.html                   Login / Registro profesional
├── 📄 busqueda.html                Resultados de búsqueda por SKU
├── 📄 catalogo.html                Vista alternativa catálogo
├── 📄 marcas.html                  Página de marcas
├── 📄 ofertas.html                 Ofertas y promociones
├── 📄 contacto.html                Formulario de contacto
├── 📄 aviso-legal.html             Aviso legal
├── 📄 politica-privacidad.html     Política de privacidad (RGPD)
├── 📄 condiciones-venta.html       Condiciones generales de venta
├── 📄 politica-devoluciones.html   Política de devoluciones (14 días)
├── 📄 politica-cookies.html        Política de cookies
│
├── assets/
│   ├── css/
│   │   └── dentix.css              Sistema de diseño compartido (todas las páginas)
│   └── js/
│       ├── dentix.js               JS compartido (buscador, pills, wishlist)
│       └── carousel.js             JS carrusel hero (solo index.html)
│
├── images/                         Imágenes locales del carrusel
│   ├── 1.jpeg                      Slide 1
│   ├── 2.jpeg                      Slide 2
│   ├── 3.jpeg                      Slide 3
│   ├── 4.jpeg                      Slide 4
│   └── 5.jpeg                      Slide 5
│
├── 📋 SAGE50-INTEGRACION.md        Guía completa integración SAGE 50 ↔ WooCommerce
├── 📋 DESPLIEGUE.md                Guía de despliegue en servidor y configuración
└── 📋 README.md                    Este archivo
```

---

## Páginas del front — descripción rápida

| Página | Descripción | Notas |
|--------|-------------|-------|
| `index.html` | Homepage con carrusel 5 slides, bento categorías, productos destacados | Carrusel con mousedown/mouseup |
| `tienda.html` | Catálogo con sidebar de filtros, ordenación, paginación | Filtros por categoría, marca, precio |
| `producto.html` | Ficha de producto: galería, variantes, cantidad, tabs info | Tabs: descripción, specs, docs, reviews |
| `carrito.html` | Cesta con líneas de pedido, subtotales, cupón | Cálculo automático |
| `checkout.html` | Checkout una página con campo NIF/CIF obligatorio B2B | Métodos: GetNet, Stripe, PayPal, Bizum, Klarna |
| `confirmacion.html` | Pedido confirmado con número y resumen | |
| `mi-cuenta.html` | Dashboard cliente: pedidos, direcciones, datos personales | |
| `pedidos.html` | Historial paginado de pedidos | |
| `pedido-detalle.html` | Detalle de un pedido con tracking | |
| `login.html` | Login existente + Registro nuevo profesional | Acceso exclusivo B2B |
| `busqueda.html` | Resultados búsqueda por SKU/referencia | |
| `marcas.html` | Grid de marcas distribuidoras | |
| `ofertas.html` | Productos en oferta con descuentos | |
| `contacto.html` | Formulario + teléfono + info empresa | |
| Páginas legales | Aviso, privacidad, condiciones, devoluciones, cookies | Generadas con Complianz en WordPress |

---

## Sistema de diseño (dentix.css)

### Paleta de colores
```css
--ink:         #1A1A1A   /* Negro puro */
--dark-main:   #2D2D2D   /* Carbón principal */
--dark-soft:   #3A3A3A   /* Carbón suave */
--gray-logo:   #9A9898   /* Gris logo */
--gray-mid:    #6B6868   /* Textos secundarios */
--gray-light:  #F2F2F2   /* Fondos imágenes */
--bg-soft:     #F0EEED   /* Fondo cálido */
--panel:       #F7F6F5   /* Gris panel */
--cream:       #F8F6F1   /* Fondo body */
--border:      #E8E4DC   /* Bordes */
--red:         #C0392B   /* Rojo corporativo */
--red-hover:   #D44333   /* Rojo hover */
```

### Tipografía
- **Títulos:** Playfair Display (400, 600, 700, italic)
- **Cuerpo:** DM Sans (300, 400, 500, 600)

### Breakpoints responsive
- `1100px` — Tablet landscape / desktop pequeño
- `768px`  — Tablet portrait
- `480px`  — Móvil

---

## Carrusel Hero — comportamiento

El carrusel de la homepage avanza automáticamente cada 5.5 segundos.

- **Avance automático:** siempre activo
- **Pausa:** solo mientras se mantiene pulsado el botón del ratón (`mousedown`)
- **Reanuda:** al soltar (`mouseup`) o al salir del área (`mouseleave`)
- **Dots:** clic en un dot → salta a ese slide y continúa el auto

```javascript
heroEl.addEventListener('mousedown',  stopAuto);   // pulsar → pausa
heroEl.addEventListener('mouseup',    startAuto);  // soltar → reanuda
heroEl.addEventListener('mouseleave', startAuto);  // sale del hero → reanuda
```

---

## Flujo de despliegue

```
git push origin pruebas       → Deploy automático en InstaWP (staging)
                                 Cliente revisa y aprueba ✅
git merge pruebas → produccion → Deploy automático en www.dentix.es
```

Documentación completa: **DESPLIEGUE.md**

---

## Integración SAGE 50 Cloud

```
SAGE 50 Cloud → Conecta HUB → WooCommerce

Sincroniza: SKUs, precios, stock, categorías, imágenes
Exporta: Pedidos, clientes, datos fiscales → SAGE 50
```

Documentación completa con todos los pasos: **SAGE50-INTEGRACION.md**

---

## Decisiones arquitectónicas

| Decisión | Opción elegida |
|----------|---------------|
| E-commerce | WordPress + WooCommerce |
| Conector SAGE 50 | Conecta HUB |
| Hosting | VPS Dinahosting (NVMe, Madrid) |
| Pasarela principal | GetNet (Santander) |
| Otras pasarelas | Stripe, PayPal, Bizum, Klarna |
| Tema | Astra Pro |
| Caché | LiteSpeed Cache + Cloudflare |
| Deploy | GitHub Actions + FTP Deploy |
| Staging | InstaWP (gratuito) |
| SEO | Yoast SEO for WooCommerce |
| Legal/RGPD | Complianz |

---

*DentixWebCLD · Dentix Productos Dentales, S.L. · v3.0 · Febrero 2026*
