# Instrucciones de instalación en Dinahosting
## Dentix Productos Dentales — dentix.es

---

## LO QUE TIENES EN ESTE PAQUETE

| Archivo | Qué es | Cómo se instala |
|---|---|---|
| `dentix-theme.zip` | Tema WordPress completo (diseño + WooCommerce) | wp-admin → Apariencia → Temas → Subir |
| `dentix-setup.zip` | Plugin de configuración automática | wp-admin → Plugins → Añadir → Subir |
| `SAGE50-INTEGRACION.md` | Guía de conexión con SAGE 50 | Referencia técnica |
| `DESPLIEGUE.md` | Guía completa del servidor | Referencia técnica |

---

## PASO 1 — Instalar WordPress en Dinahosting (5 minutos)

1. Entra en **panel.dinahosting.com**
2. Ve a tu dominio → **Gestor de aplicaciones** → **WordPress**
3. Haz clic en **Instalar**
4. Rellena:
   - Directorio: déjalo vacío (raíz del dominio)
   - Usuario admin: elige uno seguro (NO uses "admin")
   - Contraseña: mínimo 16 caracteres con símbolos
   - Email: email del equipo técnico de Dentix
5. Haz clic en **Instalar**
6. En unos minutos tendrás WordPress en www.dentix.es

---

## PASO 2 — Instalar WooCommerce (2 minutos)

1. Entra en **www.dentix.es/wp-admin**
2. Ve a **Plugins → Añadir nuevo**
3. Busca: **WooCommerce**
4. Haz clic en **Instalar ahora** → **Activar**
5. Salta el asistente de configuración (el plugin de setup lo hará por ti)

---

## PASO 3 — Instalar el tema Dentix (1 minuto)

1. Ve a **Apariencia → Temas → Añadir nuevo → Subir tema**
2. Selecciona el archivo **`dentix-theme.zip`**
3. Haz clic en **Instalar ahora**
4. Haz clic en **Activar**
5. ✅ El tema Dentix ya está activo

> Si ves la web en blanco o con error después de activar el tema,
> ve a wp-admin → Herramientas → Salud del sitio y revisa los errores.

---

## PASO 4 — Instalar y ejecutar el plugin de setup (2 minutos)

1. Ve a **Plugins → Añadir nuevo → Subir plugin**
2. Selecciona el archivo **`dentix-setup.zip`**
3. Haz clic en **Instalar ahora** → **Activar**
4. Verás un aviso amarillo en el admin: haz clic en **"Ejecutar la configuración ahora"**
5. En la página del setup, lee la lista de acciones y haz clic en **"Ejecutar configuración inicial"**
6. ✅ En menos de 10 segundos todo quedará configurado:
   - Páginas legales creadas (Aviso legal, Privacidad, Cookies, Condiciones, Devoluciones)
   - WooCommerce configurado para España y B2B
   - Categorías del catálogo odontológico creadas
   - Atributo "Marca" creado con las principales marcas
   - Envío gratuito +150€ configurado
   - Permalinks optimizados para SEO
7. **Desactiva y elimina el plugin** una vez completado (ya no es necesario)

---

## PASO 5 — Añadir imágenes del carrusel (5 minutos)

El carrusel de la homepage necesita 5 imágenes propias de Dentix.

1. Ve a **wp-admin → Medios → Añadir nuevo**
2. Sube 5 fotos de alta calidad del instrumental / clínica
   - Tamaño recomendado: 1400×900px mínimo
   - Nombres sugeridos: instrumental-01.jpg, endodoncia-01.jpg, etc.
3. Ve a **Apariencia → Personalizar → Opciones del tema Dentix**
   (o directamente edita front-page.php con los IDs de las imágenes)

> ⚠️ Hasta que subas las imágenes, el carrusel mostrará fondos de color sólido.
> La tienda es totalmente funcional sin ellas.

---

## PASO 6 — Configurar los conectores de pago y SAGE 50

Una vez que la tienda base funciona, configura:

### GetNet (pasarela principal)
```
WooCommerce → Ajustes → Pagos → GetNet
Credenciales obtenidas en: getnet.santander.es
```

### Stripe (Visa, Mastercard, Bizum, Google Pay, Apple Pay)
```
WooCommerce → Ajustes → Pagos → Stripe
Credenciales obtenidas en: dashboard.stripe.com
```

### PayPal
```
WooCommerce → Ajustes → Pagos → PayPal
Credenciales obtenidas en: developer.paypal.com
```

### Conecta HUB (SAGE 50 → WooCommerce)
```
Ver el archivo SAGE50-INTEGRACION.md incluido en este paquete.
Pasos completos con capturas de pantalla.
```

---

## PLUGINS ADICIONALES RECOMENDADOS

Instalar desde **Plugins → Añadir nuevo**:

| Plugin | Qué hace | Prioridad |
|---|---|---|
| Complianz | Banner de cookies RGPD | Alta (legal) |
| LiteSpeed Cache | Caché y rendimiento | Alta |
| Yoast SEO | SEO on-page | Alta |
| WP 2FA | Doble autenticación admin | Alta (seguridad) |
| ShortPixel | Imágenes WebP automático | Media |
| Packlink PRO | Transportistas (SEUR, DHL...) | Media |
| MonsterInsights | Google Analytics 4 | Media |
| UpdraftPlus | Backups adicionales | Media |
| FiboSearch | Búsqueda avanzada por SKU | Media |

---

## PREGUNTAS FRECUENTES

**¿La tienda funciona sin las imágenes del carrusel?**
Sí. El carrusel muestra fondos de color corporativo hasta que subas las fotos.

**¿Necesito saber programar para instalar esto?**
No. Los pasos de arriba no requieren tocar ningún código.

**¿Qué pasa si algo sale mal durante el setup?**
El plugin de setup es no destructivo: si hay un error, puedes ejecutarlo de nuevo.
Los elementos ya creados simplemente se omiten.

**¿Cuándo configuro Conecta HUB con SAGE 50?**
Después de que la tienda base esté funcionando. Consulta `SAGE50-INTEGRACION.md`.

**¿Cómo actualizo la tienda con cambios del equipo de desarrollo?**
Siguiendo el flujo de GitHub: push a rama `pruebas` → revisas en InstaWP → merge a `produccion` → se despliega automáticamente. Ver `DESPLIEGUE.md`.

---

*Dentix Productos Dentales, S.L. — Instrucciones de instalación v1.0 — Febrero 2026*
