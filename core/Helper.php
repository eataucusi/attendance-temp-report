<?php

/**
 * Archivo core/Helper.php
 * 
 * @copyright (c) 2020
 * @author Edison Ataucusi Romero <eataucusis@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/ Licencia Creative Commons Atribución-CompartirIgual 4.0 Internacional
 */

/**
 * Poporciona juego de funciones para realizar tareas habituales
 */
class Helper {

    /**
     * Texto amigable para url
     * @param string $text
     * @param int $largo
     * @return string
     */
    public static function clean(string $text, int $largo): string {
        $text = str_replace(['¿', '¡'], '', $text);
        $text = str_replace([' ', '&'], '-', $text);
        $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace_callback('/\&(.)[^;]*;/', function($txt) {
            return preg_match('/[^aeiounAEIOUN]/', $txt[1]) ? '' : $txt[1];
        }, $text);
        $text = preg_replace('/[^a-z0-9-]/', '', strtolower($text));
        if (strlen($text) > $largo) {
            return substr($text, 0, $largo);
        }
        return $text;
    }

    /**
     * Verifiva si una variable es de tipo entero
     * @param string $var
     * @return bool
     */
    public static function is_int(string $var): bool {
        if (filter_var($var, FILTER_VALIDATE_INT)) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Copia una archivo o carpeta completa
     * @param string $source
     * @param string $target
     */
    public static function copy(string $source, string $target) {
        if (is_dir($source)) {
            @mkdir($target);
            $d = dir($source);
            while (FALSE !== ( $entry = $d->read() )) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
                $new_entry = $source . '/' . $entry;
                if (is_dir($new_entry)) {
                    self::copy($new_entry, $target . '/' . $entry);
                    continue;
                }
                copy($new_entry, $target . '/' . $entry);
            }
            $d->close();
        } else {
            copy($source, $target);
        }
    }

    /**
     * Convierte segundos a minutos
     * @param string $seconds
     * @return string
     */
    public static function minutos(string $seconds): string {
        $_resto = $seconds % 60;
        return ($seconds - $_resto) / 60 . ':' . (strlen($_resto) == 1 ? '0' . $_resto : $_resto);
    }

    public static function date_format(string $string, string $from = 'd-m-Y', string $to = 'Y-m-d'): string {
        $date = DateTime::createFromFormat($from, $string);
        return $date->format($to);
    }

}
