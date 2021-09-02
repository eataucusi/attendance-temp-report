<?php

namespace NsModels;

/**
 * Archivo models/Account.php
 * 
 * @copyright (c) 2020
 * @author Edison Ataucusi Romero <eataucusis@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/ Licencia Creative Commons Atribución-CompartirIgual 4.0 Internacional
 */

 /**
  * Modelo para gestionar las cuentas.
  */

class Account extends \NsCore\Model {

    /** @var  \NsModels\Account Instancia única de la clase */
    private static $_instance;

    /**
     * Constructor privado
     */
    protected function __construct() {
        $this->db = \NsCore\DB::get_instance();
    }

    /**
     * Crea una única instancia de la clase
     * @return \NsModels\Account
     */
    public static function get_instance(): \NsModels\Account {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Inserta un nuevo usuario
     * @param string $user_name
     * @param string $user_login
     * @param string $user_email
     * @param string $user_pass
     */
    public function insert(string $user_name, string $user_login, string $user_email, string $user_pass) {
        $this->db->exec('INSERT INTO users VALUES(NULL,?,?,?,?)', [$user_name, $user_login, $user_email, $user_pass]);
    }

    /**
     * Obtiene los datos de un usuario a partir del email
     * @param string $user_email
     * @return array
     */
    public function get_by_email(string $user_email): array {
        return $this->db->row('SELECT * FROM users WHERE user_email = ?', [$user_email]);
    }

    /**
     * Obtiene los datos de u usuario apartir de su id
     * @param string $user_id
     * @return array
     */
    public function get_by_id(string $user_id): array {
        return $this->db->row('SELECT * FROM users WHERE user_id=?', [$user_id]);
    }

    /**
     * Actualizar datos datos de usuario
     * @param string $user_id
     * @param string $user_name
     * @param string $user_login
     * @param string $user_email
     */
    public function update(string $user_id, string $user_name, string $user_login, string $user_email) {
        $this->db->exec('UPDATE users SET user_name=?, user_login=?, user_email=? WHERE user_id=?',
                [$user_name, $user_login, $user_email, $user_id]);
    }

    /**
     * Actrualizar conttraseña
     * @param string $user_id
     * @param string $user_pass
     */
    public function update_pass(string $user_id, string $user_pass) {
        $this->db->exec('UPDATE users SET user_pass=? WHERE user_id=?', [$user_pass, $user_id]);
    }

}
