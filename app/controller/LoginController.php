<?php
/**
 * Clase LoginController
 * 
 * En esta clase se realizan las operaciones
 * de control de logueos
 * 
 * @author Juan González <juanftp100@gmail.com>
 */
use vista\Vista;
class LoginController {
    
    # Atributos
    /**
     * Datos recuperados del usuario, puede ser util para
     * crear sesiones
     */
    public $dataUser = null;
    
    // Nombre de usuario a validar
    public $username;
    
    // Password a validar
    public $password;
    
    // IP conexion user
    public $ip;
    
    // Numero de intentos antes de denegar el acceso
    public $attempt = 3;
    
    // Numero de intentos antes de bloquear al usuario
    public $attempBan = 4;
    
    // Tiempo para los intentos en segundos
    public $attemp_time = 60;
    
    // Fecha actual
    public $now;
    
    // Fecha actual menos $attemp_time
    public $nowDiff;
    
    // mensaje del resultado del acceso
    public $msg;
    
    // Nombre del navegador
    public $browser;
    
    // Versión del navegador
    public $browser_version;
    
    // Plataforma o SO
    public $platform;
    
    // Tipo de computador
    public $device_type;
    
    // Es un dispositivo movil?
    public $isMobile;
    
    // Es una tablet?
    public $isTablet;
    
    /**
     * Método inicializar
     * 
     * Metodo para iniciar variables
     * 
     */
    public function inicializar() {
        
        // Zona horaria
        date_default_timezone_set('America/Asuncion');
        
        // Establecemos la IP
        $this->ip = $_SERVER["REMOTE_ADDR"];
        
        // Crear objeto DateTime()
        $time = new DateTime();
        
        // Establecemos la fecha actual
        $this->now = $time->format("Y-m-d H:i:s");
        
        // Restamos 60 segundos(1 minuto) a la fecha actual
        $time->modify("-" . $this->attemp_time . " seconds");
        
        // Establecemos la fecha actual menos (intentos * tiempo por intento)
        $this->nowDiff = $time->format("Y-m-d H:i:s");
        
        # Datos del huesped
        // Obtener el user-agent
        $user_agent = $_SERVER["HTTP_USER_AGENT"];
        $datos = get_browser($user_agent);
        
        // Obtenemos el nombre del navegador y version
        $this->browser = $datos->browser;
        $this->browser_version = $datos->version;
        
        // Obtenemos la plataforma
        $this->platform = $datos->platform;
        
        // Obtenemos el tipo de computadora
        $this->device_type = $datos->device_type;
        
        // Obtenemos si es un dispositivo movil
        $this->isMobile = !empty($datos->ismobiledevice) ? "SI" : "NO";
        
        // Obtenemos si es una tablet
        $this->isTablet = !empty($datos->istablet) ? "SI" : "NO";
        
    }
    
    /**
     * Metodo para validar
     * 
     * Metodo que realiza las validaciones
     * 
     */
    public function validate($username, $password) {
        // inicializar()
        $this->inicializar();

        // Establecemos usuario y password
        $this->username = $username;
        $this->password = $password;
        
        // Buscamos el usuario en la base de datos
        $resultado = LoginModel::buscarNick($this->username);        
        if(is_array($resultado)) {
            // Guardamos los datos del usuario
            $this->dataUser = $resultado;
            // Comprobamos si esta desactivado
            if($resultado["status"] != 1) {
                /*
                 * Si esta desactivado, registramos en login-log
                 * el intento y retornamos false
                 */
                $this->msg = "Usuario bloqueado";
                // $username, $fecha, $msg, $ip, $browser, $browser_version, $platform, $device_type, $isMobile, $isTablet
                LoginModel::loginLog($this->username, $this->now,  $this->msg, $this->ip, $this->browser, $this->browser_version, $this->platform, $this->device_type, $this->isMobile, $this->isTablet);
                return false;
            }
            
            // Comprobamos si el password es correcto
            if(LoginModel::validarLogin($this->username, $this->password)) {
                // Actualizamos el ultimo inicio de sesion correcto
                LoginModel::loginSuccess($resultado["usu_id"]);
                // Comprobamos los intentos
                return $this->attempt();                                
            } else {
                /* Si el password no es correcto, registramos en el log
                 * y retornamos false
                 */
                $this->msg = "Error de password";
                LoginModel::loginLog($this->username, $this->now,  $this->msg, $this->ip, $this->browser, $this->browser_version, $this->platform, $this->device_type, $this->isMobile, $this->isTablet);
                return false;
                
            }
        } else {
            /* 
             * Si no se encontró el usuario, 
             * registramos el log y retornamos false
             */
            $this->msg = "Error de usuario";            
            LoginModel::loginLog($this->username, $this->now, $this->msg, $this->ip, $this->browser, $this->browser_version, $this->platform, $this->device_type, $this->isMobile, $this->isTablet);
            return false;
        }
    }
    
    /**
     * Metodo attempt
     * 
     * Se encarga de comprobar los pasos
     * 
     * @return boolean
     */
    public function attempt() {
        // Recuperamos del log los intentos desde la fecha nowDiff
        $resultado = LoginModel::contarIntentos($this->username, $this->nowDiff);
        // Comprobar si se recuperaron los registros
        if($resultado->rowCount() > 0) {
            $rows = $resultado->fetch(PDO::FETCH_ASSOC);
            /*
             * Comprobamos si el numero de registros es menor al
             * numero de intentos
             */
            if($this->attempt >= $rows["n"]) {
                /* Si es menor a los intentos registramos
                 * el acceso y devolvemos true
                 */
                $this->msg = "Exito";
                LoginModel::loginLog($this->username, $this->now, $this->msg, $this->ip, $this->browser, $this->browser_version, $this->platform, $this->device_type, $this->isMobile, $this->isTablet);
                return true;
            // Comprobamos si es mayor a attempBan    
            } elseif ($this->attempBan < $rows["n"]) {
                // registramos el acceso
                $this->msg = "Se ha baneado el usuario";
                LoginModel::loginLog($this->username, $this->now,  $this->msg, $this->ip, $this->browser, $this->browser_version, $this->platform, $this->device_type, $this->isMobile, $this->isTablet);
                // Bloqueamos el usuario
                LoginModel::userBan($this->username);
                // Devolvemos false
                return false;
            } else {
                // En cualquier caso registramos el acceso y retornamos false
                $this->msg = "Login Bloqueado durante " . $this->attemp_time . " seg."; 
                LoginModel::loginLog($this->username, $this->now, "Login Bloqueado durante " . $this->msg . " seg.", $this->ip, $this->browser, $this->browser_version, $this->platform, $this->device_type, $this->isMobile, $this->isTablet);
                return false;
            }
        }
        // Si nunca se ha logueado registramos el acceso
        $this->msg = "Success";
        LoginModel::loginLog($this->username, $this->now, "Success", $this->ip, $this->browser, $this->browser_version, $this->platform, $this->device_type, $this->isMobile, $this->isTablet);
        // Devolvemos true
        return true;
    }
    
    /**
     * Método msgErrorCsrf
     * 
     * Se encarga de inicializar variables,
     * registrar datos importantes antes de cargar la sesión, 
     * genera un mensaje para la validación CSRF
     * 
     * @param type $username
     */
    public function msgErrorCsrf($username) {
        $this->username = $username;
        $this->inicializar();
        LoginModel::loginLog($this->username, $this->now, "Error CSRF", $this->ip, $this->browser, $this->browser_version, $this->platform, $this->device_type, $this->isMobile, $this->isTablet);
        redirecciona()->to("/login")->withMessage(array(
            "estado" => "false",
            "mensaje" => "Error en el formulario"
        ));
    }

    # Metodos Probados ########################################################
    /**
     * Metodo index
     * 
     * Es el primer metodo que se ejecuta al 
     * iniciar el sistema, carga la vista
     * auth_nick
     * 
     * @return vista auth_nick
     */
    public function index() {
        return Vista::crear('auth.auth_nick');
    }

    /**
     * Metodo ingresar
     * 
     * Luego de haber ingresado el usuario y la clave,
     * se procesan esos datos aqui.
     * 
     */
    public function ingresar() {        
        if(val_csrf()) {
            $usuario = $_SESSION["user-true"];            
            $password = input('password');            
            $resultado_validar = $this->validate($usuario, $password);
            if($resultado_validar) {
//                echo "<pre>";
//                print_r($this->dataUser);
//                echo "</pre>";
                // En este lugar se crea la sesion !!!               
                $this->procesar_sesion($usuario);
                echo $this->msg . "\n";                
            } else {
                redirecciona()->to("/login")->withMessage(array(
                    "estado"=>"false",
                    "mensaje"=>$this->msg
                ));
            }
        } else {
            $usuario = $_SESSION["user-true"];
            $this->msgErrorCsrf($usuario);  
        }
    }    

    /**
     * Método first
     * 
     * Luego de validar el formulario
     * de usuario se redirecciona al
     * formulario de password con el método
     * second
     * 
     */
    public function first() {        
        if(val_csrf()) {
            $user = input('usuario');            
            /*
             * La entrada de usuario no debe estar vacia o nula
             */
            if(!empty($user)){
                // Validar si existe el usuario en la bd
                // user-log se guarda temporalmente
                // user-true se utiliza para enviar
                    
                $_SESSION["user-log"] = input('usuario');
                $_SESSION["user-true"] = input('usuario');                                
                
                //print_r($_SESSION);
                redirecciona()->to("/login/second");
                
            } else {                
                redirecciona()->to("/login")->withMessage(array(
                    "estado"=>"false",
                    "mensaje"=>"El campo usuario no debe estar vacío"
                ));
            }
                        
        } else {
            $user = input('usuario');
            $this->msgErrorCsrf($user);
        }
    }
    
    /**
     * Método second
     * 
     * Se comprueba la variable de sesión 'user-log'
     * para redireccionar al formulario de password.
     * 
     * @return mix vista_password or vista_login
     */
    public function second() {
        if(!empty($_SESSION["user-log"])){            
            $_SESSION["user-log"] = "";
            return Vista::crear('auth.auth_password');
        } else {
            session_destroy();            
            redirecciona()->to("/login");
        }        
    }
    
    /**
     * Metodo procesar_sesion
     * 
     * Genera una nueva sesion por usuario,
     * redirecciona a ls vista /admin.
     * 
     * @param type $usuario
     */
    public function procesar_sesion($usuario) {
        // Destruir las variables sesion anteriores 
        destruir_sesion();        
        // Generar una nueva sesion
        session_start();
        $_SESSION["nick"] = $usuario;
        redirecciona()->to("/admin");    
    }  
    
    public static function elegirGrupo() {
        redirecciona()->to("login/vistaElegirGrupo");
    }
    
    public function vistaElegirGrupo() {
        return Vista::crear("auth.auth_grupo");
    }
    
       
}