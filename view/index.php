<?php
if(!isset($_SESSION["nick"])):  
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SYS IEC</title>
    <?php include VISTA_RUTA . 'inc/head.php'?>
</head>
<body>
<div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1 class="">
                        SYS IEC                        
                    </h1>
                    <hr>
                    <a href="<?php url("login")?>" class="btn btn-primary">Ingresar al sistema <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
</body>
</html>
<?php else:    
     redirecciona()->to("/admin");
endif;
?>