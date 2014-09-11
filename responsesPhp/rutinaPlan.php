<?php
	include("../header.php");
	$idPlan = $db->secure($_GET['idPlan']);
	$idRutina = $db->secure($_GET['idRutina']);
	$repeticiones = $db->secure($_GET['repeticiones']);
	$numDiasSemana = $db->secure($_GET['diasSemana']);
	$descripcion = $db->secure($_GET['descripcionRutinaPlan']);
	$circuito= $db->secure($_GET['circuito']);
	if($idPlan == "" || $idRutina == "")
	{
		echo "Data insufficient";	
		exit;
	}
	
	$query="
		INSERT INTO 
			Rutina_Plan(
				idRutina,
				idPlan,
				repeticiones,
				numDiasSemana,
				descripcion,
				circuito
			)
		VALUES(
			$idRutina,
			$idPlan,
			'$repeticiones',
			'$numDiasSemana',
			'$descripcion',
			'$circuito'
		)
		";
		
	if($db->setquery("Guardar la relacion rutina plan",$query))
	{
		echo "Guardado exitosamente";	
	}else{
		echo "Error al guardar";
	}
?>