<?php

namespace NsCore;

 /**
 * Archivo core/Route.php
 * 
 * @copyright (c) 2020
 * @author Edison Ataucusi Romero <eataucusis@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/ Licencia Creative Commons Atribución-CompartirIgual 4.0 Internacional
 */

/**
 * Analiza y ejecuta una url o petición
 * 
 * Esta clase que recupera, valida y resuelve las peticiones
 */
class Route {

    /** @var string Nombre de controlador */
    public static $controller;

    /** @var string Nombre de método */
    public static $method;

    /** @var array Argumentos */
    public static $args;

    /** @var string Url de la pertición */
    public static $url;

    /**
     * Extrae el controlador, método y argumentos de una ruta
     */
    public static function resolve() {
        self::$url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL);
        if (self::$url) {
            $_aux = array_filter(explode('/', self::$url));
            self::$controller = strtolower(array_shift($_aux));
            self::$method = count($_aux) ? strtolower(array_shift($_aux)) : '';
            self::$args = count($_aux) ? $_aux : [];
        } else {
            self::$controller = '';
            self::$method = '';
            self::$args = [];
        }        
        self::resolve_file(self::$controller, self::$method, self::$args);
    }

    /**
     * Verifica el controlador y metodo, para luego ejecutarlos
     * @param string $controller
     * @param string $method
     * @param array $args
     */
    private static function resolve_file(string $controller = '', string $method = '', array $args = []) {
        $controller = $controller ?: 'main';
        $method = $method ?: 'index';
        $controller_name = ucfirst($controller);
        $file_path = 'controllers/' . $controller_name . '.php';
        if (!is_readable(APP_PATH . $file_path)) {
            \NsCore\Error::app('Gestor de rutas', 'Archivo de controlador no encontrado', $file_path, 'Verifique la ruta de acceso y el controlador');
        }
        require_once APP_PATH . $file_path;
        $class_name = "\\NsControllers\\$controller_name";
        if (!class_exists($class_name, false)) {
            \NsCore\Error::app('Gestor de rutas', 'Clase ' . $class_name . ' no definida', $file_path, 'Verifique su clase y el espacio de nombres');
        }
        if (!method_exists($class_name, 'get_instance')) {
            \NsCore\Error::app('Gestor de rutas', 'Método estático get_instance no definido en', $file_path, 'Verifique que el método existe y es estático');
        }
        if (!method_exists($class_name, $method)) {
            \NsCore\Error::app('Gestor de rutas', 'Método ' . $method . ' no existe en', $file_path, 'Verifique el método');
        }
        $ctlr = call_user_func([$class_name, 'get_instance']);
        call_user_func_array([$ctlr, $method], $args);
    }

}
