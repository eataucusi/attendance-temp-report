<?php

namespace NsControllers;

/**
 * Archivo controllers/Account.php
 * 
 * @copyright (c) 2020
 * @author Edison Ataucusi Romero <eataucusis@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/ Licencia Creative Commons Atribución-CompartirIgual 4.0 Internacional
 */

/**
 * Controlador del la gestion de cuentas
 */
class Account extends \NsCore\Controller {

    /** @var  \NsControllers\Account Instancia única de la clase */
    private static $_instance;

    /**
     * Constructor privado
     */
    protected function __construct() {
        $this->view = \NsCore\View::get_instance();
    }

    /**
     * Crea una única instancia de la clase
     * @return \NsControllers\Account
     */
    public static function get_instance(): \NsControllers\Account {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Método principal
     */
    public function index() {
        if (\Session::exist('user_id')) {
            $this->redirect();
        }
        \Input::get_inputs();
        if (\Input::form_was_sent()) {
            \Input::verify_token('account-login');
            \Input::validate('user_email', 'email');
            \Input::validate_pass('user_pass', 8);
            if (!\Input::error_exist()) {
                /* @var $account \NsModels\Account */
                $account = $this->get_model('Account');
                $user = $account->get_by_email(\Input::get('user_email'));
                $this->_pass_verify($user);
            }
        }
        $this->view->title = 'Acceder';
        $this->view->render_part('account/login');
    }

    /**
     * Verificar contraseña
     * @param array $user
     */
    private function _pass_verify(array $user = []) {
        if (count($user)) {
            \Input::pass_verify($user['user_pass'], 'user_pass', 8);
            if (!\Input::error_exist()) {
                \Session::regenerate();
                \Session::set('user_id', $user['user_id']);
                \Session::set('user_login', $user['user_login']);
                \Input::validate_alt_text('origin', 0, 0, 'get');
                $this->redirect(\Input::get('origin', 'get'));
            }
        } else {
            \Input::set_error('user_email', 'Email incorrecto.');
        }
    }

    /**
     * Salir del sistema
     */
    public function logout() {
        if (!\Session::exist('user_id')) {
            $this->redirect('account');
        }
        \Session::destroy();
        $this->redirect('account?logout');
    }

    /**
     * Informacion del usuario
     */
    public function info() {
        \Session::access_control('user_id', 'account');
        /* @var $account \NsModels\Account */
        $account = $this->get_model('Account');
        \Input::get_inputs();
        if (\Input::form_was_sent()) {
            \Input::verify_token('account-info');
            \Input::validate_text('user_name', 10, 150);
            \Input::validate_text('user_login', 5, 60);
            \Input::validate('user_email', 'email');
            if (!\Input::error_exist()) {
                $account->update(\Session::get('user_id'), \Input::get('user_name'), \Input::get('user_login'), \Input::get('user_email'));
                \Session::set('message', 'La operación se realizó correctamente.');
            }
        } else {
            \Input::set_data($account->get_by_id(\Session::get('user_id')));
        }
        $this->view->title = 'Mis datos';
        $this->view->render('account/info');
    }

    /**
     * Cambiar contraseña de usuario
     */
    public function password() {
        \Session::access_control('user_id', 'account');
        /* @var $account \NsModels\Account */
        $account = $this->get_model('Account');
        $user = $account->get_by_id(\Session::get('user_id'));
        \Input::get_inputs();
        if (\Input::form_was_sent()) {
            \Input::verify_token('account-password');
            \Input::pass_verify($user['user_pass'], 'user_pass', 8);
            \Input::validate_pass('new_pass', 8);
            if (!\Input::error_exist()) {
                \Input::pass_verify(\Input::get('new_pass'), 're_pass', 8);
                if (!\Input::error_exist()) {
                    $account->update_pass(\Session::get('user_id'), \Input::get('new_pass'));
                    \Session::set('message', 'La operación se realizó correctamente.');
                }
            }
        }
        $this->view->title = 'Mi contraseña';
        $this->view->render('account/password');
    }

}
