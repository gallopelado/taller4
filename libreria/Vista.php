<?php
namespace vista;

class Vista {

    public static function crear($path, $key = null, $value = null) {
        // Esta vacio el $path
        if($path != ''){
            // Convertir en un array separados por puntos
            // view.ciudad a array([0]=>view, [1]=>ciudad)
            $paths = explode('.', $path);
            $ruta = '';

            // Recorrer el array $paths
            for($i = 0; $i < count($paths); $i++) {
                // Comprobar si es el ultimo
                if($i == count($paths)-1) {
                    $ruta .= $paths[$i] . '.php';
                } else {
                    $ruta .= $paths[$i] . '/';
                }
            }

            // Comprobar si existe el archivo
            if(file_exists(VISTA_RUTA . $ruta)) {
                // Comprobar si existe el key
                if(!is_null($key)) {
                    // Es un array ?
                    if(is_array($key)) {
                        // extrae los key y convierte a variables
                        extract($key, EXTR_PREFIX_SAME, '');
                    } else {
                        ${$key} = $value;
                    }
                }

                // Ahora se incluye
                include VISTA_RUTA . $ruta;
            } else {
                die('No existe la vista');
            }
        }
        return null;
    }

}