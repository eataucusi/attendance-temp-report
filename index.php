<?php

/**
 * Archivo index.php
 * 
 * Archivo principal de la parte pública de la aplicación
 * 
 * @copyright (c) 2020
 * @author Edison Ataucusi Romero <eataucusis@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/ Licencia Creative Commons Atribución-CompartirIgual 4.0 Internacional
 */
/** Ruta raiz de la aplicación */
define('APP_PATH', realpath(__DIR__) . '/');

if (!is_readable(APP_PATH . 'core/Load.php')) {
    die('Archivo del sistema no encontrado: core/Load.php');
}
/** Inclusión de loader */
require APP_PATH . 'core/Load.php';


