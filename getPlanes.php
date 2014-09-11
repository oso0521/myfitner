<?php
	include("../header.php");
	$query="
		SELECT
			*
		FROM
			Programa
		WHERE
			habilitado = 1
		ORDER BY
			idEspecialidad
	";
	$db->setquery("Traer los programas",$query);
	$i=0;
	$json = "{\"Data\":[";

	while($datos = $db->fetch()){
		$return[$datos['idEspecialidad']][$datos['idPrograma']][$i] = array("nombre"=>$datos['nombre'],"idEspecialidad"=>$datos['idEspecialidad'],"idPrograma"=>$datos['idPrograma'],"planes"=>array());
		//echo "idP : $datos[idPrograma] $i <br />";
		$json .= "{nombre:\"$datos[nombre]\",
					planes:[";
		$query="
			SELECT
				p.*,
				COUNT(rp.idRutina) as rutinas,
				rp.idRutina
			FROM
				Plan p
					LEFT JOIN
						Rutina_Plan rp ON
							rp.idPlan = p.idPlan					
						
			WHERE
				p.idPrograma = $datos[idPrograma]
				AND p.habilitado = 1
			GROUP BY
				rp.idRutina
		";
		//echo $query;
		$db2->setquery("Traer los planes",$query);
		$j=0;
		while($plan = $db2->fetch())
		{
			$return[$datos['idEspecialidad']][$datos['idPrograma']][$i]['planes'][$plan['idPlan']][$j] = array("idPlan"=>$plan['idPlan'],"nombre"=>$plan['nombre'],"numrutinas"=>$plan['rutinas'],"rutinas"=>array());
			//echo "idP : $datos[idPrograma] $i idPlan: $plan[idPlan] $j <br />";
			if($j>0)
				$json.=",";
			$json .= "
			{
				nombre:\"$plan[nombre]\",
				numrutinas: $plan[rutinas],
				rutinas:[
				
			";
			if($plan['idRutina']!= "")
			{
				
				$query="
					SELECT
						idRutina,
						nombre
					FROM
						Rutina
					WHERE
						habilitado = 1
						AND idRutina = $plan[idRutina]
				";
				//echo $query;
				$qEx = $db->squery($query);
				$k=0;
				while($rutinas = $db->fetch($qEx))
				{
					$return[$datos['idEspecialidad']][$datos['idPrograma']][$i]['planes'][$plan['idPlan']][$j]['rutinas'][$rutinas['idRutina']]= array("idRutina"=>$rutinas['idRutina'],"nombre"=>$rutinas['nombre'],"idRutina"=>$rutinas['idRutina'],"ejercicios"=>array());
					//echo "idP : $datos[idPrograma] $i idPlan: $plan[idPlan] $j idRutina: $rutinas[idRutina] $k <br />";
					if($k>0)
						$json.=",";
					$json.="
						{nombre=\"$rutinas[nombre]\",
						idRutina=\"$rutinas[idRutina]\",
						ejercicios:[
						";
						$query="
							SELECT
								e.nombre,
								e.idEjercicio,
								e.img,
								e.gif,
								er.repeticiones,
								er.descanso,
								er.rm,
								er.ritmo,
								er.series
							FROM
								Ejercicio e,
								Ejercicio_Rutina er
							WHERE
								e.habilitado = 1
								AND er.idEjercicio = e.idEjercicio
								AND er.idRutina = $rutinas[idRutina]
						";
						$qEx = $db->squery($query);
						$l=0;
						while($ejercicio = $db->fetch($qEx))
						{
							$return[$datos['idEspecialidad']][$datos['idPrograma']][$i]['planes'][$plan['idPlan']][$j]['rutinas'][$rutinas['idRutina']]['ejercicios'][$ejercicio['idEjercicio']] = array("idEjercicio"=>$ejercicio['idEjercicio'],"nombre"=>$ejercicio['nombre'],"img"=>$ejercicio["img"],"gif"=>$ejercicio['gif'],"repeticiones"=>$ejercicio['repeticiones'],"descanso"=>$ejercicio['descanso'],"rm"=>$ejercicio['rm'],"ritmo"=>$ejercicio['ritmo'],"series"=>$ejercicio['series']);
							//echo "idP : $datos[idPrograma] $i idPlan: $plan[idPlan] $j idRutina: $rutinas[idRutina] $k  idEjercicio: $ejercicio[idEjercicio] $l <br />";
							if($l>0)
								$json.=",";
							$json.="{
									nombre:\"$ejercicio[nombre]\",
									img:\"$ejercicio[img]\",
									gif:\"$ejercicio[gif]\"
							}
							";	
							
						}
						$k++;
						$json.="]}";
				}
				$j++;
				$json.="]}";
			}
			
			$json.="]}";
		}
		$i++;
		$json .= "]}";
	}
	$json.="
	}
	";
	$returnF['Data'] = $return;
	echo json_encode($returnF);
?>