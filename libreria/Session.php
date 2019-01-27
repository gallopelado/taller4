<?php

/**
 * Clase Session
 * 
 * Metodos simples para obtener mensajes
 * a través de sesiones.
 *
 * @author Juan González <juanftp100@gmail.com>
 */
class Session {
    
    /**
     * Metodo has
     * 
     * Permite validar si existe una sesion.
     * 
     * @param type $variable_session
     * @return boolean
     */
    public static function has($variable_session) {
        if(isset($_SESSION[$variable_session])) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Metodo get
     * 
     * Obtiene la variable de sesion.
     * 
     * @param type $variable_session
     * @return type
     */
    public static function get($variable_session) {
        try {
            $mensaje = $_SESSION[$variable_session];
            unset($_SESSION[$variable_session]);
            return $mensaje;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
        }
}
