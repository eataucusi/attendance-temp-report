<?php

/**
 * Archivo core/Input.php
 * 
 * @copyright (c) 2020
 * @author Edison Ataucusi Romero <eataucusis@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/ Licencia Creative Commons Atribución-CompartirIgual 4.0 Internacional
 */

/**
 * Valida y sanea los datos recibidos por get o pots
 */
class Input {

    /** @var array  Copia de datos originales */
    public static $_backup;

    /** @var array Datos validados */
    private static $_validated;

    /** @var array Errores de validación */
    private static $_errors;

    /**
     * Obtiene todas las variables enviadas
     */
    public static function get_inputs() {
        if (!is_array(self::$_backup)) {
            $methods = ['get' => INPUT_GET, 'post' => INPUT_POST];
            foreach ($methods as $method_key => $method_value) {
                $copy = filter_input_array($method_value);
                if (!is_array($copy)) {
                    continue;
                }
                foreach ($copy as $key => $value) {
                    self::$_backup[$method_key][$key] = trim($value);
                }
            }
        }
    }

    /**
     * Verifica si una variable ha sido enviado por get o post
     * @param string $name
     * @param string $method Método de envío: post o get
     * @return bool
     */
    public static function exist(string $name, string $method = 'post'): bool {
        if (!isset(self::$_backup[$method][$name])) {
            return false;
        }
        return true;
    }

    /**
     * Verifica si el formulario fue enviado
     * @param string $method
     * @return bool
     */
    public static function form_was_sent(string $method = 'post'): bool {
        if (isset(self::$_backup[$method])) {
            return true;
        }
        return false;
    }

    /**
     * Obtine valor recibido para imprimir en formulario
     * @param string $name
     * @param string $method
     * @return type
     */
    public static function value(string $name, string $method = 'post'): string {
        if (self::exist($name, $method)) {
            return self::$_backup[$method][$name];
        }
        return '';
    }

    /**
     * Valida el tipo de dato recibido por post o get
     * @param string $name Nombre de varaible a recibir
     * @param string $type Tipo de dato: int, float, bool, email, url
     * @param string $method Método de envío: get o post
     */
    public static function validate(string $name, string $type, string $method = 'post') {
        if (!self::exist($name, $method)) {
            self::set_error($name, 'El campo ' . $name . ' no existe.', $method);
        } else {
            $types = ['int' => FILTER_VALIDATE_INT, 'float' => FILTER_VALIDATE_FLOAT,
                'bool' => FILTER_VALIDATE_BOOLEAN, 'email' => FILTER_VALIDATE_EMAIL,
                'url' => FILTER_VALIDATE_URL];
            if (!key_exists($type, $types)) {
                self::set_error($name, 'Tipo de dato no soportado ' . $type, $method);
            } elseif (filter_var(self::$_backup[$method][$name], $types[$type])) {
                self::$_validated[$method][$name] = self::$_backup[$method][$name];
            } else {
                self::set_error($name, 'Este campo no es de tipo ' . $type, $method);
            }
        }
    }

    /**
     * Validar por expresión regular
     * @param string $name Nombre de varaible a recibir
     * @param string $pattern Expresión regular
     * @param string $message Mensaje de error en caso de error
     * @param string $method Método de envío: get o post
     */
    public static function validate_exp(string $name, string $pattern, string $message, string $method = 'post') {
        if (!self::exist($name, $method)) {
            self::set_error($name, 'El campo ' . $name . ' no existe.', $method);
        } elseif (filter_var(self::$_backup[$method][$name], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => $pattern]])) {
            self::$_validated[$method][$name] = self::$_backup[$method][$name];
        } else {
            self::set_error($name, $message, $method);
        }
    }

    /**
     * Valida como texto
     * @param string $name
     * @param int $min_length
     * @param int $max_length
     * @param type $method
     * @return bool
     */
    public static function validate_text(string $name, int $min_length = 0, int $max_length = 0, $method = 'post') {
        if (!self::exist($name, $method)) {
            self::set_error($name, 'El campo ' . $name . ' no existe.', $method);
        } else {
            $length = strlen(self::$_backup[$method][$name]);
            if ($max_length == 0) {
                if ($length < $min_length) {
                    self::set_error($name, 'Mínimo ' . $min_length . ' caracteres', $method);
                } else {
                    self::$_validated[$method][$name] = strip_tags(self::$_backup[$method][$name]);
                }
            } elseif ($min_length <= $length && $length <= $max_length) {
                self::$_validated[$method][$name] = strip_tags(self::$_backup[$method][$name]);
            } else {
                self::set_error($name, 'Mínimo ' . $min_length . ' y máximo ' . $max_length . ' caracteres', $method);
            }
        }
    }

    /**
     * Valida como texto alternativo
     * @param string $name
     * @param int $min_length
     * @param int $max_length
     * @param type $method
     */
    public static function validate_alt_text(string $name, int $min_length = 0, int $max_length = 0, $method = 'post') {
        if (!self::exist($name, $method)) {
            self::$_validated[$method][$name] = null;
        } elseif (self::$_backup[$method][$name] == '') {
            self::$_validated[$method][$name] = null;
        } else {
            self::validate_text($name, $min_length, $max_length, $method);
        }
    }

    /**
     * Valida como html
     * @param string $name
     * @param int $min_length
     * @param int $max_length
     * @param type $method
     */
    public static function validate_html(string $name, int $min_length = 0, int $max_length = 0, $method = 'post') {
        if (!self::exist($name, $method)) {
            self::set_error($name, 'El campo ' . $name . ' no existe.', $method);
        } else {
            $length = strlen(self::$_backup[$method][$name]);
            if ($max_length == 0) {
                if ($length < $min_length) {
                    self::set_error($name, 'Mínimo ' . $min_length . ' caracteres', $method);
                } else {
                    self::$_validated[$method][$name] = self::$_backup[$method][$name];
                }
            } elseif ($min_length <= $length && $length <= $max_length) {
                self::$_validated[$method][$name] = self::$_backup[$method][$name];
            } else {
                self::set_error($name, 'Mínimo ' . $min_length . ' y máximo ' . $max_length . ' caracteres', $method);
            }
        }
    }

    /**
     * Valida un dato alternativo recibido por post o get
     * @param string $name Nombre de varaible a recibir
     * @param string $type Tipo de dato: int, float, bool, email, url
     * @param string $method Método de envío: get o post
     * @return boolean
     */
    public static function validate_alt(string $name, string $type, string $method = 'post') {
        if (!self::exist($name, $method)) {
            self::$_validated[$method][$name] = null;
        } elseif (self::$_backup[$method][$name] == '') {
            self::$_validated[$method][$name] = null;
        } else {
            self::validate($name, $type, $method);
        }
    }

    /**
     * Valida una fecha y lo convierte a formato Y-m-d
     * @param string $name
     * @param string $method
     */
    public static function validate_date(string $name, string $method = 'post') {
        if (!self::exist($name, $method)) {
            self::set_error($name, 'El campo ' . $name . ' no existe.', $method);
        } else {
            $date = DateTime::createFromFormat('d-m-Y', self::$_backup[$method][$name]);
            if ($date) {
                self::$_validated[$method][$name] = $date->format('Y-m-d');
            } else {
                self::$_errors[$method][$name] = 'Fecha no válida, ejemplo de fecha 25-10-2019';
            }
        }
    }

    /**
     * Obtiene el valor validado
     * @param string $name
     * @param string $method
     * @return string
     */
    public static function get(string $name, string $method = 'post'): ?string {
        if (self::was_valid($name, $method)) {
            return self::$_validated[$method][$name];
        }
        throw new Exception('No valido');
    }

    /**
     * Verifica si u campo ya fue validado
     * @param string $name
     * @param string $method
     * @return bool
     */
    public static function was_valid(string $name, string $method = 'post'): bool {
        if (isset(self::$_validated[$method])) {
            if (key_exists($name, self::$_validated[$method])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica si hubo errores en la validación de un campo
     * @param string $name
     * @param string $method
     * @return boolean
     */
    public static function was_invalid(string $name, string $method = 'post'): bool {
        if (isset(self::$_errors[$method][$name])) {
            return true;
        }
        return false;
    }

    /**
     * Obtiene el mensaje de error para el campo indicado y elimina el mensaje
     * @param string $name
     * @param string $method
     * @return string
     */
    public static function get_error(string $name, string $method = 'post'): string {
        $_error = self::$_errors[$method][$name];
        unset(self::$_errors[$method][$name]);
        return $_error;
    }

    /**
     * Establece un error para un campo
     * @param string $name
     * @param string $message
     * @param string $method
     */
    public static function set_error(string $name, string $message, string $method = 'post') {
        self::$_errors[$method][$name] = $message;
    }

    /**
     * Establece un error general de formulario
     * @param string $message
     * @param string $method
     */
    public static function set_form_error(string $message, string $method = 'post') {
        self::set_error('_form_error_', $message, $method);
    }

    /**
     * Obtiene los errores del formulario
     * @param string $method
     * @return array
     */
    public static function get_form_errors(string $method = 'post'): array {
        return self::$_errors[$method];
    }

    /**
     * Obtiene el error general de formulario
     * @param string $method
     * @return string
     */
    public static function get_form_error(string $method = 'post'): string {
        if (self::was_invalid('_form_error_', $method)) {
            return self::$_errors[$method]['_form_error_'];
        }
        return '';
    }

    /**
     * Genera el token csrf
     * @param string $form_name
     * @return string
     */
    public static function get_token(string $form_name): string {
        $hash = hash_init('md5', HASH_HMAC, session_id());
        hash_update($hash, $form_name);
        return hash_final($hash);
    }

    /**
     * Verifica el token csrf de formulario
     * @param string $form_name
     * @param string $method
     */
    public static function verify_token(string $form_name, string $method = 'post') {
        if (!self::exist('_token', $method)) {
            self::set_form_error('El token no ha sido enviado.', $method);
        } elseif (!hash_equals(self::get_token($form_name), self::$_backup[$method]['_token'])) {
            self::set_form_error('Token csrf no válido.', $method);
        }
    }

    /**
     * ¿Existe errores en el formulario?
     * @param string $method
     * @return bool
     */
    public static function error_exist(string $method = 'post'): bool {
        if (isset(self::$_errors[$method]) && count(self::$_errors[$method])) {
            return true;
        }
        return false;
    }

    /**
     * Valida como contraseña
     * @param string $name
     * @param int $min_length
     * @param int $max_length
     * @param type $method
     */
    public static function validate_pass(string $name, int $min_length = 0, int $max_length = 0, $method = 'post') {
        self::validate_html($name, $min_length, $max_length, $method);
        if (self::was_valid($name, $method)) {
            self::$_validated[$method][$name] = password_hash(self::get($name, $method), PASSWORD_DEFAULT, ['cost' => 8]);
        }
    }

    /**
     * Verifica una contraseña
     * @param string $hash
     * @param string $name
     * @param int $min_length
     * @param int $max_length
     * @param string $method
     */
    public static function pass_verify(string $hash, string $name, int $min_length = 0, int $max_length = 0, string $method = 'post') {
        self::validate_html($name, $min_length, $max_length, $method);
        if (self::was_valid($name, $method)) {
            if (!password_verify(self::get($name, $method), $hash)) {
                self::set_error($name, 'La contraseña no coincide.', $method);
            }
        }
    }

    /**
     * Establecer datos al input
     * @param array $data
     * @param string $method
     */
    public static function set_data(array $data, string $method = 'post') {
        self::$_backup[$method] = $data;
    }

    /**
     * Obtiene el primer error
     * @param string $method
     * @return string
     */
    public static function first_error_value(string $method = 'post'): string {
        return array_values(self::$_errors[$method])[0];
    }

    /**
     * Obtiene el nombre del campo del primer error
     * @param string $method
     * @return string
     */
    public static function first_error_key(string $method = 'post'): string {
        return array_keys(self::$_errors[$method])[0];
    }

}
