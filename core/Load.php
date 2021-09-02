<?php

 /**
 * Archivo core/Load.php
 * 
 * Archivo que incluye a todos los archivos del sistema 
 * @copyright (c) 2020
 * @author Edison Ataucusi Romero <eataucusis@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/ Licencia Creative Commons Atribución-CompartirIgual 4.0 Internacional
 */

/** Inclusión de config */
if (!is_readable(APP_PATH . 'config.php')) {
    die('Archivo del sistema no encontrado: config.php');
}
require APP_PATH . 'config.php';

/** Inclusión de Input */
if (!is_readable(APP_PATH . 'core/Helper.php')) {
    die('Archivo del sistema no encontrado: core/Helper.php');
}
require APP_PATH . 'core/Helper.php';

/** Inclusión de Input */
if (!is_readable(APP_PATH . 'core/Input.php')) {
    die('Archivo del sistema no encontrado: core/Input.php');
}
require APP_PATH . 'core/Input.php';

/** Inclusión de DB */
if (!is_readable(APP_PATH . 'core/DB.php')) {
    die('Archivo del sistema no encontrado: core/DB.php');
}
require APP_PATH . 'core/DB.php';

/** Inclusión de Model */
if (!is_readable(APP_PATH . 'core/Model.php')) {
    die('Archivo del sistema no encontrado: core/Model.php');
}
require APP_PATH . 'core/Model.php';

/** Inclusión de View */
if (!is_readable(APP_PATH . 'core/View.php')) {
    die('Archivo del sistema no encontrado: core/View.php');
}
require APP_PATH . 'core/View.php';

/** Inclusión de Controller */
if (!is_readable(APP_PATH . 'core/Controller.php')) {
    die('Archivo del sistema no encontrado: core/Controller.php');
}
require APP_PATH . 'core/Controller.php';

/** Inclusión de Error */
if (!is_readable(APP_PATH . 'core/Error.php')) {
    die('Archivo del sistema no encontrado: core/Error.php');
}
require APP_PATH . 'core/Error.php';

/** Inclusión de Session */
if (!is_readable(APP_PATH . 'core/Session.php')) {
    die('Archivo del sistema no encontrado: core/Session.php');
}
require APP_PATH . 'core/Session.php';

/** Inclusión de Route */
if (!is_readable(APP_PATH . 'core/Route.php')) {
    die('Archivo del sistema no encontrado: core/Route.php');
}
require APP_PATH . 'core/Route.php';

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
} else {
    ini_set("display_errors", 0);
}

\Session::start();
\NsCore\Route::resolve();
