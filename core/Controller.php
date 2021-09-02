<?php

namespace NsCore;

/**
 * Archivo core/Controller.php
 * 
 * @copyright (c) 2020
 * @author Edison Ataucusi Romero <eataucusis@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/ Licencia Creative Commons Atribución-CompartirIgual 4.0 Internacional
 */

/**
 * Controlador base de la aplicación
 * 
 * Esta clase proporciona atributos y métodos que heredarán los controladores
 */
abstract class Controller {

    /** @var \NsCore\View Acceso a vista */
    protected $view;

    /**
     * Crea una instancia de la clase, se sobreescribirá en los hijos
     */
    abstract public static function get_instance();

    /**
     * Constructor no público para evitar instanciar a la clase, se sobreescribirá en los hijos
     */
    abstract protected function __construct();

    /**
     * Función principal del controlador, se sobreescribirá en los hijos
     */
    abstract public function index();

    /**
     * Importa y retorna la instancia de un modelo
     * @param type $model Nombre de modelo primera letra mayúscula
     * @return \NsCore\Model
     */
    protected function get_model($model) {
        $model_path = 'models/' . $model . '.php';
        if (!is_readable(APP_PATH . $model_path)) {
            \NsCore\Error::app('Controlador', 'Archivo de modelo no encontrado',
                    $model_path, 'Verifique el nombre de modelo y archivo', true);
        }
        require_once APP_PATH . $model_path;
        $class_name = "\\NsModels\\$model";
        if (!class_exists($class_name, false)) {
            \NsCore\Error::app('Controlador', 'Clase ' . $class_name . ' no definida',
                    $model_path, 'Verifique su clase y el espacio de nombres', true);
        }
        if (!method_exists($class_name, 'get_instance')) {
            \NsCore\Error::app('Controlador', 'Método estático get_instance no definido en',
                    $model_path, 'Verifique que el método existe y es estático', true);
        }
        return call_user_func([$class_name, 'get_instance']);
    }

    /**
     * Redirige a la página de inicio o a otra ruta
     * @param string $path
     */
    protected function redirect(?string $path = '') {
        header('location: ' . SITE_URL . $path);
        exit(0);
    }

    /**
     * Incluye una librería 
     * @param string $library
     */
    protected function get_lib(string $library) {
        $library_path = 'libs/' . $library . '.php';
        if (!is_readable(APP_PATH . $library_path)) {
            \NsCore\Error::app('Controlador', 'Archivo de librería no encontrado',
                    $library_path, 'Verifique el nombre de librería y el archivo', true);
        }
        require_once APP_PATH . $library_path;
    }

    /**
     * Muestra respuesta correcta a petición ajax
     * @param string $message
     * @param string $info
     */
    protected function ajax_response(string $message = 'La operación se realizó correctamente.', string $info = '') {
        \Session::set('message', $message);
        die('{"status":"success","message":"' . $message . '","info":"' . $info . '"}');
    }

    /**
     * Muestra error si no se envió un token para lapetición ajax
     * @param string $form_name
     * @param string $method
     */
    protected function ajax_error_no_token(string $form_name, string $method = 'post') {
        \Input::get_inputs();
        if (!\Input::form_was_sent($method)) {
            \NsCore\Error::app('Controlador', 'Datos no enviados', \NsCore\Route::$url, 'La petición AJAX requiere un token');
        }
        \Input::verify_token($form_name, $method);
        if (\Input::error_exist($method)) {
            \NsCore\Error::app('Controlador', \Input::get_form_error($method), \NsCore\Route::$url, 'La petición AJAX requiere un token');
        }
    }

}
