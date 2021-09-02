<?php

namespace NsCore;

/**
 * Archivo core/Error.php
 * 
 * @copyright (c) 2020
 * @author Edison Ataucusi Romero <eataucusis@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/ Licencia Creative Commons Atribución-CompartirIgual 4.0 Internacional
 */

/**
 * Gestiona los errores
 * 
 * Permite establecer y mostrar errores para poder solucionarlos
 */
class Error {

    /**
     * Establecer errores de aplicación
     * @param string $place Lugar en el que se produce el error
     * @param string $message Mensaje
     * @param string $file Archivo
     * @param string $info Información para corregir
     */
    public static function app(string $place, string $message, string $file, string $info) {
        http_response_code(404);
        self::show($place, $message, $file, $info);
    }

    /**
     * Muestra un error
     * @param string $place Lugar en el que se produce el error
     * @param string $message Mensaje
     * @param string $code Palabra clave del error
     * @param string $info Información para corregir
     */
    private static function show(string $place, string $message, string $code = '', string $info = '') {
        $debug_path = 'core/debug.phtml';
        if (is_readable(APP_PATH . $debug_path)) {
            require APP_PATH . $debug_path;
            exit(0);
        } else {
            die($message);
        }
    }

    /**
     * Establecer errores de base de datos
     * @param string $message Mensaje de error
     * @param string $code Código del error
     * @param string $sql Sentencia SQL
     */
    public static function db(string $message, string $code = '', string $sql = '') {
        http_response_code(404);
        if (!DEBUG_MODE) {
            self::show('Gestor de base de datos', 'Se ha producido un error al acceder a base de datos');
        } else {
            $_error = [
                '2002' => 'Debe verificar el servidor de base de datos en su configuración',
                '2005' => 'Debe verificar el servidor de base de datos en su configuración',
                '1044' => 'Debe verificar el nombre de usuario para la base de datos en su configuración',
                '1045' => 'Debe verificar la contraseña para el acceso a base de datos en su configuración',
                '1049' => 'Debe verificar el nombre de base de datos en su configuración',
                '42000' => 'Verifique su consulta',
                '42S02' => 'Verifique su consulta, tabla inexistente',
                '42S22' => 'Verifique su consulta, columna de tabla inexistente',
                '21S01' => 'Verifique su consulta, no hay correspondencia en número de columnas',
                '23000' => 'Intentó duplicar una clave única',
                'HY093' => 'El número de parámetros de sustitución debe corresponder con el tamaño de parámetros'
            ];
            $info = key_exists($code, $_error) ? $_error[$code] : '';
            self::show('Gestor de base de datos', $message, $sql, $info);
        }
    }

    /**
     * Mensaje de error para peticiones ajax
     * @param string $place Lugar en el que se produce el error
     * @param string $message Mensaje
     * @param string $file Archivo
     * @param string $info Información para corregir
     */
    public static function ajax(string $place, string $message, string $info) {
        if (DEBUG_MODE) {
            die('{"status":"error","message":"' . $message . '","place":"' . $place . '","file":"' . \NsCore\Route::$url . '","info":"' . $info . '"}');
        } else {
            die('{"status":"error","message":"' . $message . '","place":"' . $place . '"}');
        }
    }

}
