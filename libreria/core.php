<?php
/**
 * Fichero core.php
 * 
 * Fichero núcleo del sistema,
 * se gestionan todas las definiciones.
 * 
 * @author Juan González <juanftp100@gmail.com>
 */

# Cargar el helper
require_once 'help/helps.php';

# Definicion de las rutas
define('APP_RUTA', RUTA_BASE . 'app/');
define('VISTA_RUTA', RUTA_BASE . 'view/');
define('LIBRERIA', RUTA_BASE . 'libreria/');
define('RUTA', APP_RUTA . 'rutas/');
define('MODELS', APP_RUTA . 'model/');
define('CONTROLLERS', APP_RUTA . 'controller/');

# Configuraciones
// Datos de la BD
require_once RUTA_BASE . 'config/config.php';

// Clase Conexion
require_once 'Conexion.php';

// Clase Redirecciona
require_once 'Redirecciona.php';

// Clase Session
require_once 'Session.php';

// Incluir todos los modelos
includeModels();

// Controladores
//require_once CONTROLLERS . "LoginController.php";

// Clase Vista
require_once 'Vista.php';

// Clase Ruta
include 'Ruta.php';

# Primera ejecucion, arranque del sistema
// Ejecuta la instancia de la clase Ruta
include RUTA . 'rutas.php';


