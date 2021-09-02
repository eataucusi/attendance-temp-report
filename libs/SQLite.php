<?php

/**
 * Archivo libs/SQLite.php
 * 
 * @copyright (c) 2020
 * @author Edison Ataucusi Romero <eataucusis@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/ Licencia Creative Commons Atribución-CompartirIgual 4.0 Internacional
 */

/**
 * Librería de conexión a SQLite.
 * 
 * Permite la administración de base de datos SQLite. 
 */

class SQLite {

    /** @var \PDO */
    private $_dbh;

    /** @var \PDOStatement */
    private $_stmt;

    public function __construct(string $path) {
        try {
            $this->_dbh = new \PDO('sqlite:' . APP_PATH . 'uploads/' . $path);
            $this->_dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\Exception $ex) {
            \NsCore\Error::db($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * Ejecuta una consulta.
     * 
     * @param string $sql Consulta SQL
     * @param array $args Arreglo de datos para reemplazar cada ? de la consulta.
     */
    public function exec(string $sql, array $args = []) {
        $this->_stmt = $this->_dbh->prepare($sql);
        $this->_stmt->execute($args);
    }

    public function query(string $sql, array $args = []): array {
        $this->exec($sql, $args);
        return $this->_stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function get_users(): array {
        return $this->query('SELECT * FROM users');
    }

    public function daily(string $date): array {
        $sql = 'SELECT min(events.hour*60+events.min) AS ingreso, users.dni, users.name, events.* ' .
                'FROM events INNER JOIN users ON events.user_id=users.id WHERE events.date=? ' .
                'GROUP BY events.user_id ORDER BY ingreso DESC';
        return $this->query($sql, [$date]);
    }

    public function users(): array {
        return $this->query('SELECT id, dni, name FROM users WHERE id>1 ORDER BY name');
    }

    public function period(string $id, string $date1, string $date2): array {
        $sql = 'SELECT date FROM events WHERE user_id=? AND date BETWEEN ? AND ? GROUP BY date';
        return $this->query($sql, [$id, $date1, $date2]);
    }

    public function temp(string $user_id): array {
        $sql = 'SELECT min(hour*60+min) AS ingreso,date, temp FROM events WHERE user_id=? GROUP BY date ORDER BY date';
        return $this->query($sql, [$user_id]);
    }

    public function acum_correct(): string {
        $res = $this->query('SELECT COUNT(id) AS total FROM events WHERE temp IS NOT NULL AND temp <= 38');
        return $res[0]['total'];
    }

    public function acum_temperature(): string {
        $res = $this->query('SELECT COUNT(id) AS total FROM events WHERE temp IS NOT NULL AND temp > 38');
        return $res[0]['total'];
    }

    public function acum_unknow(): string {
        $res = $this->query('SELECT COUNT(id) AS total FROM events WHERE user_id IS NULL AND gauge = 1');
        return $res[0]['total'];
    }

    public function acum_mask(): string {
        $res = $this->query('SELECT COUNT(id) AS total FROM events WHERE user_id IS NULL AND gauge = 0');
        return $res[0]['total'];
    }

    public function alert(): array {
        return $this->query('SELECT events.temp, events.date, users.id, users.name FROM events INNER JOIN users ON events.user_id=users.id WHERE temp > 38 ORDER BY events.id DESC');
    }

}
