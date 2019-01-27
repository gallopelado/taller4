<?php

class LoginModel extends Conexion {

    protected static $cnx;

    // Validar Login
    public static function validarLogin($user, $password) {
        //$estadoCifrar = verifica_cifrar($user, $password);       
        try {
            //echo "***Validando Login***\n";

            $query = "SELECT usu_id, usu_nick, usu_clave, fun_id, gru_id
            FROM usuarios WHERE usu_nick = :nick";

            self::getConexion();

            $st = self::$cnx->prepare($query);
            $st->bindParam(':nick', $user);
            $rs = $st->execute();
            if ($rs) {
                if ($st->rowCount() > 0) {
                    $data = $st->fetch(PDO::FETCH_ASSOC);
                    foreach ($data as $clave => $valor) {
                        if ($clave == 'usu_clave') {
                            $estado_clave = verifica_cifrar($password, $valor);
                            if ($estado_clave) {
                                //echo "Password OK"; 
                                return true;
                            } else {
                                //die('Error'); 
                                return false;
                            }
                        }
                    }
                }
            } else {
                //echo "Nada que mostrar\n";
                return false;
            }
        } catch (Exception $th) {
            die('Error al validar ' . $th);
        } finally {
            self::desconectar();
        }
    }

    /**
     * Metodo buscarNick
     * 
     * Se encarga de verificar la existencia del usuario 
     * en la base de datos
     * 
     * @param type $user
     * @return boolean
     */
    public static function buscarNick($user) {

        try {
            $query = "SELECT usu_id, usu_nick, usu_clave, fun_id, gru_id, last_login, status "
                    . "FROM usuarios WHERE usu_nick = :nick";

            self::getConexion();

            $st = self::$cnx->prepare($query);

            $st->bindParam(':nick', $user);
            $rs = $st->execute();
            if ($rs) {
                if ($st->rowCount() > 0) {
                    $data = $st->fetch(PDO::FETCH_ASSOC);
                    foreach ($data as $clave => $valor) {
                        if ($valor === $user) {
                            return $data;
                        }
                    }
                    return false;
                }
            } else {
                return false;
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /**
     * Metodo contarIntentos
     * 
     * Se encarga de contar los intentos hechos por el usuarios para
     * devolver un objeto tipo statement
     * 
     * @param type $usuario
     * @param type $fechaDiff
     * @return statement
     */
    public static function contarIntentos($usuario, $fechaDiff) {
        try {
            $query = "SELECT count(*) n FROM login_log WHERE username = :usuario AND fecha > :fecha";

            self::getConexion();
            $st = self::$cnx->prepare($query);
            $st->bindParam(":usuario", $usuario);
            $st->bindParam(":fecha", $fechaDiff);

            $st->execute();

            return $st;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /**
     * Método que registra en login-log
     * 
     * Sirve para persistir los datos de la
     * sesion
     * 
     * @param type $username
     * @param type $fecha
     * @param type $msg
     * @param type $ip
     * @param type $browser
     * @param type $browser_version
     * @param type $platform
     * @param type $device_type
     * @param type $isMobile
     * @param type $isTablet
     * @return boolean
     */
    public static function loginLog($username, $fecha, $msg, $ip, $browser, $browser_version, $platform, $device_type, $isMobile, $isTablet) {
        try {
            $query = "INSERT INTO public.login_log(
            username, msg, fecha, ip, browser, browser_version, platform, device_type, ismobile, istablet)
            VALUES (:username, :msg, :fecha, :ip, :browser, :browser_version, :platform, :device_type, :isMobile, :isTablet)";

            self::getConexion();

            $st = self::$cnx->prepare($query);
            $st->bindParam(":username", $username);
            $st->bindParam(":fecha", $fecha);
            $st->bindParam(":msg", $msg);
            $st->bindParam(":ip", $ip);
            $st->bindParam(":browser", $browser);
            $st->bindParam(":browser_version", $browser_version);
            $st->bindParam(":platform", $platform);
            $st->bindParam(":device_type", $device_type);
            $st->bindParam(":isMobile", $isMobile);
            $st->bindParam(":isTablet", $isTablet);

            $st->execute();

            return true;
        } catch (Exception $ex) {
            die("Error en loginLog: \n" . $ex->getMessage());
        }
    }

    /**
     * Metodo de loginSuccess
     * 
     * Se encarga de actualizar el ultimo acceso
     * 
     * @param int $idUser
     * @return boolean
     */
    public static function loginSuccess(int $idUser) {
        try {
            $query = "UPDATE public.usuarios SET last_login=NOW()
            WHERE usu_id = :id";
            self::getConexion();

            $st = self::$cnx->prepare($query);
            $st->bindParam(":id", $idUser);
            $st->execute();
            return true;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /**
     * Metodo userBan
     * 
     * Se encarga de bloquear el usuario
     * 
     * @param type $usuario
     * @return boolean
     */
    public static function userBan($usuario) {
        try {
            $query = "UPDATE public.usuarios SET status=0
            WHERE usu_nick = :usuario";
            self::getConexion();

            $st = self::$cnx->prepare($query);
            $st->bindParam(":usuario", $usuario);
            $st->execute();
            return true;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /**
     * Método contarGrupos
     * 
     * Se encarga de contar los grupos
     * en los que participa el usuario
     * 
     * @param type $usuario
     * @return type
     */
    public static function contarGrupos($usuario) {
        try {
            $query = "SELECT count(*) n
            FROM usuarios_grupos ug
            join usuarios u on ug.usu_id = u.usu_id
            join grupos g on ug.gru_id = g.gru_id
            group by ug.usu_id, u.usu_nick
            having  u.usu_nick = :usuario";
            self::getConexion();
            $st = self::$cnx->prepare($query);
            $st->bindParam(":usuario", $usuario);
            $st->execute();
            return $st->fetch(PDO::FETCH_OBJ);
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    public static function controladorPorGrupo($usuario, $grupo) {
        $listArray = array();
        try {
            $query = "SELECT pag.pag_nombre pagnombre, pag.pag_direc direccion
            FROM usuarios_grupos ug
            join usuarios u on ug.usu_id = u.usu_id
            left join grupos g on ug.gru_id = g.gru_id
            left join permisos p on g.gru_id = p.gru_id
            left join paginas pag on p.pag_id = pag.pag_id
            where g.gru_nombre = :grupo and u.usu_nick = :usuario";
            self::getConexion();
            $st = self::$cnx->prepare($query);
            $st->bindParam(":grupo", $grupo);
            $st->bindParam(":usuario", $usuario);
            $st->execute();
            $data = $st->fetchAll(PDO::FETCH_ASSOC);
            foreach ($data as $clave) {
                $listArray[$clave["direccion"]] = $clave["pagnombre"] . ",";
            }
            // Eliminamos la ultima coma
            array_walk($listArray, 'trim_value');
            return $listArray;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    public static function allControladores($usuario) {
        
    }

    public static function verificaRol($usuario, $rol) {
        try {
            if ($rol == "ADMINISTRADOR") {
                $query = "SELECT g.gru_nombre grupo FROM usuarios_grupos ug
                join usuarios u on ug.usu_id = u.usu_id
                join grupos g on ug.gru_id = g.gru_id
                where  u.usu_nick = :usuario and g.gru_nombre = :rol";
                self::getConexion();
                $st = self::$cnx->prepare($query);
                $st->bindParam(":usuario", $usuario);
                $st->bindParam(":rol", $rol);
            } else {
                $query = "SELECT g.gru_nombre grupo FROM usuarios_grupos ug
                join usuarios u on ug.usu_id = u.usu_id
                join grupos g on ug.gru_id = g.gru_id
                where  u.usu_nick = :usuario and g.gru_nombre != 'ADMINISTRADOR'";
                self::getConexion();
                $st = self::$cnx->prepare($query);
                $st->bindParam(":usuario", $usuario);                
            }

            $st->execute();
            //return $st->fetch(PDO::FETCH_OBJ);
            if ($st->rowCount() > 0) {
                return $st->fetch(PDO::FETCH_OBJ);
            } else {
                return "vacio";
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    // Constructor
    function __construct() {
        self::getConexion();
    }

    public function __destruct() {
        self::desconectar();
    }

    // Obtener la conexion
    public static function getConexion() {
        self::$cnx = Conexion::conectar();
    }

    // Desconectar
    public static function desconectar() {
        if (self::$cnx != null)
            self::$cnx = null;
    }

}
