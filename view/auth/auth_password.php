<?php
if(!isset($_SESSION["nick"])):
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SYS IEC | Login</title>
     <!-- Bootstrap Core CSS -->
     <link href="<?php asset("vendor/bootstrap/css/bootstrap.min.css") ?>" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="<?php asset("vendor/metisMenu/metisMenu.min.css") ?>" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php asset("dist/css/sb-admin-2.css") ?>" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="<?php asset("vendor/font-awesome/css/font-awesome.min.css") ?>" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title text-center">Ingresar al Sistema</h3>
                    </div>
                    <div class="panel-body">
                        <form role="form" method="POST" action="<?php url('login/ingresar') ?>">
                            <input type="hidden" name="_token" value="<?php csrf_token() ?>">
                            <fieldset>                                
                                <div class="form-group">
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required autofocus>
                                </div>
                                <button type="submit" class="btn btn-lg btn-success btn-block">Conectarse</button>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="<?php asset("vendor/jquery/jquery.min.js") ?>"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="<?php asset("vendor/bootstrap/js/bootstrap.min.js") ?>"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="<?php asset("vendor/metisMenu/metisMenu.min.js") ?>"></script>

    <!-- Custom Theme JavaScript -->
    <script src="<?php asset("dist/js/sb-admin-2.js") ?>"></script>
</body>
</html>
<?php else:
     redirecciona()->to("/admin");
endif;
?>