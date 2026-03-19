# Integración SAGE 50 Cloud ↔ WooCommerce
## Dentix Productos Dentales, S.L. — Guía técnica completa

---

## Índice
1. [Arquitectura de la integración](#1-arquitectura)
2. [Requisitos previos](#2-requisitos-previos)
3. [Instalación de Conecta HUB](#3-instalación-de-conecta-hub)
4. [Configuración en SAGE 50 Cloud](#4-configuración-en-sage-50-cloud)
5. [Configuración en WooCommerce](#5-configuración-en-woocommerce)
6. [Mapeo de campos SAGE ↔ WooCommerce](#6-mapeo-de-campos)
7. [Flujo de sincronización de catálogo](#7-flujo-catálogo)
8. [Flujo de pedidos y clientes](#8-flujo-pedidos-y-clientes)
9. [Verificación y pruebas](#9-verificación-y-pruebas)
10. [Panel de logs y alertas](#10-logs-y-alertas)
11. [Activar sincronización bidireccional futura](#11-bidireccional)
12. [Resolución de errores frecuentes](#12-errores)

---

## 1. Arquitectura

```
SAGE 50 Cloud (MAESTRO)
       │
       │  API SAGE 50 Cloud
       ▼
  CONECTA HUB
  (conector middleware)
       │
       │  REST API WooCommerce
       ▼
  WooCommerce / WordPress
  www.dentix.es

  Dirección INICIAL: SAGE → Web (unidireccional)
  Dirección FUTURA:  Bidireccional (activable en Conecta HUB sin código)
```

### Regla de oro
**SAGE 50 Cloud es la única fuente de verdad.**
Nunca se modifican productos, precios ni stock directamente en WooCommerce.
Cualquier cambio en el catálogo se realiza en SAGE 50 y Conecta HUB lo propaga.

---

## 2. Requisitos previos

| Requisito | Detalle |
|-----------|---------|
| SAGE 50 Cloud | Versión con API REST activada |
| Suscripción Conecta HUB | Plan adecuado al volumen (~10.000 SKUs). Ver [conectahub.es](https://conectahub.es) |
| WordPress | 6.4+ |
| WooCommerce | 8.0+ |
| PHP | 8.1+ |
| Plugin WooCommerce REST API | Activo (incluido en WooCommerce) |
| Clave API WooCommerce | Consumer Key + Consumer Secret (leer/escribir) |
| Credenciales SAGE 50 Cloud | Usuario con permisos API + clave de acceso |

---

## 3. Instalación de Conecta HUB

### 3.1 Contratar el servicio
1. Ir a **[conectahub.es](https://conectahub.es)** y seleccionar el plan SAGE 50 + WooCommerce.
2. Para ~10.000 SKUs con sincronización de stock en tiempo real, se recomienda el **plan Professional** (verificar precios actuales).
3. Completar el registro con los datos de Dentix Productos Dentales, S.L.

### 3.2 Instalar el plugin en WordPress
```bash
# Opción A — desde el panel de WordPress
Panel Admin → Plugins → Añadir nuevo → buscar "Conecta HUB" → Instalar → Activar

# Opción B — subir ZIP manualmente
Panel Admin → Plugins → Subir plugin → seleccionar conectahub.zip → Instalar → Activar
```

### 3.3 Obtener claves API de WooCommerce
```
WooCommerce → Ajustes → Avanzado → API REST → Añadir clave
  Descripción: Conecta HUB
  Usuario: [administrador]
  Permisos: Leer/Escribir
  → Generar clave API
  
  Guardar:
    Consumer Key:    ck_xxxxxxxxxxxxxxxxxxxxxxxxxxxx
    Consumer Secret: cs_xxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### 3.4 Conectar Conecta HUB con WooCommerce
```
Panel Conecta HUB → Conexiones → WooCommerce → Nueva conexión
  URL tienda:      https://www.dentix.es
  Consumer Key:    [pegar el ck_ generado]
  Consumer Secret: [pegar el cs_ generado]
  → Verificar conexión → Guardar
```

---

## 4. Configuración en SAGE 50 Cloud

### 4.1 Habilitar acceso API
```
SAGE 50 Cloud → Configuración → Integración → API REST
  → Activar acceso API
  → Generar token de acceso
  → Anotar: API Key, API Secret, URL base
```

### 4.2 Conectar SAGE en Conecta HUB
```
Panel Conecta HUB → Conexiones → SAGE 50 Cloud → Nueva conexión
  URL API SAGE:    [URL facilitada por SAGE]
  API Key:         [de SAGE 50 Cloud]
  API Secret:      [de SAGE 50 Cloud]
  Empresa:         Dentix Productos Dentales, S.L.
  → Verificar conexión → Guardar
```

### 4.3 Estructura de artículos en SAGE 50 recomendada
Para que la sincronización funcione correctamente, los artículos en SAGE 50 deben tener:

| Campo SAGE 50 | Obligatorio | Uso en WooCommerce |
|---------------|-------------|---------------------|
| Código artículo | ✅ Sí | SKU del producto |
| Descripción | ✅ Sí | Nombre del producto |
| Precio de venta | ✅ Sí | Precio WooCommerce |
| Stock | ✅ Sí | Stock WooCommerce |
| Familia/Categoría | Recomendado | Categoría WooCommerce |
| Descripción larga | Recomendado | Descripción del producto |
| Imágenes (ruta) | Recomendado | Imágenes del producto |
| EAN/Código barras | Recomendado | Campo personalizado |
| IVA aplicable | ✅ Sí | Clase de impuesto WooCommerce |
| Peso/Dimensiones | Recomendado | Cálculo de envío |

---

## 5. Configuración en WooCommerce

### 5.1 Taxonomías de categorías
Antes de la primera sincronización, crear en WooCommerce la estructura de categorías
que coincida con las familias de artículos en SAGE 50:

```
Catálogo/
├── Instrumental quirúrgico
├── Endodoncia
│   ├── Limas y archivos
│   └── Obturación
├── Ortodoncia
│   ├── Brackets
│   └── Arcos y ligaduras
├── Implantología
├── Material Clínico
├── Esterilización
├── Equipamiento
│   ├── Turbinas y piezas de mano
│   └── Unidades dentales
└── Radiología
```

### 5.2 Atributos de producto
Crear estos atributos globales en WooCommerce antes de sincronizar:
```
WooCommerce → Atributos → Añadir atributo
  - Marca (marca)           → slug: marca
  - Material (material)     → slug: material
  - Longitud (longitud)     → slug: longitud
  - Esterilización (ester.) → slug: esterilizacion
  - País origen             → slug: pais-origen
```

### 5.3 Activar HPOS (High Performance Order Storage)
```
WooCommerce → Ajustes → Avanzado → Características
  → Activar: "Almacenamiento de pedidos de alto rendimiento"
  → Guardar cambios
```

---

## 6. Mapeo de campos

En Conecta HUB, configurar el mapeo de campos entre SAGE 50 y WooCommerce:

```
Panel Conecta HUB → Mapeo → Productos → Configurar

SAGE 50 Campo          →  WooCommerce Campo
─────────────────────────────────────────────────────
Código artículo        →  SKU
Descripción corta      →  Nombre del producto (title)
Descripción larga      →  Descripción (description)
Precio venta tarifa 1  →  Precio regular (_regular_price)
Precio oferta          →  Precio rebajado (_sale_price)
Stock actual           →  Cantidad en stock (_stock)
Familia                →  Categoría (product_cat)
Subfamilia             →  Subcategoría (product_cat)
Marca (atributo)       →  Atributo: marca
IVA (tipo)             →  Clase de impuesto (tax_class)
Activo/Inactivo        →  Estado (status: publish/draft)
Peso                   →  Peso (_weight)
Imagen 1               →  Imagen destacada
Imágenes 2-5           →  Galería de imágenes
EAN                    →  Campo personalizado: _ean
```

### 6.1 Tarifas especiales B2B
SAGE 50 permite múltiples tarifas de precios por cliente.
En Conecta HUB, configurar:
```
Panel Conecta HUB → Tarifas → Configurar
  Tarifa general     → Precio WooCommerce base
  Tarifa cliente VIP → Plugin WooCommerce B2B Pricing (o meta campos)
  Tarifa clínica     → Grupo de usuario WooCommerce
```

---

## 7. Flujo de sincronización de catálogo

### 7.1 Primera sincronización (inicial)
```
Panel Conecta HUB → Sincronización → Productos → Sincronización completa
  → Seleccionar: Todos los artículos activos
  → Dirección: SAGE 50 → WooCommerce
  → Modo: Crear + Actualizar
  → Iniciar sincronización

  ⚠️ AVISO: Con 10.000 SKUs, esta operación puede tardar entre 30 y 90 minutos.
  No interrumpir el proceso.
```

### 7.2 Sincronización automática de catálogo
Después de la sincronización inicial, configurar actualizaciones periódicas:
```
Panel Conecta HUB → Automatización → Productos
  Frecuencia de sincronización:
    → Precios y stock: Cada 15 minutos (recomendado)
    → Datos de producto: Cada 2 horas
    → Stock crítico (<5 unidades): Webhook en tiempo real

  Opciones avanzadas:
    → Actualizar solo productos modificados: ✅ Activado
    → Crear nuevos productos automáticamente: ✅ Activado
    → Desactivar productos eliminados en SAGE: ✅ Activado
```

### 7.3 Webhook para stock en tiempo real
```
Panel Conecta HUB → Webhooks → Configurar
  Evento:    Cambio de stock en SAGE 50
  Endpoint:  https://www.dentix.es/wp-json/wc/v3/products/{id}
  Método:    PATCH
  Payload:   { "stock_quantity": {nuevo_stock} }

  Activar: ✅
```

---

## 8. Flujo de pedidos y clientes

### 8.1 Exportación de pedidos WooCommerce → SAGE 50
Cuando un cliente completa un pedido en la web:
```
1. WooCommerce crea el pedido (estado: processing)
2. Conecta HUB detecta el nuevo pedido (webhook o polling cada 5 min)
3. Conecta HUB crea en SAGE 50:
   - Nuevo cliente (si no existe) con NIF/CIF y datos fiscales
   - Albarán de venta con las líneas del pedido
   - Reserva de stock
4. SAGE 50 puede generar automáticamente la factura al estado "completed"
```

Configurar en Conecta HUB:
```
Panel Conecta HUB → Pedidos → Configurar
  Sincronizar pedidos:         ✅ Activado
  Estado WooCommerce → SAGE:
    processing  → Albarán
    completed   → Factura
    refunded    → Abono
  Crear cliente si no existe:  ✅ Activado
  Campo NIF/CIF:               Billing NIF (campo personalizado checkout)
  Campo Razón Social:          Billing Company
```

### 8.2 Datos fiscales obligatorios B2B
El checkout de WooCommerce incluye campo NIF/CIF (plugin de campos personalizados).
El mapeo en Conecta HUB debe enviar este campo al campo equivalente en SAGE 50:
```
WooCommerce meta: _billing_nif  →  SAGE 50: NIF/CIF del cliente
WooCommerce meta: _billing_company → SAGE 50: Razón social
```

### 8.3 Sincronización de clientes
```
Panel Conecta HUB → Clientes → Configurar
  Dirección: Bidireccional
    WooCommerce → SAGE 50: Nuevos clientes web
    SAGE 50 → WooCommerce: Clientes existentes + tarifas
  Clave de deduplicación: NIF/CIF (evita duplicados)
```

---

## 9. Verificación y pruebas

### 9.1 Checklist de verificación antes de producción
```
□ Conecta HUB muestra "Conectado" en ambos lados (SAGE + WooCommerce)
□ Sincronización inicial completada sin errores
□ Al menos 100 productos visibles en WooCommerce con precios y stock
□ Modificar precio de un producto en SAGE → verificar que cambia en WooCommerce en <15 min
□ Reducir stock de un artículo en SAGE → verificar que se refleja en WooCommerce
□ Realizar pedido de prueba en WooCommerce → verificar albarán creado en SAGE 50
□ Cliente nuevo creado en WooCommerce → verificar que aparece en SAGE 50
□ Producto con stock 0 en SAGE → verificar que aparece "Agotado" en WooCommerce
□ Panel de logs sin errores críticos (pueden haber warnings normales)
```

### 9.2 Pedido de prueba paso a paso
```
1. Activar modo de prueba en GetNet/Stripe
2. Registrarse como cliente en www.dentix.es (entorno staging)
3. Añadir 3-4 productos al carrito
4. Completar checkout con NIF de prueba: B-12345678
5. Usar tarjeta de prueba Stripe: 4242 4242 4242 4242
6. Verificar:
   a. Email de confirmación recibido
   b. Pedido visible en WooCommerce → Pedidos
   c. Albarán creado en SAGE 50
   d. Stock descontado en SAGE 50
   e. Cliente creado en SAGE 50 con NIF correcto
```

---

## 10. Panel de logs y alertas

### 10.1 Acceso al panel de logs
```
Panel Conecta HUB → Logs → Ver registros
  Filtrar por: Error / Warning / Info
  Período: Últimas 24h / 7 días / 30 días
```

### 10.2 Configurar alertas por email
```
Panel Conecta HUB → Alertas → Configurar
  Email de alertas: [email del equipo técnico]
  Alertar cuando:
    ✅ Error de sincronización de producto
    ✅ Error al exportar pedido a SAGE 50
    ✅ Pérdida de conexión con SAGE 50 API
    ✅ Pérdida de conexión con WooCommerce API
    ✅ Cola de sincronización con >100 elementos pendientes
```

### 10.3 Monitorización de stock crítico
```
WooCommerce → Ajustes → Productos → Inventario
  Umbral de stock bajo: 5
  Umbral de stock agotado: 0
  Notificaciones: email del equipo de almacén
```

---

## 11. Activar sincronización bidireccional futura

La arquitectura actual es unidireccional: **SAGE 50 → WooCommerce**.

Cuando Dentix decida activar la bidireccional completa:

```
Panel Conecta HUB → Sincronización → Productos → Dirección
  Cambiar: "Solo SAGE → WooCommerce"
        a: "Bidireccional"

  ⚠️ IMPORTANTE antes de activar bidireccional:
  1. Definir qué campos pueden modificarse desde WooCommerce
     (normalmente: imágenes, descripción web, meta SEO)
  2. SAGE 50 siempre tiene prioridad en: precio, stock, referencia
  3. Configurar resolución de conflictos:
     Precio:        SAGE 50 gana siempre
     Stock:         SAGE 50 gana siempre
     Descripción:   Última modificación gana (o elegir manualmente)
```

**No se requieren cambios de código.** La activación es puramente de configuración en el panel de Conecta HUB.

---

## 12. Resolución de errores frecuentes

### Error: "No se puede conectar con la API de SAGE 50"
```
Causa:    Token de API caducado o IP no autorizada
Solución:
  1. SAGE 50 Cloud → Configuración → API → Regenerar token
  2. Verificar que la IP del servidor (VPS Raiola/Dinahosting) está
     en la lista blanca de SAGE 50 Cloud
  3. Actualizar el token en Panel Conecta HUB → Conexiones → SAGE 50
```

### Error: "Producto no encontrado al sincronizar"
```
Causa:    El SKU en WooCommerce no coincide con el código de artículo en SAGE 50
Solución:
  1. Panel Conecta HUB → Logs → Ver el SKU del error
  2. Verificar el código del artículo en SAGE 50
  3. Si hay discrepancia, usar "Reasignar SKU" en Conecta HUB
  4. Re-sincronizar solo ese producto
```

### Error: "El pedido no se exportó a SAGE 50"
```
Causa:    NIF/CIF inválido, cliente duplicado, o línea de pedido sin SKU válido
Solución:
  1. Panel Conecta HUB → Pedidos → Ver pedidos fallidos
  2. Ver detalle del error en la línea del pedido
  3. Corregir manualmente el dato erróneo
  4. Usar "Reintentar exportación" en el pedido afectado
  5. Si el problema es recurrente con un cliente, verificar su ficha en SAGE 50
```

### Error: "Stock negativo en WooCommerce"
```
Causa:    Pedido procesado antes de que el stock se actualizara desde SAGE 50
Solución:
  1. Ajustar stock manualmente en SAGE 50
  2. Forzar sincronización inmediata: Conecta HUB → Sincronizar ahora
  3. Reducir intervalo de sincronización de stock a 5 minutos
```

### La sincronización se detiene sin errores visibles
```
Solución:
  1. Reiniciar la cola de sincronización en Conecta HUB
  2. Verificar que los cron jobs de WordPress funcionan:
     wp-cli: wp cron event run --due-now
  3. Revisar el log de PHP en el servidor (errores de memoria o timeout)
     VPS: /var/log/php/error.log
  4. Aumentar el timeout de PHP si es necesario:
     php.ini: max_execution_time = 300
```

---

## Contactos de soporte

| Servicio | Contacto |
|----------|----------|
| Conecta HUB | soporte@conectahub.es · Panel de soporte |
| SAGE 50 Cloud | soporte.sage50@sage.es · 902 123 456 |
| WooCommerce | [woocommerce.com/support](https://woocommerce.com) |
| Servidor VPS | Soporte Raiola/Dinahosting 24/7 |

---

*Documento: SAGE50-INTEGRACION.md · Dentix Productos Dentales, S.L. · v1.0 · Febrero 2026*
