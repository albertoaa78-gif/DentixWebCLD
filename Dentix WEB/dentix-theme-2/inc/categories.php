<?php
/**
 * Dentix — Configuración centralizada de categorías / especialidades
 *
 * ESTE ES EL ÚNICO SITIO donde se definen las especialidades del catálogo.
 * Todo el tema (nav, homepage, filtros) las lee desde aquí dinámicamente.
 *
 * CÓMO AÑADIR, MODIFICAR O ELIMINAR UNA CATEGORÍA:
 *   1. Ve a wp-admin → Productos → Categorías (gestión visual con imagen y descripción)
 *   2. Si quieres cambiar el orden o el icono del menú/homepage, edita este archivo.
 *
 * CAMPOS:
 *   slug  → debe coincidir exactamente con el slug de WooCommerce
 *   label → nombre visible en el nav y en la homepage
 *   icon  → emoji o SVG inline para la homepage
 *   color → color de fondo suave para la tarjeta de la homepage
 *   desc  → descripción corta para la tarjeta de la homepage
 */

defined('ABSPATH') || exit;

function dentix_get_categories(): array {
    return [
        [
            'slug'  => 'cirugia',
            'label' => 'Cirugía',
            'icon'  => '🔬',
            'color' => '#F0EEED',
            'desc'  => 'Instrumental quirúrgico, bisturíes, fórceps y material de cirugía oral',
        ],
        [
            'slug'  => 'diagnostico',
            'label' => 'Diagnóstico',
            'icon'  => '🦷',
            'color' => '#EDF2F7',
            'desc'  => 'Exploradores, espejos, sondas y equipos de diagnóstico clínico',
        ],
        [
            'slug'  => 'periodoncia',
            'label' => 'Periodoncia',
            'icon'  => '⚕️',
            'color' => '#F0F4F0',
            'desc'  => 'Curetas, raspadores y material de tratamiento periodontal',
        ],
        [
            'slug'  => 'restauradora',
            'label' => 'Restauradora',
            'icon'  => '✨',
            'color' => '#FDF8F0',
            'desc'  => 'Composite, cementos, adhesivos y material de restauración directa',
        ],
        [
            'slug'  => 'implantologia',
            'label' => 'Implantología',
            'icon'  => '🏥',
            'color' => '#F0F0F7',
            'desc'  => 'Implantes, componentes protésicos y material de regeneración ósea',
        ],
        [
            'slug'  => 'ortodoncia',
            'label' => 'Ortodoncia',
            'icon'  => '📐',
            'color' => '#F7F0F0',
            'desc'  => 'Brackets, arcos, ligaduras y material de ortodoncia fija y removible',
        ],
        [
            'slug'  => 'laboratorio',
            'label' => 'Laboratorio',
            'icon'  => '🧪',
            'color' => '#F0F7F4',
            'desc'  => 'Materiales de prótesis, escayolas, ceras y equipos de laboratorio dental',
        ],
        [
            'slug'  => 'accesorios',
            'label' => 'Accesorios',
            'icon'  => '📦',
            'color' => '#F5F0EC',
            'desc'  => 'Guantes, mascarillas, baberos y accesorios clínicos desechables',
        ],
    ];
}

/**
 * Devuelve una categoría por slug.
 */
function dentix_get_category(string $slug): ?array {
    foreach (dentix_get_categories() as $cat) {
        if ($cat['slug'] === $slug) return $cat;
    }
    return null;
}

/**
 * Devuelve el objeto WooCommerce term de una categoría.
 * Combina la config del tema con los datos reales de WooCommerce.
 */
function dentix_get_wc_categories(): array {
    $config = dentix_get_categories();
    $result = [];

    foreach ($config as $cat) {
        $term = get_term_by('slug', $cat['slug'], 'product_cat');
        if ($term && ! is_wp_error($term)) {
            $cat['term_id'] = $term->term_id;
            $cat['count']   = $term->count;
            $cat['url']     = get_term_link($term);
            $thumbnail_id   = get_term_meta($term->term_id, 'thumbnail_id', true);
            $cat['image']   = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'medium') : '';
        } else {
            $cat['term_id'] = 0;
            $cat['count']   = 0;
            $cat['url']     = home_url('/tienda/?cat=' . $cat['slug']);
            $cat['image']   = '';
        }
        $result[] = $cat;
    }

    return $result;
}
