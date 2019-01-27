<?php

/**
 * Clase Ruta
 * 
 * El sistema inicia por aqui.
 * La clase Ruta es esencial para obtener datos ingresador por url,
 * analizarlos y procesarlos.
 * Tambien para cargar los respectivos controladores.
 * 
 * @author Juan González <juanftp100@gmail.com>
 */
class Ruta {

    private $_controladores = array();

    /**
     * Método controladores
     * 
     * Metodo que nos permite ingresar controladores con sus rutas
     * 
     * @param type $controlador
     */
    public function controladores($controlador) {

        if (!empty($controlador)) {
            // Se carga con un array proveniente de rutas.php
            $this->_controladores = $controlador;
//            print_r($this->_controladores);
            // Metodo que procesa las rutas
            $this->submit();
        } else {
            echo "La lista de controladores esta vacia!!";
        }
    }

    /**
     * Método submit()
     * 
     * Metodo que se ejecuta cada vez que se envia una peticion en la url
     * 
     * 
     */
    public function submit() {
        $uri = isset($_GET['uri']) ? $_GET['uri'] : '/';

        // Dividir la entrada en un array de partes
        $paths = explode("/", $uri);

        // Condicion para saber si estas en la raiz
        if ($uri == '/') {
            // Principal
            // Buscar si existe / en el array, de las claves
            $res = array_key_exists("/", $this->_controladores);
            // Si es verdadero el resultado es 1
            if ($res != '' && $res == 1) {
                // Recorrer el array definido en rutas.php
                foreach ($this->_controladores as $ruta => $controller) {
                    // Si la ruta es igual a /
                    if ($ruta == '/') {
                        // Asignar a controlador el valor de la KEY
                        $controlador = $controller;
                    } 
                }
                // Llamar al controlador                
                $this->getController('index', $controlador);
            }
        } else {
            // Otros controladores
            $estado = false;
            // Recorrer el array definido en rutas.php
            foreach ($this->_controladores as $ruta => $cont) {
                // Recortar la variable $ruta
                if (trim($ruta, '/') != '') {
                    // Si coincide los nombres del controlador definido y el escrito en la url
                    $pos = strpos($uri, trim($ruta, "/"));

                    // No coincide
                    if ($pos === false) {
                        // Nada que hacer                        
                    } else {
                        $arrayParams = array(); //para los parametros
                        $estado = true;
                        $controlador = $cont; // Se asigna el controlador
                        $metodo = '';
                        /*
                          1 - Eliminar al principio y final de la KEY $ruta el caracter / con trim()
                          /ciudadControlador   a    ciudadControlador

                          2 - Convertir en array separado por el caracter / con explode()
                          array(
                          [0] => "ciudadControlador")
                          3 - Contar cuantos elementos del array con count()
                          1
                         */
                        $cantidadRuta = count(explode('/', trim($ruta, '/')));
                        //echo "CantidadRuta $cantidadRuta\n";
                        $cantidad = count($paths);
                        //echo "cantidad $cantidad\n";

                        /* Si la cantidad encontrada en el array de
                          $path es mayor que la encontrada en el array de lo
                          obtenido en las rutas definidas
                          1. obtenido en la url
                          Array
                          (
                          [0] => ciudad
                          [1] => agregar
                          )
                          2.
                         */
                        // $cantidad = obtengo de lo escrito en la uri
                        // $cantidadRuta = obtengo de lo definido en rutas.php, siempre sera 1
                        // En array el 1 es 0, 1
                        // Entonces si es mayor a la cantidadRuta, quiere decir que hay parametros
                        if ($cantidad > $cantidadRuta) {
                            // El tercer lugar en el array es 2 --- 0, 1, 2
                            // En el tercer lugar estan los parametros
                            // ciudad/agregar/12000
                            $metodo = $paths[$cantidadRuta];
                            for ($i = 0; $i < count($paths); $i++) {
                                //Capturamos el ultimo lugar
                                if ($i > $cantidadRuta) {
                                    $arrayParams[] = $paths[$i];
                                }
                            }
                        }
                        // Incluimos el controlador
                        $this->getController($metodo, $controlador, $arrayParams);
                    }
                }
            }
            if ($estado == false) {
                if (isset($_SESSION["nick"])) {
                    redirecciona()->to("/admin");
                } else {
                    redirecciona()->to("/login");
                    //die('Error en la ruta');
                }
            }
        }
    }

    /**
     * Método getController
     * 
     * Metodo que procesa los controladores
     * 
     * @param type $metodo
     * @param type $controlador
     * @param type $params
     */
    public function getController($metodo, $controlador, $params = null) {
        // Para almacenar el metodo
        $metodoController = '';

        //Comprobar el tipo de metodo recibido
        if ($metodo == 'index' || $metodo == '') {
            // Si el metodo es vacio, sera index
            $metodoController = 'index';
        } else {
            // Si es distinto a index
            $metodoController = $metodo;
        }

        // Incluir el controlador
        $this->incluirControlador($controlador);

        // Comprobar si existe la clase
        if (class_exists($controlador)) {
            //Asignar la clase a una variable, que sera la clase
            $ClaseTemp = new $controlador();
            // Puedo llamar al metodo ?
            if (is_callable(array($ClaseTemp, $metodoController))) {
                // El metodo es index?
                if ($metodoController == 'index') {
                    // No enviaste parametros ?                    
                    if (empty($params)) {
                        $ClaseTemp->$metodoController();
                    } else {
                        die('Error en parametros');
                    }
                } else {
                    // Es distinto al metodo index
                    // Llamar al metodo de la clase con sus parametros
                    call_user_func_array(array($ClaseTemp, $metodoController), $params);
                }
            } else {
                die('No existe el metodo');
            }
        } else {
            die('No existe la clase');
        }
    }

    /**
     * Método incluirControlador
     * 
     * Dependiendo de lo enviado, hace un include del fichero
     * 
     * @param type $controlador
     */
    public function incluirControlador($controlador) {
        // Validar si existe el archivo
        if (file_exists(CONTROLLERS . $controlador . '.php')) {
            //Si existe incluimos
            include CONTROLLERS . $controlador . '.php';
        } else {
            die('No existe el controlador');
        }
    }

}
