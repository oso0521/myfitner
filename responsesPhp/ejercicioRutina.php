<?php
	include("../header.php");
	
	$idRutina = $db->secure($_GET['idRutina']);
	$idEjercicio = $db->secure($_GET['idEjercicio']);
	$repeticiones = $db->secure($_GET['repeticiones']);
	$descanso=$db->secure($_GET['descanso']);
	$diasSemana=$db->secure($_GET['diasSemana']);
	$rm=$db->secure($_GET['rm']);
	$ritmo= $db->secure($_GET['ritmo']);
	$series= $db->secure($_GET['series']);
	$query="
		INSERT INTO
			Ejercicio_Rutina(
				repeticiones,
				descanso,
				idEjercicio,
				idRutina,
				habilitado,
				rm,
				ritmo,
				series
			)
		VALUES(
			'$repeticiones',
			'$descanso',
			'$idEjercicio',
			'$idRutina',
			1,
			'$rm',
			'$ritmo',
			'$series'
		)
	";
	
	if($db->setquery("Guarda la relacion ejercicio-rutina",$query))
	{
		echo "Guardado correctamente";
	}else
	{
		echo "No se guardo ocurrio un error";
	}
?>