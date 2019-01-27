<?php

/**
 * Fichero rutas.php
 * 
 * Se crea la instancia de la clase Ruta, 
 * revisa la sesión y según resultado carga
 * los controladores.
 * 
 * @see Ruta
 * @author Juan González <juanftp100@gmail.com>
 */
use vista\Vista;
// Crea instancia de la clase Ruta
$ruta = new Ruta();
$listaArray = array();

// 1 -  Verificar si existe la sesion
if (isset($_SESSION["nick"])) {
    // Obtener controladores
    //print_r($_SESSION);
    $nroGrupos = LoginModel::contarGrupos($_SESSION["nick"]);
    # Los usuarios que solamente esten en 1 grupo
    if ($nroGrupos->n == 1) {
        $rol = LoginModel::verificaRol($_SESSION["nick"], "ADMINISTRADOR");
        if (!empty($rol->grupo) && $rol->grupo == "ADMINISTRADOR") {
            $data = LoginModel::controladorPorGrupo($_SESSION["nick"], $rol->grupo);
            $ruta->controladores($data);
            // No es administrador
        } else {
            $ro = LoginModel::verificaRol($_SESSION["nick"], "DISTINTO");
            $data = LoginModel::controladorPorGrupo($_SESSION["nick"], $ro->grupo);
            $ruta->controladores($data);
        }
    } else {
        // Cuando el user tiene mas de 1 grupo
//        destruir_sesion();
//        echo "Nro de grupos $nroGrupos->n , entonces hay que elegir";
//        $controladores = array(            
//            "/login" => "LoginController"
//        );
        //Asignar los controladores
        //$ruta->controladores($controladores);
        //LoginController::elegirGrupo();
        
        return Vista::crear("auth.auth_grupo");
    }
} else {
    $controladores = array(
        "/" => "BienvenidoController",
        "/login" => "LoginController",
    );
    //Asignar los controladores
    $ruta->controladores($controladores);
}

