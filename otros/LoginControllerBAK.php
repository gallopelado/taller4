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
    /*
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
    public $attempt = 5;
    
    // Numero de intentos antes de bloquear al usuario
    public $attempBan = 10;
    
    // Tiempo para los intentos en segundos
    public $attemp_time = 60;
    
    // Fecha actual
    public $now;
    
    // Fecha actual menos $attemp_time
    public $nowDiff;
    
    // mensaje del resultado del acceso
    public $msg;
    
    /**
     * Metodo para iniciar variables
     * 
     * @author Juan González <juanftp100@gmail.com>
     */
    public function inicializar() {
        
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
        
    }
    
    /**
     * Metodo para validar
     * 
     * Metodo que realiza las validaciones
     */
    public function validate($username, $password) {
        // inicializar()
        $this->inicializar();

        // Establecemos usuario y password
        $this->username = $username;
        $this->password = $password;
        
        // Buscamos el usuario en la base de datos
        $resultado = LoginModel::buscarNick($this->username);
        if($resultado) {
            // Guardamos los datos del usuario
            $this->dataUser = $resultado;
            // Comprobamos si esta desactivado
            if($resultado["status"] != 1) {
                /*
                 * Si esta desactivado, registramos en login-log
                 * el intento y retornamos false
                 */
                LoginModel::loginLog($this->username, $this->now, "Usuario bloqueado", $this->ip);
                return false;
            }
            
            // Comprobamos si el password es correcto
            if(LoginModel::validarLogin($username, $password)) {
                // Actualizamos el ultimo inicio de sesion correcto
                LoginModel::loginSuccess($resultado["usu_id"]);
                // Comprobamos los intentos
                
            }
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
                LoginModel::loginLog($this->username, $this->now, "Exito", $this->ip);
                return true;
            // Comprobamos si es mayor a attempBan    
            } elseif ($this->attempBan < $rows["n"]) {
                // registramos el acceso
                LoginModel::loginLog($this->username, $this->now, "Se ha baneado el usuario", $this->ip);
                // Bloqueamos el usuario
                LoginModel::userBan($this->username);
                // Devolvemos false
                return false;
            } else {
                // En cualquier caso registramos el acceso y retornamos false
                LoginModel::loginLog($this->username, $this->now, "Login Bloqueado durante " . $this->attemp_time . " seg.", $this->ip);
                return false;
            }
        }
        // Si nunca se ha logueado registramos el acceso
        LoginModel::loginLog($this->username, $this->now, "Success", $this->ip);
        // Devolvemos true
        return true;
    }
        
    
    # Metodos Probados ########################################################
    public function index() {
        return Vista::crear('auth.auth_nick');
    }

    public function ingresar() {        
        if(val_csrf()) {
            $usuario = $_SESSION["user-true"];            
            $password = input('password');            
            $estado_login = LoginModel::validarLogin($usuario, $password);

            // Registrar lo que ingresa el usuario para validar
            //$_SESSION['user'] = $usuario;           
                                    
            if($estado_login) {
                //redireccionar al inicio
                echo "<h1>Logueo Exitoso</h1>\n";
                echo "Usuario: $usuario<br>";
                echo "Password: $password<br>";
                session_destroy();
            } else {
                // Volver a analizar                
                            
            }

        } else {
            echo '<h1>Login Error !</h1>';            
        }
    }    

    public function first() {
        if(val_csrf()) {
            $user = input('usuario');
            $estado_login = LoginModel::buscarNick($user);
            /*
             * La entrada de usuario no debe estar vacia o nula,
             * debe existir en la base de datos
             */
            if(!empty($user)){
                // Validar si existe el usuario en la bd
                // user-log se guarda temporalmente
                // user-true se utiliza para enviar
                    
                $_SESSION["user-log"] = input('usuario');
                $_SESSION["user-true"] = input('usuario');                
                if($estado_login) {                    
                    // Setear en 0 la variable intentos
                    if(isset($_SESSION["intentos"])) {
                        $_SESSION["intentos"] = 0;
                    }
                    
                    // Redirecciona al formulario de password
                    redirecciona()->to("/login/second");
                } else {
                    // Conteo de ingresos fallidos
                    if(!isset($_SESSION["intentos"]) && !isset($_SESSION["user-validar"])) {
                        $_SESSION["intentos"] = 1; 
                        $_SESSION["user-validar"] = $user;
                        redirecciona()->to("/login");
                    } else {                         
                            if(!empty($_SESSION["user-validar"]) && $_SESSION["user-validar"] === $user) {
                                $_SESSION["intentos"]++;
                                echo " Intentos : " . $_SESSION["intentos"] ."\n";                            
                                print_r($_SESSION);
                                //echo "IP: " . $_SERVER["REMOTE_ADDR"]."\n";                                
//                              redirecciona()->to("/login");                            
                            } else {
                                $_SESSION["intentos"] = 1;
                                $_SESSION["user-validar"] = $user;
                                redirecciona()->to("/login");
                            } 
                                                                          
                        
                    }
                    //redirecciona()->to("/login");
                }
                
            } else {                
                redirecciona()->to("/login");
            }
                        
        } else {
            //echo "error al validar CSRF";
            redirecciona()->to("/login");
        }
    }
    
    public function second() {
        if(!empty($_SESSION["user-log"])){            
            $_SESSION["user-log"] = "";
            return Vista::crear('auth.auth_password');
        } else {
            session_destroy();            
            redirecciona()->to("/login");
        }        
    }
}