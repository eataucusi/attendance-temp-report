<?php

namespace NsModels;

/**
 * Archivo models/Sync.php
 * 
 * @copyright (c) 2020
 * @author Edison Ataucusi Romero <eataucusis@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/ Licencia Creative Commons Atribución-CompartirIgual 4.0 Internacional
 */

/**
 * Gestiona la sincronización.
 */

class Sync extends \NsCore\Model {

    /** @var  \NsModels\Sync Instancia única de la clase */
    private static $_instance;

    /**
     * Constructor privado
     */
    protected function __construct() {
        $this->db = \NsCore\DB::get_instance();
    }

    /**
     * Crea una única instancia de la clase
     * @return \NsModels\Sync
     */
    public static function get_instance(): \NsModels\Sync {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 
     * @param string $user_name
     * @param string $user_login
     * @param string $user_email
     * @param string $user_pass
     */

    /**
     * Inserta una sincronización
     * @param string $date Fecha y hora actual
     * @param string $name Nombre de archivo
     * @return string Identificador insertado
     */
    public function insert(string $date, string $name): string {
        $this->db->exec('INSERT INTO sync VALUES(NULL, ?, ?, NULL)', [$date, $name]);
        return $this->db->last_inserted();
    }

    public function set_current(string $id) {
        $this->db->exec('UPDATE sync SET current=NULL WHERE current=?', ['Si']);
        $this->db->exec('UPDATE sync SET current=? WHERE id=?', ['Si', $id]);
    }

    public function get_current(): string {
        return $this->db->scalar('SELECT name FROM sync WHERE current=?', ['Si']);
    }

    public function list() {
        return $this->db->all('SELECT * FROM sync ORDER BY id DESC');
    }

    public function get_by_id(string $id): array {
        return $this->db->row('SELECT * FROM sync WHERE id=?', [$id]);
    }

}
