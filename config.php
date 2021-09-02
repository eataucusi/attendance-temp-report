<?php

 /**
 * Archivo config.php
 * 
 * @copyright (c) 2020
 * @author Edison Ataucusi Romero <eataucusis@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/ Licencia Creative Commons Atribución-CompartirIgual 4.0 Internacional
 */

/**
 * Configuración básica
 * 
 * Este archivo contiene configuraciones para el acceso a base de datos MYSQL,
 * palabras secretas y modo de ejecución.
 */
/**
 * Configuración de la aplicación
 */

/** Url base de la aplicación */
const SITE_URL = 'https://edisonat.com/';

/** Url base de la aplicación */
const SITE_NAME = 'Control de acceso';

/** Identificador de aplicación */
const APP_ID = 'wen';

/** Modo depuración, por defecto FALSE */
const DEBUG_MODE = true;

/** Minutos en el que expira la sesión */
const EXPIRY_TIME = 150;

/** Número de registros por página */
const RECORDS_PAGE = 10;

/**
 * Configuración para el acceso a base de datos
 */

/** Nombre de la base de datos */
const DB_NAME = 'ac';

/** Usuario de la base de datos */
const DB_USER = 'root';

/** Contraseña de la base de datos */
const DB_PASSWORD = '';

/** Servidor de la base de datos */
const DB_HOST = 'localhost';


