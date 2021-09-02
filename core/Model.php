<?php

namespace NsCore;

 /**
 * Archivo core/Model.php
 * 
 * @copyright (c) 2020
 * @author Edison Ataucusi Romero <eataucusis@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/ Licencia Creative Commons Atribución-CompartirIgual 4.0 Internacional
 */

/**
 * Modelo base de la aplicación
 * 
 * Clase que permite hacer consultas a base de datos, los modelos heredarán de ésta
 */
abstract class Model {

    /** @var \NsCore\DB Manejador de base de datos */
    protected $db;

    /**
     * Constructor no público para evitar instanciar a la clase, se sobreescribirá en los hijos
     */
    abstract protected function __construct();

    /**
     * Crea una instancia de la clase, se sobreescribirá en los hijos
     */
    abstract static public function get_instance();
}
