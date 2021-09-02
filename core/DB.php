<?php

namespace NsCore;

/**
 * Archivo core/DB.php
 * 
 * @copyright (c) 2020
 * @author Edison Ataucusi Romero <eataucusis@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/ Licencia Creative Commons Atribución-CompartirIgual 4.0 Internacional
 */

/**
 * Gestiona las conexiones a base de datos
 * 
 * Esta clase recupera información de las conexiones y resultados de las
 * consultas a base de datos, clase implementada con el patrón de diseño singleton
 */
class DB {

    /** @var array Instancia única de la clase */
    private static $_instance;

    /** @var \PDO Manejador de base de datos */
    private $_dbh;

    /** @var \PDOStatement Sentencia preparada */
    private $_stmt;

    /**
     * Crea una instancia de PDO 
     */
    private function __construct() {
        try {
            $this->_dbh = new \PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST . ';charset=utf8', DB_USER, DB_PASSWORD);
            $this->_dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\Exception $ex) {
            \NsCore\Error::db($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * Crea una única instnacia de la clase
     * @return \NsCore\DB Instancia única de la clase
     */
    public static function get_instance(string $dsn = 'default'): \NsCore\DB {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Prepara y ejecuta una sentencia
     * @param string $sql SQL con parámetros de sustitución (?)
     * @param array $args Arreglo para sustituir cada parámetro de sustitución
     */
    private function _query(string $sql, array $args = []) {
        $this->_stmt = $this->_dbh->prepare($sql);
        $this->execute($args);
    }

    /**
     * Recupera un arreglo asociativo de la primera y única fila
     * @param string $sql SQL con parámetros de sustitución (?)
     * @param array $args Arreglo para sustituir cada parámetro de sustitución
     * @return array
     */
    public function row(string $sql, array $args = []): array {
        $this->_query($sql . ' LIMIT 1', $args);
        $result = $this->_stmt->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) ? $result[0] : $result;
    }

    /**
     * Recupera el primer y único valor
     * @param string $sql SQL con parámetros de sustitución (?)
     * @param array $args Arreglo para sustituir cada parámetro de sustitución
     * @return string
     */
    public function scalar(string $sql, array $args = []): string {
        $this->_query($sql . ' LIMIT 1', $args);
        $result = $this->_stmt->fetchAll(\PDO::FETCH_NUM);
        return count($result) ? $result[0][0] : '';
    }

    /**
     * Recupera un arreglo asociativo de muchas filas
     * @param string $sql SQL con parámetros de sustitución (?)
     * @param array $args Arreglo para sustituir cada parámetro de sustitución
     * @return array
     */
    public function all(string $sql, array $args = []): array {
        $this->_query($sql, $args);
        return $this->_stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Ejecuta consulta que no recupera valoreS: INSERT|UPDATE|DELETE
     * @param string $sql SQL con parámetros de sustitución (?)
     * @param array $args Arreglo para sustituir cada parámetro de sustitución
     */
    public function exec(string $sql, array $args = []) {
        $this->_query($sql, $args);
    }

    /**
     * Id insertado por la última sentencia
     * @return string
     */
    public function last_inserted(): string {
        return $this->_dbh->lastInsertId();
    }

    /**
     * Número de filas afectadas por la última sentencia
     * @return int
     */
    public function affected_rows(): int {
        return $this->_stmt->rowCount();
    }

    /** Inicia una transacción */
    public function transac_begin() {
        $this->_dbh->beginTransaction();
    }

    /** Confirma una transacción */
    public function transac_commit() {
        $this->_dbh->commit();
    }

    /** Revierte una transacción */
    public function transac_rollback() {
        $this->_dbh->rollBack();
    }

    /**
     * Ejecuta una sentencia preparada
     * @param array $args Arreglo para sustituir cada parámetro de sustitución
     */
    public function execute(array $args = []) {
        try {
            $this->_stmt->execute($args);
        } catch (\Exception $ex) {
            if ($this->_dbh->inTransaction()) {
                $this->transac_rollback();
            }
            \NsCore\Error::db($ex->getMessage(), $ex->getCode(), $this->_stmt->queryString);
        }
    }

}
