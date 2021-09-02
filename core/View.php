<?php

namespace NsCore;

 /**
 * Archivo core/View.php
 * 
 * @copyright (c) 2020
 * @author Edison Ataucusi Romero <eataucusis@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/ Licencia Creative Commons Atribución-CompartirIgual 4.0 Internacional
 */

/**
 * Gestiona las vistas
 */
class View {

    /** @var \NsCore\View */
    private static $_instance;

    /** @var string Directorio relativo de la plantilla */
    public $theme_dir;

    /** @var array Arreglo de archivos js del head */
    public $js_head;

    /** @var array Arreglo de archivos js del body */
    public $js_body;

    /** @var array Arreglo de archivos css */
    public $css;

    /** @var string Título de la página */
    public $title;

    /** @var string Paginación */
    public $pagination;

    /** @var string Contenido central */
    public $content;

    /**
     * Constructor privado para asegurar una sola instancia
     */
    private function __construct() {
        $this->title = '';
        $this->pagination = '';
        $this->content = '';
        $this->theme_dir = 'views/_theme/';
        $this->css = [];
        $this->js_head = [];
        $this->js_body = [];
    }

    /**
     * Crea una única instancia de la clase
     * @return \NsCore\View
     */
    public static function get_instance(): \NsCore\View {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Renderiza a una vista de la carpeta views e incluye el archivo general de plantillas
     * @param string $view
     * @param array $data
     */
    public function render(string $view, array $data = []) {
        if (!is_readable(APP_PATH . $this->theme_dir . 'template.phtml')) {
            \NsCore\Error::app('Gestor de vistas', 'La plantilla no existe', $this->theme_dir . 'template.phtml', 'Verifique el archivo', true);
        }
        $this->content = $this->get_render($view, $data);
        require_once APP_PATH . $this->theme_dir . 'template.phtml';
    }

    /**
     * Renderiza a una vista de la carpeta views y retorna el html generado
     * @param string $view
     * @param array $data
     * @return string
     */
    public function get_render(string $view, array $data = []): string {
        $view_path = 'views/' . $view . '.phtml';
        if (!is_readable(APP_PATH . $view_path)) {
            \NsCore\Error::app('Gestor de vistas', 'La vista no existe', $view_path, 'Verifique la vista', true);
        }
        extract($data);
        ob_start();
        require APP_PATH . $view_path;
        return ob_get_clean();
    }

    /**
     * Renderiza a una vista de la carpeta views
     * @param string $view
     * @param array $data
     */
    public function render_part(string $view, array $data = []) {
        echo $this->get_render($view, $data);
    }

    /**
     * Establece javascript para la plantilla
     * @param string $js
     * @param bool $head True coloca en el head y false antes de cerrar el body
     */
    public function set_js(string $js, bool $head = true) {
        $js_path = $this->theme_dir . 'js/' . $js . '.js';
        if (!is_readable(APP_PATH . $js_path)) {
            \NsCore\Error::app('Gestor de vistas', 'El archivo js no existe', $js_path, 'Verifique el archivo', true);
        }
        if ($head) {
            $this->js_head[] = $js_path;
        } else {
            $this->js_body[] = $js_path;
        }
    }

    /**
     * Establece hoja de estilos para la plantilla
     * @param string $css
     */
    public function set_css(string $css) {
        $css_path = $this->theme_dir . 'css/' . $css . '.css';
        if (!is_readable(APP_PATH . $css_path)) {
            \NsCore\Error::app('Gestor de vistas', 'El archivo css no existe', $css_path, 'Verifique el archivo', true);
        }
        $this->css[] = $css_path;
    }

    /**
     * Genera el paginado
     * @param int $number_pages Número de páginas
     * @param string $route URL de paginado
     * @param int $current Página actual
     * @param string $after_route argumentos despues de la ruta
     */
    public function paginate(int $number_pages, string $route, int $current = 1, string $after_route = '') {
        $paged_path = $this->theme_dir . 'pagination.phtml';
        if (!is_readable(APP_PATH . $paged_path)) {
            $this->pagination = 'El archivo de paginación ' . $paged_path . ' no existe, restaure el archivo';
        } else {
            ob_start();
            require_once APP_PATH . $paged_path;
            $this->pagination = ob_get_clean();
        }
    }

}
