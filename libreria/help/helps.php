<?php
/**
 * Fichero helps
 * 
 * Sirve para registrar funciones
 * específicas.
 * 
 * @author Juan González <juanftp100@gmail.com>
 */

/**
 * Función includeModels
 * 
 * Carga en memoria todos los
 * ficheros de modelos del directorio
 * /model
 * 
 */
function includeModels() {
    $directorio = opendir(MODELS);
    while ($archivo = readdir($directorio)) {
        if (!is_dir($archivo)) {
            require_once MODELS . $archivo;
        }
    }
}

/**
 * Función asset
 * 
 * Imprime la ruta del directorio assets
 * 
 * @param type $asset
 */
function asset($asset) {
    $urlprin = trim(str_replace("index.php", "", $_SERVER["PHP_SELF"]), "/");
    echo "/" . $urlprin . "/assets/" . $asset;
}

/**
 * Función redireccionar
 * 
 * Imprime la ruta sin el
 * index.php
 * 
 * @param string $rute
 */
function redireccionar($rute) {
    $urlprin = str_replace("index.php", "", $_SERVER["PHP_SELF"]);
    echo $urlprin;
}

/**
 * Función url
 * 
 * Imprime la ruta sin el index.php pero con las 
 * barras(/) añadidas
 * 
 * @param string $rute
 */
function url($rute) {
    $urlprin = str_replace("index.php", "", $_SERVER["PHP_SELF"]);
    echo "/" . trim($urlprin, "/") . "/" . $rute;
}

# Se inicia la sesión
session_start();

/**
 * Función csrf_token
 * 
 * Limpia la variable de sesión _token,
 * genera un código aleatorio en la variable de sesión 
 * csrf_token, imprime.
 * 
 */
function csrf_token() {
    if (isset($_SESSION["_token"])) {
        unset($_SESSION["_token"]);
    }
    $csrf_token = md5(uniqid(mt_rand(), true));
    $_SESSION["csrf_token"] = $csrf_token;
    echo $csrf_token;
}

/**
 * Función val_csrf
 * 
 * Verifica las variables de sesión _token y
 * csrf_token que sean iguales.
 * 
 * @see csrf_token()
 * @return boolean
 */
function val_csrf() {
    if ($_REQUEST["_token"] == $_SESSION["csrf_token"]) {
        return true;
    } else {
        return false;
    }
}

/**
 * Función input
 * 
 * Recupera datos desde la petición(GET o POST)
 * y limpia.
 * 
 * @param string $campo
 * @return string $campo
 */
function input($campo) {
    $campo = $_REQUEST[$campo];
    $campo = trim($campo);
    $campo = stripcslashes($campo);
    $campo = htmlspecialchars($campo);
    return $campo;
}

/*
 * funcion que permite cifrar un string
 */

// Primera manera de encriptar 
// function encriptar($string) {    
//     return crypt($string, '$2a$07$usesomesillystringforsalt$');
// }

// Segunda manera de encriptar
// function cifrar($user_pass) {
//     $salt = md5($user_pass);
//     $password_cifrado = crypt($user_pass, $salt);
//     return $password_cifrado;
// }

// Tercera forma de encriptar(Recomendada)
// Retorna un hash del password
/**
 * Función cifrar
 * 
 * Genera un hash desde el password del usuario.
 * 
 * @param string $user_pass
 * @return string $passHash
 */
function cifrar($user_pass) {
    $passHash = password_hash($user_pass, PASSWORD_BCRYPT);
    return $passHash;
}

/**
 * Función verifica_cifrar
 * 
 * Verifica el password del usuario con su hash.
 * 
 * @param string $user_pass
 * @param string $pHash
 * @return boolean
 */
function verifica_cifrar($user_pass, $pHash) {
    if(password_verify($user_pass, $pHash)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Función redirecciona
 * 
 * Retorna una nueva instancia 
 * de la Clase Redirecciona
 * 
 * @see Redirecciona
 * @return \Redirecciona
 */
function redirecciona() {
    return new Redirecciona();
}

/**
 * Función json_response
 * 
 * Permite retornar json a partir de un array.
 * 
 * @param array $data
 * @return json $data
 */
function json_response($data) {
    header('Content-Type: application/json');
    if (is_array($data)) {
        $array = array();
        foreach ($data as $d) {
            array_push($array, $d->getColumnas());
        }
        return json_encode($array);
    } else {
        return json_encode($data->getColumnas());
    }
}

/**
 * Funcion destruir_sesion
 * 
 * Destruye la sesion actual libera recursos.
 * 
 */
function destruir_sesion() {
    session_destroy();
    unset($_SESSION);
}

/**
 * Funcion trim_value
 * 
 * Elimina la coma del ultimo elemento de un
 * array
 * 
 * @param type $value
 */
function trim_value(&$value) {
    $value = trim($value, ",");
}