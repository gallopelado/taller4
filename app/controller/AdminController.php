<?php

/**
 * Description of AdminController
 *
 * @author Juan González <juanftp100@gmail.com>
 */
use vista\Vista;

class AdminController {

    public function index() {
        return Vista::crear("admin.index");
    }

    public function logout() {
        // Destruir las variables sesion anteriores 
        destruir_sesion();
        redirecciona()->to("/login");
    }

}
