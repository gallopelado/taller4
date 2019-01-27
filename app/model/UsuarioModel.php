<?php

class UsuarioModel extends Conexion {

    # atributos
    private $op;
    private $usu_id;
    private $usu_nick;
    private $usu_clave;
    private $fun_id;
    private $gru_id;
    protected static $cnx;

    # Metodos para acceder a la BD
    // Constructor
    function __construct() {
        self::getConexion();
    }

    // Obtener Conexion
    public static function getConexion() {
        self::$cnx = Conexion::conectar();
    }

    // Liberar la conexion    
    public static function desconectar() {
        self::$cnx = null;
    }

    // Leer consulta    
    public static function getAll() {
        $query = 'SELECT * FROM usuarios';
        self::getConexion();
        $res = self::$cnx->query($query);        
        return $res->fetchAll();                
    }    

    // Getters y Setters
    /**
     * Get the value of gru_id
     */ 
    public function getGru_id()
    {
        return $this->gru_id;
    }

    /**
     * Set the value of gru_id
     *
     * @return  self
     */ 
    public function setGru_id($gru_id)
    {
        $this->gru_id = $gru_id;

        return $this;
    }

    /**
     * Get the value of fun_id
     */ 
    public function getFun_id()
    {
        return $this->fun_id;
    }

    /**
     * Set the value of fun_id
     *
     * @return  self
     */ 
    public function setFun_id($fun_id)
    {
        $this->fun_id = $fun_id;

        return $this;
    }

    /**
     * Get the value of usu_clave
     */ 
    public function getUsu_clave()
    {
        return $this->usu_clave;
    }

    /**
     * Set the value of usu_clave
     *
     * @return  self
     */ 
    public function setUsu_clave($usu_clave)
    {
        $this->usu_clave = $usu_clave;

        return $this;
    }

    /**
     * Get the value of usu_nick
     */ 
    public function getUsu_nick()
    {
        return $this->usu_nick;
    }

    /**
     * Set the value of usu_nick
     *
     * @return  self
     */ 
    public function setUsu_nick($usu_nick)
    {
        $this->usu_nick = $usu_nick;

        return $this;
    }

    /**
     * Get the value of usu_id
     */ 
    public function getUsu_id()
    {
        return $this->usu_id;
    }

    /**
     * Set the value of usu_id
     *
     * @return  self
     */ 
    public function setUsu_id($usu_id)
    {
        $this->usu_id = $usu_id;

        return $this;
    }

    /**
     * Get the value of op
     */ 
    public function getOp()
    {
        return $this->op;
    }

    /**
     * Set the value of op
     *
     * @return  self
     */ 
    public function setOp($op)
    {
        $this->op = $op;

        return $this;
    }    
}