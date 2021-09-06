<?php

namespace NsControllers;

/**
 * Archivo controllers/Main.php
 * 
 * @copyright (c) 2020
 * @author Edison Ataucusi Romero <eataucusis@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/ Licencia Creative Commons Atribución-CompartirIgual 4.0 Internacional
 */


/**
 * Controlador principal de la aplicación
 */
class Main extends \NsCore\Controller {

    /** @var  \NsControllers\Main Instancia única de la clase */
    private static $_instance;

    /**
     * Constructor privado
     */
    protected function __construct() {
        $this->view = \NsCore\View::get_instance();
    }

    /**
     * Crea una única instancia de la clase
     * @return \NsControllers\Main
     */
    public static function get_instance(): \NsControllers\Main {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Método principal
     */
    public function index() {
        \Session::access_control('user_id', 'account');
        /* @var $sync \NsModels\Sync */
        $sync = $this->get_model('Sync');
        $this->get_lib('SQLite');
        $lite = new \SQLite($sync->get_current());
        $data = ['correct' => $lite->acum_correct(),
            'temperature' => $lite->acum_temperature(),
            'unknow' => $lite->acum_unknow(), 'mask' => $lite->acum_mask()];
        $this->view->title = 'Control de acceso y temperatura';
        $this->view->render('main/index', $data);
    }

    public function syncs() {
        \Session::access_control('user_id', 'account');
        /* @var $sync \NsModels\Sync */
        $sync = $this->get_model('Sync');
        $this->view->render('main/syncs', ['data' => $sync->list()]);
    }

    public function current(string $id = '') {
        \Session::access_control('user_id', 'account');
        /* @var $sync \NsModels\Sync */
        $sync = $this->get_model('Sync');
        $data = $sync->get_by_id($id);
        if (!$data) {
            $this->redirect();
        }
        \Input::get_inputs();
        if (\Input::form_was_sent('get')) {
            \Input::verify_token('index-current', 'get');
            if (!\Input::error_exist('get')) {
                $sync->set_current($id);
                \Session::set('message', 'La operación se realizó correctamente.');
                $this->redirect('main/index');
            }
        }
    }

    public function sync() {
        \Input::get_inputs();
        if (\Input::form_was_sent()) {
            $this->_upload();
        } else {
            \NsCore\Error::app('Sincronización', 'Error de formulario.', 'controllers/Main.php', 'Datos no enviados.');
        }
    }

    private function _upload() {
        if (isset($_FILES['db']['size'])) {
            if ('.db' == substr($_FILES['db']['name'], strrpos($_FILES['db']['name'], '.'))) {
                $this->_move();
            } else {
                \NsCore\Error::app('Sincronización', 'Error de formulario.', 'controllers/Main.php', 'Archivo db no válido.');
            }
        } else {
            \NsCore\Error::app('Sincronización', 'Error de formulario.', 'controllers/Main.php', 'Archivo db no enviado.');
        }
    }

    private function _move() {
        $name = 'db_' . uniqid() . '.db';
        if (move_uploaded_file($_FILES['db']['tmp_name'], APP_PATH . 'uploads/' . $name)) {
            /* @var $sync \NsModels\Sync */
            $sync = $this->get_model('Sync');
            $fecha = date('Y-m-d H:i:s');
            $id = $sync->insert($fecha, $name);
            $this->get_lib('SQLite');
            $lite = new \SQLite($name);
            if ($lite->get_users()) {
                $sync->set_current($id);
                $this->ajax_response();
            }
        } else {
            \NsCore\Error::ajax('Sincronización', 'Error al subir archivo.', 'No se pudo mover el archivo.');
        }
    }

    public function report() {
        \Session::access_control('user_id', 'account');
        /* @var $sync \NsModels\Sync */
        $sync = $this->get_model('Sync');
        $this->get_lib('SQLite');
        $lite = new \SQLite($sync->get_current());
        $this->view->set_js('tabs', false);
        $this->view->set_css('datepicker.min');
        $this->view->set_js('datepicker');
        $this->view->render('main/report', ['users' => $lite->users(), 'alert' => $lite->alert()]);
    }

    public function daily(string $date = '', string $time = '') {
        \Session::ajax_access_control('user_id');
        $date1 = \DateTime::createFromFormat('d-m-Y', $date);
        if ($date1) {
            /* @var $sync \NsModels\Sync */
            $sync = $this->get_model('Sync');
            $this->get_lib('SQLite');
            $lite = new \SQLite($sync->get_current());
            $this->view->render_part('main/daily', ['date' => $date, 'data' => $lite->daily($date1->format('Y-m-d')), 'time' => $time]);
        } else {
            \NsCore\Error::ajax('Asistencia diaria', 'Fecha no válida', 'Verifique la fecha');
        }
    }

    public function period(string $inicio = '', string $final = '') {
        \Session::ajax_access_control('user_id');
        $date1 = \DateTime::createFromFormat('d-m-Y', $inicio);
        $date2 = \DateTime::createFromFormat('d-m-Y', $final);
        $diff = $date2->diff($date1);
        if ($date1 and $date2) {
            /* @var $sync \NsModels\Sync */
            $sync = $this->get_model('Sync');
            $this->get_lib('SQLite');
            $lite = new \SQLite($sync->get_current());
            $users = $lite->users();
            $asis = [];
            $_date1 = $date1->format('Y-m-d');
            $_date2 = $date2->format('Y-m-d');
            for ($i = 0; $i < count($users); $i++) {
                $asis[] = $lite->period($users[$i]['id'], $_date1, $_date2);
            }
            $data = ['users' => $users, 'asis' => $asis, 'period' => $inicio . ' A ' . $final, 'date' => $date1, 'days' => $diff->days + 1];
            $this->view->render_part('main/period', $data);
        } else {
            \NsCore\Error::ajax('Asistencia diaria', 'Fecha no válida', 'Verifique la fecha');
        }
    }

    public function temp(string $user_id = '') {
        \Session::ajax_access_control('user_id');
        /* @var $sync \NsModels\Sync */
        $sync = $this->get_model('Sync');
        $this->get_lib('SQLite');
        $lite = new \SQLite($sync->get_current());
        $this->view->render_part('main/temp', ['data' => $lite->temp($user_id)]);
    }

}
