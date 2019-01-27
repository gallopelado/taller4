<!-- jQuery -->
<script src="<?php asset("vendor/jquery/jquery.min.js") ?>"></script>

<!-- Bootstrap Core JavaScript -->
<script src="<?php asset("vendor/bootstrap/js/bootstrap.min.js") ?>"></script>

<!-- Metis Menu Plugin JavaScript -->
<script src="<?php asset("vendor/metisMenu/metisMenu.min.js") ?>"></script>

<!-- Custom Theme JavaScript -->
<script src="<?php asset('dist/js/sb-admin-2.js') ?>"></script>

<script src="<?php asset('dist/js/jquery-confirm.min.js') ?>"></script>

<script src="<?php asset('dist/js/angular.min.js') ?>"></script>

<script src="<?php asset('js/controladores/VentaController.js') ?>"></script>

<script>

    function confirmar(url) {
        $.confirm({
            title: "Cuidado!",
            content: "Deseas eliminar esto ?",
            buttons: {
                confirm: {
                    text: "Confirmar",
                    btnClass: "btn-info",
                    action: () => {
                        window.location.href = url;
                    }
                },
                cancel: {
                    text: "Cancelar",
                    btnClass: "btn-danger",
                    action: () => {
                        $.alert("Cancelaste pancho");
                    }
                }
            }
        });
    }

</script>

