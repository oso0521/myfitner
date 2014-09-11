<?php	
	include("header.php");	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Admin</title>
        <link rel="stylesheet" href="_ABC/css/red-theme/jquery-ui.min.css" />
        <link rel="stylesheet" href="_ABC/css/red-theme/jquery-ui.theme.min.css" />
        <link rel="stylesheet" href="_ABC/css/jquery.tooltip.css" />
        <link rel="stylesheet" href="_ABC/css/ajaxfileupload.css" />
        <link rel="stylesheet" href="_ABC/css/jqueryui.datatables.css" />
        <link rel="stylesheet" href="_ABC/css/abc_style.css" />
         <script type="text/javascript" src="_ABC/js/jquery-1.11.1.min.js"></script>
		<script type="text/javascript" src="_ABC/js/jquery-ui.1.10.min.js"></script>
        <script type="text/javascript" src="_ABC/js/jquery-ui-timepicker-addon.js"></script>
        <script type="text/javascript" src="_ABC/js/jquery.imagepreview.js"></script>
        <script type="text/javascript" src="_ABC/js/abc_function.js"></script>
        <script type="text/javascript" src="_ABC/js/ajaxfileupload.js"></script>
        <script type="text/javascript" src="_ABC/js/abc_ajax.js"></script>
        <script type="text/javascript" src="_ABC/js/jquery.dataTables.js"></script>
        <script type="text/javascript" src="_ABC/js/datatables.jquery.integration.js"></script>
        <script type="text/javascript" src="_ABC/js/jquery.noty.packaged.min.js"></script>
        <style>
			.inline{
				display:inline-block;				
			}
			.w33
			{
				width:30%;				
			}
		</style>
    </head>
    <body>
    	<button id="getRutinas">
            	Rutinas
         </button>
    	<div id="planes" class="inline w33">
        	
        	<?php
            	$query="
					SELECT
						*
					FROM
						Programa
					WHERE
						habilitado = 1
				";
				$db->setquery("Traer los planes",$query);
				while($datos = $db->fetch())
				{
					?>
                    <div class="programa" data-id="<?= $datos['idPrograma']?>">
                    	<form class="programForm" action="javascript:void(0)">
                            <input type="text" id="nombre" name="nombre" data-id="<?= $datos['idPrograma']?>" maxlength="150" placeholder="NOMBRE" value="<?= $datos[nombre]?>"/>
                            <input type="text" id="objetivo" name="objetivo" data-id="<?= $datos['idPrograma']?>" maxlength="250" placeholder="OBJETIVO" value="<?= $datos[objetivo]?>"/>
                            <select id="nivel" name="nivel">
                                <option value="">Seleccione...</option>
                                <option value="Principiante" <?= $datos['nivel'] == 'Principiante' ? ' selected="selected" ':'' ?>>Principiante</option>
                                <option value="Intermedio" <?= $datos['nivel'] == 'Intermedio' ? ' selected="selected" ':'' ?>>Intermedio</option>
                                <option value="Avanzado" <?= $datos['nivel'] == 'Avanzado' ? ' selected="selected" ':'' ?>>Avanzado</option>
                                <option value="Experto" <?= $datos['nivel'] == 'Experto' ? ' selected="selected" ':'' ?>>Experto</option>
                            </select>                        
                            <textarea id="descripcion" name="descripcion" placeholder="DESCRIPCION"><?= $datos['descripcion']?></textarea>
                        </form>
						<button class="programaBtn" data-id="<?= $datos['idPrograma']?>">Ver</button>
                    </div>
                    <?php
				}
            ?>
        </div>
        <div id="programas" class="inline w33">
        	
        </div>
        <div id="rutinas" class="inline w33">
        	
        </div>
        <table id="ejercicio">
        </table>
        <script>
			$(".programaBtn").unbind("click")
							.click(function(){
								$.get("responsesPhp/planes.php?idPrograma="+$(this).data("id"),function(e){
									$("#programas").html(e);
								});	
							});
							
			$("#getRutinas").unbind("click")
							.click(function(){
								$.get("responsesPhp/rutinaEjercicioForm.php",function(e){
									$("#planes").html(e);	
								});	
							});
			
			abc_table("Ejercicio","file_img:../../img/static,_IMAGE|file_gif:../../gifs,_IMAGE","ejercicio");
		</script>
    </body>
</html>