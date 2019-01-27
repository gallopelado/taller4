<?php

class Conexion {
    public static function conectar() {
        try {
            $cn = new PDO('pgsql:host=' . HOST . ';dbname=' . DB, USER, PASSWORD);
            $cn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//depuracion
            return $cn;
        } catch (Exception $exc) {
            die('Error en la conexion ' . $exc);
        }
    }
}