<?php
use \vista\Vista;
class BienvenidoController {

    public function index() {
        return Vista::crear('index');
    }

}