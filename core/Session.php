<?php

 /**
 * Archivo core/Session.php
 * 
 * @copyright (c) 2020
 * @author Edison Ataucusi Romero <eataucusis@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/ Licencia Creative Commons Atribución-CompartirIgual 4.0 Internacional
 */

/**
 * Gestiona las sesiones
 * 
 * Permite almacenar y recuperar informacion de sesiones
 */
class Session {

    /**
     * Inicia la sesión
     */
    public static function start() {
        session_start();
        self::time();
    }

    /**
     * Destruye la sesión
     */
    public static function destroy() {
        unset($_SESSION);
        session_destroy();
    }

    /**
     * Guardar un item en la sesión
     * @param string $name
     * @param string $value
     */
    public static function set(string $name, string $value) {
        $_SESSION[APP_ID . '_' . $name] = $value;
    }

    /**
     * Obtiene un item de la sesión
     * @param string $name
     * @return string
     */
    public static function get(string $name): string {
        return $_SESSION[APP_ID . '_' . $name];
    }

    /**
     * Verifica si existe un item en la sesión
     * @param string $name
     * @return bool
     */
    public static function exist(string $name): bool {
        if (isset($_SESSION[APP_ID . '_' . $name])) {
            return true;
        }
        return false;
    }

    /**
     * Elimina un item de la sesión
     * @param string $name
     */
    public static function remove(string $name) {
        unset($_SESSION[APP_ID . '_' . $name]);
    }

    /**
     * Obtiene y elimina un item de la sesión
     * @param string $name
     * @return string
     */
    public static function get_remove(string $name): string {
        $value = self::get($name);
        self::remove($name);
        return $value;
    }

    /**
     * Regenerar la sesión
     */
    public static function regenerate() {
        session_regenerate_id(true);
        self::set('_time', time());
    }

    /**
     * Verifica si el tiempo de sesión ha expirado
     */
    public static function time() {
        if (self::exist('_time')) {
            if (time() - self::get('_time') > EXPIRY_TIME * 60) {
                self::destroy();
                \NsCore\Error::app('Sesión', 'La sesión ha expirado.', EXPIRY_TIME . ' minutos sin interactuar.', 'Vuelva a iniciar sesión.');
            }
            self::set('_time', time());
        }
    }

    /**
     * Redirigir a una ruta si no existe variable en la sesión
     * @param string $name Nombre de variable
     * @param string $path Ruta a redirigir
     */
    public static function access_control(string $name, string $path) {
        if (!self::exist($name)) {
            $params = \NsCore\Route::$url ? '?require&origin=' : '';
            header('location:' . SITE_URL . $path . $params . \NsCore\Route::$url);
            exit(0);
        }
    }

    /**
     * Mostrar error ajax si no existe una variabkle en la sesión
     * @param string $name
     */
    public static function ajax_access_control(string $name) {
        if (!self::exist($name)) {
            \NsCore\Error::ajax('Sesión', 'Acceso denegado', 'Necesita iniciar sesión para acceder');
        }
    }

}
