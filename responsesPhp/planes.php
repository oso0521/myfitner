<?php
	include("../header.php");
	$idPrograma = $_GET['idPrograma'];
	if($idPrograma == "")
	{
		echo "No data received";
		exit;	
	}
	$query="
		SELECT
			idObjetivo,
			nombre
		FROM
			Objetivo
		WHERE
			habilitado = 1
	";
	$db->setquery("Traer los objetivos",$query);
	$objetivos="<option value=\"\">Seleccione...</option>";
	while($datos=$db->fetch())
	{
		$objetivos .= "<option value=\"".$datos[idObjetivo]."\">".$datos[nombre]."</option>";
	}
	
	$query="
		SELECT
			*
		FROM
			Plan
		WHERE
			habilitado = 1
			AND idPrograma = !Q%
	";
	
	$db->setquery("traer los planes de un programa",$query,array($idPrograma));
	while($datos = $db->fetch())
	{
		?>
        <div class="plan" data-id="<?= $datos['idPlan']?>">
        	<form>
                <input type="text" name="nombre" id="nombre" maxlength="150" placeholder="NOMBRE" value="<?= $datos['nombre']?>" />
                <select>
                    <?= $objetivos ?>
                </select>
            </form>
            <button class="planBtn" data-id="<?= $datos['idPlan']?>">Ver</button>
        </div>
        <?php
	}
	
?>

<script>
	$(".planBtn").unbind("click")
				.click(function(){
					$.get("responsesPhp/rutinas.php?idPlan="+$(this).data("id"),function(e){
						$("#rutinas").html(e);	
					});	
				});
</script>