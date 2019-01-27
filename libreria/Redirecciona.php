<?php

/**
 * Clase Redirecciona
 * 
 * Se definen métodos para redireccionar páginas.
 * Esa clase se requiere en el core del proyecto.
 *
 * @author Juan González <juanftp100@gmail.com>
 */
class Redirecciona {
   
    /**
     * Funcion redirect($rute)
     * 
     * Esta funcion limpia la url entrante, quitandole el index.php
     * Luego redirecciona a la url limpia de / barras
     * 
     * @param type $rute
     */    
    private function redirect($rute) {
        $urlprin = str_replace("index.php", "", $_SERVER["PHP_SELF"]);
        header("location:/" . trim($urlprin, "/") . "/" . trim($rute, "/"));
    }
    
    /**
     * Función estática to($url)
     * 
     * Redirecciona hacia algún lugar
     * to("hola/nuevo")
     * 
     * @static
     * 
     * @param type $url
     * @return \Redirecciona
     */    
    public static function to($url) {
        self::redirect($url);
        return new Redirecciona();
    }
    
    /**
     * Función withMessage($var, $value = null)
     * 
     * Redirecciona a algún lugar llevando datos en la 
     * variable de sesión.
     * 
     * @param type $var
     * @param type $value
     * @return \Redirecciona
     */
    public function withMessage($var, $value = null) {
        if(is_null($value)) {
            foreach($var as $clave => $valor) {
                $_SESSION[$clave] = $valor;
            }
        } else {
            $_SESSION[$var] = $value;
        }
        return new Redirecciona();
    }
}
