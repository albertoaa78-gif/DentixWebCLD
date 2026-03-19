# Guía de Despliegue y Configuración
## Dentix Productos Dentales, S.L. — dentix.es

---

## Índice
1. [Arquitectura de entornos](#1-arquitectura-de-entornos)
2. [Configuración del servidor VPS](#2-servidor-vps)
3. [Instalación de WordPress y WooCommerce](#3-wordpress-y-woocommerce)
4. [GitHub y despliegue automático](#4-github-y-despliegue-automático)
5. [Entorno de staging (InstaWP)](#5-staging-instawp)
6. [Plugins: instalación y configuración](#6-plugins)
7. [Pasarelas de pago](#7-pasarelas-de-pago)
8. [Seguridad y RGPD](#8-seguridad-y-rgpd)
9. [Rendimiento y caché](#9-rendimiento-y-caché)
10. [Backups](#10-backups)
11. [SEO técnico](#11-seo-técnico)
12. [Dominio y DNS](#12-dominio-y-dns)
13. [Checklist de lanzamiento](#13-checklist-de-lanzamiento)

---

## 1. Arquitectura de entornos

```
Desarrollador
    │
    │ git push origin pruebas
    ▼
GitHub (rama: pruebas)
    │
    │ GitHub Actions → FTP Deploy
    ▼
InstaWP (staging)
    │
    │ Cliente revisa y aprueba ✅
    │ git merge pruebas → produccion
    ▼
GitHub (rama: produccion)
    │
    │ GitHub Actions → FTP Deploy
    ▼
VPS Raiola Networks / Dinahosting
www.dentix.es (PRODUCCIÓN)
```

### Regla de flujo
**Ningún cambio llega a la web pública sin aprobación del cliente.**

---

## 2. Servidor VPS

### 2.1 Contratar VPS en Dinahosting
```
1. Ir a dinahosting.com → VPS → Seleccionar plan VPS Negocio o superior
   Requisitos mínimos para 10.000 SKUs:
     RAM:      4 GB mínimo (8 GB recomendado)
     CPU:      4 vCores
     NVMe:     80 GB mínimo
     Ancho de banda: ilimitado
     PHP:      8.1+ (8.5 disponible)
     MySQL:    8.0+
     LiteSpeed / Apache con caché

2. Completar la contratación con los datos de Dentix:
   Titular: Dentix Productos Dentales, S.L.
   CIF: B-85937787

3. Anotar:
   IP del servidor: xxx.xxx.xxx.xxx
   Acceso SSH: ssh usuario@xxx.xxx.xxx.xxx
   Panel de control: panel.dinahosting.com
   FTP: ftp.dentix.es (configurar en GitHub Actions)
```

### 2.2 Configuración PHP (php.ini)
Acceder al panel de control del VPS y ajustar:
```ini
; Necesario para WordPress con 10.000 productos
memory_limit          = 512M
max_execution_time    = 300
max_input_time        = 300
upload_max_filesize   = 64M
post_max_size         = 64M
max_input_vars        = 5000

; Para WooCommerce HPOS y sincronización SAGE
opcache.enable        = 1
opcache.memory_consumption = 256
opcache.max_accelerated_files = 20000
```

### 2.3 Configuración MySQL (my.cnf)
```ini
[mysqld]
innodb_buffer_pool_size   = 1G       # 25% de la RAM disponible
innodb_log_file_size      = 256M
query_cache_type          = 0        # Desactivar en MySQL 8
max_connections           = 150
slow_query_log            = 1
slow_query_log_file       = /var/log/mysql/slow.log
long_query_time           = 2
```

### 2.4 Configuración LiteSpeed (si aplica)
El servidor Dinahosting con LiteSpeed ya está optimizado para WordPress.
En el panel LiteSpeed Web Admin:
```
Configuration → Server → Tuning
  Max Connections:     500
  Connection Timeout:  60
  Keep-Alive Timeout:  15

PHP → External App
  Max Connections:     35
  Initial Request Timeout: 60
```

---

## 3. WordPress y WooCommerce

### 3.1 Instalación de WordPress
```bash
# Desde el panel de Dinahosting → Instalación automática de WordPress
# O manualmente via SSH:

cd /var/www/dentix.es/public_html
wget https://wordpress.org/latest.tar.gz
tar -xzf latest.tar.gz
mv wordpress/* .
rm -rf wordpress latest.tar.gz

# Crear base de datos
mysql -u root -p
CREATE DATABASE dentix_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dentix_user'@'localhost' IDENTIFIED BY '[contraseña_segura]';
GRANT ALL PRIVILEGES ON dentix_db.* TO 'dentix_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3.2 Configuración wp-config.php
```php
<?php
// Base de datos
define('DB_NAME',     'dentix_db');
define('DB_USER',     'dentix_user');
define('DB_PASSWORD', '[contraseña_segura]');
define('DB_HOST',     'localhost');
define('DB_CHARSET',  'utf8mb4');

// Seguridad — generar en: https://api.wordpress.org/secret-key/1.1/salt/
define('AUTH_KEY',         'xxx...');
define('SECURE_AUTH_KEY',  'xxx...');
define('LOGGED_IN_KEY',    'xxx...');
define('NONCE_KEY',        'xxx...');
// ... (resto de salts)

// Rendimiento
define('WP_MEMORY_LIMIT',       '512M');
define('WP_MAX_MEMORY_LIMIT',   '512M');
define('WP_CACHE',              true);   // Activar cuando esté LiteSpeed Cache

// Despliegue — deshabilitar edición de ficheros desde admin
define('DISALLOW_FILE_EDIT',    true);
define('DISALLOW_FILE_MODS',    false);  // Permitir instalar plugins

// URL de la instalación
define('WP_HOME',    'https://www.dentix.es');
define('WP_SITEURL', 'https://www.dentix.es');

// Revisiones y papelera (rendimiento)
define('WP_POST_REVISIONS', 5);
define('EMPTY_TRASH_DAYS',  14);

// Debug (SOLO en staging, nunca en producción)
define('WP_DEBUG',         false);
define('WP_DEBUG_LOG',     false);
define('WP_DEBUG_DISPLAY', false);

$table_prefix = 'dtx_';  // Prefijo personalizado (seguridad)
```

### 3.3 Instalación de WooCommerce
```
Panel Admin → Plugins → Añadir nuevo → buscar "WooCommerce" → Instalar → Activar
Seguir el asistente de configuración inicial:
  - País/región: España
  - Moneda: Euro (€)
  - Tipo de tienda: Productos físicos
  - Sector: Salud/Médico
```

### 3.4 Configuración WooCommerce para B2B
```
WooCommerce → Ajustes → General
  País de venta: España
  Moneda: Euro (EUR)
  Separador decimal: ,
  Separador de miles: .
  Número de decimales: 2

WooCommerce → Ajustes → Productos → Inventario
  Gestionar inventario: ✅ Activado
  Umbral stock bajo: 5
  Ocultar artículos agotados: ❌ No (mostrar como agotado)

WooCommerce → Ajustes → Impuestos
  Precios introducidos con impuesto: Sin impuesto (precios netos B2B)
  Visualizar precios en tienda: Sin impuesto
  Visualizar precios en carrito/checkout: Con impuesto

WooCommerce → Ajustes → Avanzado → Características
  HPOS (High Performance Order Storage): ✅ Activar
```

---

## 4. GitHub y Despliegue Automático

### 4.1 Estructura de ramas
```
main          → Rama de desarrollo integrada
pruebas       → Despliega automáticamente en InstaWP (staging)
produccion    → Despliega automáticamente en VPS (www.dentix.es)
```

### 4.2 GitHub Actions — Workflow de despliegue
Crear el archivo `.github/workflows/deploy.yml`:

```yaml
name: Deploy Dentix

on:
  push:
    branches:
      - pruebas
      - produccion

jobs:
  deploy-staging:
    if: github.ref == 'refs/heads/pruebas'
    runs-on: ubuntu-latest
    steps:
      - name: Checkout código
        uses: actions/checkout@v4

      - name: Deploy a InstaWP (staging)
        uses: SamKirkland/FTP-Deploy-Action@v4.3.4
        with:
          server:   ${{ secrets.STAGING_FTP_HOST }}
          username: ${{ secrets.STAGING_FTP_USER }}
          password: ${{ secrets.STAGING_FTP_PASS }}
          server-dir: /public_html/
          exclude: |
            **/.git*
            **/.git*/**
            **/node_modules/**
            SAGE50-INTEGRACION.md
            DESPLIEGUE.md
            README.md

  deploy-produccion:
    if: github.ref == 'refs/heads/produccion'
    runs-on: ubuntu-latest
    steps:
      - name: Checkout código
        uses: actions/checkout@v4

      - name: Deploy a VPS producción
        uses: SamKirkland/FTP-Deploy-Action@v4.3.4
        with:
          server:   ${{ secrets.PROD_FTP_HOST }}
          username: ${{ secrets.PROD_FTP_USER }}
          password: ${{ secrets.PROD_FTP_PASS }}
          server-dir: /public_html/
          exclude: |
            **/.git*
            **/.git*/**
            **/node_modules/**
            SAGE50-INTEGRACION.md
            DESPLIEGUE.md
            README.md
```

### 4.3 Secrets necesarios en GitHub
```
GitHub → Repositorio DentixWebCLD → Settings → Secrets and variables → Actions

Añadir los siguientes secrets:
  STAGING_FTP_HOST   → Host FTP de InstaWP
  STAGING_FTP_USER   → Usuario FTP de InstaWP
  STAGING_FTP_PASS   → Contraseña FTP de InstaWP
  PROD_FTP_HOST      → ftp.dentix.es (o IP del VPS)
  PROD_FTP_USER      → Usuario FTP del VPS
  PROD_FTP_PASS      → Contraseña FTP del VPS
```

### 4.4 Flujo de trabajo diario para el cliente
```bash
# El cliente tiene GitHub Desktop instalado (una sola vez, gratuito)
# Workflow habitual:

1. Desarrollador hace cambios y hace push a "pruebas"
2. GitHub Actions despliega automáticamente en InstaWP (~2 minutos)
3. Cliente abre el staging en el navegador y revisa
4. Si aprueba → Desarrollador hace merge de "pruebas" a "produccion"
5. GitHub Actions despliega en www.dentix.es (~2 minutos)
6. Web pública actualizada ✅
```

---

## 5. Staging InstaWP

### 5.1 Crear el sitio de staging
```
1. Ir a instawp.com → Crear sitio gratuito
2. Anotar la URL del staging (p.ej: https://dentix-staging.instawp.xyz)
3. Anotar credenciales FTP del sitio staging
4. Configurar como secret en GitHub (ver sección 4.3)
```

### 5.2 Diferencias staging vs producción
```
Staging:
  - URL diferente (instawp.xyz)
  - WordPress en modo debug activado
  - Pagos en modo test (Stripe test mode)
  - Emails desactivados o redirigidos a buzón de prueba
  - Robots.txt con Disallow: / (bloquear indexación)

Producción:
  - www.dentix.es con SSL
  - WordPress debug desactivado
  - Pagos en modo real
  - Emails activos (transaccionales)
  - SEO activo
```

### 5.3 Sincronización de base de datos staging → producción
Para llevar datos reales de productos (sincronizados desde SAGE 50) a producción:
```bash
# Exportar desde staging
wp db export staging_backup.sql --allow-root

# Buscar/reemplazar URLs
wp search-replace 'https://dentix-staging.instawp.xyz' 'https://www.dentix.es' --all-tables

# Importar en producción (hacer backup antes)
wp db import staging_backup.sql --allow-root
```

---

## 6. Plugins

### 6.1 Lista completa de plugins a instalar
```
ESENCIALES (instalar primero)
  □ WooCommerce             — Tienda online
  □ Astra (tema)            — Tema base (Astra Pro)
  □ LiteSpeed Cache         — Caché y rendimiento
  □ Yoast SEO               — SEO on-page
  □ Conecta HUB             — Conector SAGE 50

PAGOS
  □ WooCommerce Stripe      — Stripe (Visa, MC, Bizum, Google/Apple Pay)
  □ PayPal Payments         — PayPal
  □ Klarna for WooCommerce  — Klarna / pago aplazado
  □ GetNet for WooCommerce  — Pasarela principal (Santander)

LEGAL Y SEGURIDAD
  □ Complianz (o CookieYes) — RGPD y cookies
  □ WP 2FA                  — Autenticación 2 factores admin
  □ WP reCAPTCHA            — Protección formularios

FUNCIONALIDADES
  □ FiboSearch (o similiar) — Búsqueda avanzada por SKU
  □ Checkout Field Editor   — Campo NIF/CIF en checkout
  □ WooCommerce Shipment Tracking — Seguimiento de envíos
  □ Packlink PRO            — Integración transportistas
  □ FluentCRM (o Mailpoet) — Emails corporativos HTML
  □ ShortPixel              — Optimización imágenes WebP

ANÁLISIS
  □ MonsterInsights (o GA4) — Google Analytics 4
  □ UpdraftPlus             — Backups adicionales
```

### 6.2 Límite de plugins activos
Mantener un máximo de **15-18 plugins activos** en producción.
Revisar y desinstalar plugins inactivos regularmente.

### 6.3 Configuración del tema Astra Pro
```
Apariencia → Personalizar → Astra Options
  Logo: [subir logo Dentix SVG]
  Paleta de colores:
    Principal: #C0392B (rojo corporativo)
    Texto:     #1A1A1A
    Fondo:     #F8F6F1

Tipografía:
  Títulos:  Playfair Display
  Cuerpo:   DM Sans

  (Estas fuentes se cargan optimizadas desde Google Fonts)
```

---

## 7. Pasarelas de Pago

### 7.1 GetNet (pasarela principal)
```
WooCommerce → Ajustes → Pagos → GetNet
  Activar: ✅
  Modo: Producción (o Pruebas para staging)
  Merchant ID: [facilitado por GetNet/Santander]
  API Key: [facilitado por GetNet/Santander]
  Secret: [facilitado por GetNet/Santander]
  
  Para obtener credenciales:
  Contactar con Banco Santander/GetNet: getnet.santander.es
  Proceso: firma de contrato de TPV virtual → credenciales en 3-5 días
```

### 7.2 Stripe (Visa, Mastercard, Bizum, Google Pay, Apple Pay)
```
WooCommerce → Ajustes → Pagos → Stripe
  Activar: ✅
  Claves publicadas: pk_live_xxxxxxxxxxxx
  Clave secreta:     sk_live_xxxxxxxxxxxx
  
  Para obtener claves:
  1. Crear cuenta en stripe.com con el email de Dentix
  2. Dashboard Stripe → Developers → API Keys
  3. Para Bizum: activar en Dashboard Stripe → Payment methods → Bizum
     (disponible solo para cuentas Stripe España verificadas)
  4. Google Pay y Apple Pay: se activan automáticamente con Stripe JS v3
  
  Webhook Stripe:
    URL: https://www.dentix.es/wc-api/wc_stripe
    Eventos: payment_intent.succeeded, charge.refunded
```

### 7.3 PayPal
```
WooCommerce → Ajustes → Pagos → PayPal Payments
  Client ID:     [de PayPal Developer Dashboard]
  Secret Key:    [de PayPal Developer Dashboard]
  
  Para obtener:
  1. developer.paypal.com → Log In con cuenta Dentix
  2. My Apps & Credentials → Create App
  3. Copiar Client ID y Secret
```

### 7.4 Klarna
```
WooCommerce → Ajustes → Pagos → Klarna
  API Key Username: [de Klarna Merchant Portal]
  API Key Password: [de Klarna Merchant Portal]
  
  Para obtener:
  Solicitar cuenta merchant en klarna.com/es/business
  Klarna tiene proceso de aprobación (5-10 días hábiles)
```

---

## 8. Seguridad y RGPD

### 8.1 SSL (HTTPS)
```
# Let's Encrypt (incluido en VPS Dinahosting)
# Renovación automática cada 90 días

# Forzar HTTPS en .htaccess:
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# En wp-config.php añadir:
define('FORCE_SSL_ADMIN', true);
if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
    $_SERVER['HTTPS'] = 'on';
```

### 8.2 Configuración RGPD con Complianz
```
Instalar Complianz → Seguir el asistente de configuración
  Tipo de sitio:    Tienda online
  Países:          España (UE)
  Servicios:       Google Analytics, Stripe, PayPal, GetNet
  
  El asistente genera automáticamente:
  - Banner de cookies con control granular
  - Política de privacidad
  - Política de cookies
  - Términos y condiciones (revisar y adaptar)
```

### 8.3 Hardening WordPress
```
# .htaccess — bloquear acceso a archivos sensibles
<FilesMatch "(wp-config\.php|readme\.html|license\.txt)">
  Order Allow,Deny
  Deny from all
</FilesMatch>

# Desactivar XML-RPC (si no se usa)
<Files xmlrpc.php>
  Order Allow,Deny
  Deny from all
</Files>

# Limitar intentos de login — añadir en functions.php o plugin:
# Instalar "Limit Login Attempts Reloaded"
```

### 8.4 Autenticación 2 factores (administración)
```
Instalar plugin "WP 2FA"
  Usuarios → perfil de cada administrador → Configurar 2FA
  Método recomendado: aplicación TOTP (Google Authenticator, Authy)
  Activar obligatorio para: roles Administrador, Editor
```

---

## 9. Rendimiento y Caché

### 9.1 LiteSpeed Cache
```
LiteSpeed Cache → General → Activar:
  ✅ Cache del servidor
  ✅ Cache de páginas
  ✅ Cache de navegador
  ✅ QUIC.cloud CDN (gratuito con LiteSpeed)
  ✅ Minificación CSS/JS

LiteSpeed Cache → Imágenes:
  ✅ Activar WebP automático
  ✅ Lazy loading imágenes
  ✅ Optimización responsive

LiteSpeed Cache → Avanzado:
  Purgar caché automáticamente al: actualizar producto, nuevo pedido
```

### 9.2 Cloudflare CDN
```
1. Crear cuenta gratuita en cloudflare.com
2. Añadir dominio dentix.es
3. Cambiar nameservers en el registrador de dominio a los de Cloudflare
4. Configurar:
   SSL/TLS: Full (strict)
   Cache Level: Standard
   Browser Cache TTL: 4 hours
   Minify: HTML, CSS, JS ✅
   Rocket Loader: Desactivar (puede interferir con WooCommerce)

5. Crear Page Rule para no cachear el checkout:
   URL: *dentix.es/checkout*
   Setting: Cache Level = Bypass
```

### 9.3 Objetivo de rendimiento
```
Meta: 90+ puntos en Google PageSpeed (móvil y escritorio)

Medidas principales:
  □ Imágenes en formato WebP (ShortPixel + LiteSpeed)
  □ Fonts cargadas con display:swap
  □ CSS crítico inline (Astra Pro lo hace automáticamente)
  □ JS en diferido (defer/async)
  □ HPOS activo (WooCommerce queries más rápidas)
  □ Object cache con Redis (si el plan VPS lo incluye)
```

---

## 10. Backups

### 10.1 Backups del servidor (Dinahosting)
```
Los VPS de Dinahosting incluyen backups diarios automáticos (retención 7 días).
Verificar en panel Dinahosting → Backups que estén activados.
```

### 10.2 Backups adicionales con UpdraftPlus
```
Instalar UpdraftPlus → Configurar:
  Frecuencia backups base de datos: Diario
  Frecuencia backups archivos:       Semanal
  Retención: 30 copias de BD, 4 copias de archivos
  
  Destino de los backups (elegir uno):
  Google Drive: conectar con cuenta Google de Dentix
  Amazon S3:    crear bucket S3 en cuenta AWS de Dentix
  Dropbox:      conectar con cuenta Dropbox de Dentix

  ⚠️ IMPORTANTE: Probar la restauración desde backup cada 3 meses.
```

### 10.3 Backup antes de cada despliegue mayor
```bash
# Ejecutar antes de cualquier actualización mayor de WordPress/WooCommerce
wp db export backup_pre_update_$(date +%Y%m%d).sql
# O desde UpdraftPlus → Hacer backup ahora → Guardar en remoto
```

---

## 11. SEO Técnico

### 11.1 Configuración Yoast SEO
```
Yoast SEO → General → Ajustes del sitio
  Nombre del sitio: Dentix Productos Dentales
  Separador: ·

Yoast SEO → Búsqueda → Tipos de contenido
  Productos: Indexar ✅
  Categorías: Indexar ✅

Yoast SEO → Social
  Facebook Open Graph: ✅
  Twitter Cards: ✅

Yoast SEO → Mapa del sitio XML
  Activar: ✅
  URL: https://www.dentix.es/sitemap_index.xml
  Enviar a Google Search Console
```

### 11.2 robots.txt
```
User-agent: *
Disallow: /wp-admin/
Disallow: /wp-login.php
Disallow: /checkout/
Disallow: /carrito/
Disallow: /mi-cuenta/
Allow: /wp-admin/admin-ajax.php

Sitemap: https://www.dentix.es/sitemap_index.xml
```

### 11.3 Datos estructurados
Yoast SEO for WooCommerce genera automáticamente Schema.org para:
- Producto (Product, Offer, AggregateRating)
- Breadcrumbs (BreadcrumbList)
- Organización (Organization)
- Sitio web (WebSite con SearchAction)

Verificar en Google Rich Results Test: https://search.google.com/test/rich-results

---

## 12. Dominio y DNS

### 12.1 Contratar dominio
```
Opciones recomendadas:
  1. Dinahosting (mismo proveedor que el VPS — gestión centralizada)
  2. Raiola Networks

Dominio a contratar (decidir):
  dentix.es  (recomendado — mercado español, más barato)
  dentix.com (opcional — más universal, más caro)

Precio orientativo:
  .es:  ~8-10 €/año
  .com: ~12-15 €/año
```

### 12.2 Configuración DNS
```
Zona DNS del dominio → Añadir registros:

Tipo A (apuntar dominio a IP del VPS):
  Nombre: @          → IP del VPS
  Nombre: www        → IP del VPS

Tipo CNAME (Cloudflare si se usa):
  Nombre: @          → dentix.es (Cloudflare gestiona la IP)
  Nombre: www        → dentix.es

Registros de email (para emails corporativos):
  MX:  @  →  [servidor de correo: Google Workspace, Raiola, etc.]
  SPF: @  →  "v=spf1 include:_spf.google.com ~all"
  DKIM: según configuración del proveedor de email
```

---

## 13. Checklist de Lanzamiento

Completar en orden antes de hacer la web pública:

```
INFRAESTRUCTURA
□ VPS contratado y activo en Dinahosting
□ Dominio dentix.es registrado y apuntando al VPS
□ SSL activo (HTTPS funcionando sin errores)
□ PHP 8.1+ configurado
□ MySQL 8.0+ configurado
□ Backups automáticos activos

WORDPRESS / WOOCOMMERCE
□ WordPress instalado y actualizado
□ WooCommerce instalado y configurado
□ Tema Astra Pro instalado y diseño aplicado
□ HPOS activado en WooCommerce
□ Todos los plugins de la lista instalados y configurados
□ Panel admin accesible en dentix.es/wp-admin
□ 2FA activado para todos los administradores

INTEGRACIÓN SAGE 50
□ Conecta HUB instalado y conectado a SAGE 50 y WooCommerce
□ Mapeo de campos configurado
□ Sincronización inicial completada (10.000 SKUs)
□ Sincronización automática cada 15 minutos activa
□ Prueba de pedido → albarán SAGE completada
□ Alertas de error configuradas

PAGOS
□ GetNet activo en modo producción y probado
□ Stripe activo con Bizum habilitado
□ PayPal activo
□ Klarna activo (si aprobado)
□ Transferencia bancaria activa
□ Prueba de compra real con cada método completada

LEGAL
□ Aviso legal redactado y publicado
□ Política de privacidad redactada y publicada
□ Condiciones generales de venta publicadas
□ Política de devoluciones (14 días) publicada
□ Política de cookies con banner granular activo
□ Checkbox de términos en checkout obligatorio
□ Enlace a ODR (resolución litigios UE) en pie de página

SEO
□ Yoast SEO configurado
□ Sitemap XML enviado a Google Search Console
□ robots.txt configurado
□ Google Analytics 4 activo y verificado
□ Meta títulos y descripciones en categorías principales revisadas

RENDIMIENTO
□ PageSpeed móvil ≥ 90 puntos
□ PageSpeed escritorio ≥ 90 puntos
□ LiteSpeed Cache activo
□ Imágenes en WebP
□ Cloudflare CDN activo

GITHUB / DESPLIEGUE
□ Repositorio DentixWebCLD configurado
□ Ramas: main, pruebas, produccion creadas
□ GitHub Actions con secrets configurados
□ Deploy automático a InstaWP (pruebas) verificado
□ Deploy automático a VPS (produccion) verificado
□ Flujo aprobación cliente documentado y entendido

FUNCIONAL
□ Registro de cliente con validación NIF/CIF
□ Acceso solo para profesionales verificado
□ Búsqueda por SKU/referencia funcionando
□ Carrito persistente entre sesiones
□ Checkout en una página
□ Emails transaccionales recibidos correctamente
□ Área "Mi cuenta" con historial de pedidos
□ Tracking de envíos activo
□ Web responsiva verificada en móvil, tablet y escritorio

MONITORIZACIÓN
□ UptimeRobot configurado (alerta si web cae)
□ Alertas Conecta HUB activas
□ Alertas de stock bajo configuradas en WooCommerce
```

---

## Soporte y contactos

| Servicio | Contacto |
|----------|----------|
| Servidor VPS | Dinahosting: soporte@dinahosting.com · 902 195 019 |
| Dominio | Dinahosting: panel.dinahosting.com |
| Conecta HUB | soporte@conectahub.es |
| Stripe | dashboard.stripe.com → Support |
| GetNet | getnet.santander.es → Soporte |
| GitHub | github.com/support |
| WordPress | wordpress.org/support |

---

*Documento: DESPLIEGUE.md · Dentix Productos Dentales, S.L. · v1.0 · Febrero 2026*
