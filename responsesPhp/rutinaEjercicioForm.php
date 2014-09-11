<?php
	include("../header.php");
	$idRutina = $_GET['idRutina'];
?>
	<select id="idRutina" name="idRutina" onChange="getRutina()">
    	<option value="">Seleccione</option>
        <?php
			$query="
		SELECT
			idRutina,
			nombre
		FROM
			Rutina
		WHERE
			habilitado = 1
			
	";
	$db->setquery("Traer todas las rutinas que no están en el plan",$query);
	 while($datos=$db->fetch())
        {
            ?>
             <option value="<?= $datos[idRutina]?>" <?= $idRutina == $datos[idRutina] ? ' selected="selected" ':''?>><?= $datos[nombre]?></option>
            <?php
        }
		?>
    	
    </select><br>
	<select id="idEjercicio" name="idEjercicio" onChange="getEjercicio()">
    	<option value="">Seleccione</option>
        <?php
			$query="
				SELECT
					idEjercicio,
					nombre
				FROM
					Ejercicio
				WHERE
					habilitado = 1
				
			";
			if($idRutina != "")
			{
				$query.="
					AND idEjercicio not in(
						SELECT
							idEjercicio
						FROM
							Ejercicio_Rutina
						WHERE
							idRutina = $idRutina
					)
				";	
			}
			$query.="ORDER BY nombre";
			$db->setquery("Traer todos los ejercicios",$query);
			while($datos = $db->fetch())
			{
				?>
                <option value="<?= $datos['idEjercicio']?>"><?= $datos['nombre'] ?></option>
                <?php
			}
		?>
    </select>
    <div id="ejercicios">
<?php
	
	if($idRutina != "")
	{
		
	$query="
			SELECT
				e.*,
				er.repeticiones,
				er.descanso,
				er.rm,
				er.ritmo,
				er.idEjercicio_Rutina,
				er.series
			FROM
				Ejercicio e,
				Ejercicio_Rutina er
			WHERE
				e.habilitado = 1 
				AND e.idEjercicio = er.idEjercicio
				AND er.idRutina = $idRutina
		";	
		$db2->setquery("Traer los ejercicios de las rutinas",$query);
		while($ejercicio=$db2->fetch())
		{
			?>
            <div class="ejercicio">
            	
                <table>                	
                		<tr>
                        	<th colspan="2">
                            	<img src="<?= $ejercicio['img']?>" class="gifEjercicio" />
                            </th>
                        </tr>
                    	<tr>
                        	<th>
                            	Nombre
                            </th>
                            <td>
                            	<?= $ejercicio['nombre'] ?>
                            </td>
                        </tr>
                        <tr>
                        	<th>
                            	Descripci&oacute;n
                            </th>
                            <td>
                            	<?= $ejercicio['descripcion'] ?>
                            </td>
                        </tr>                    
                </table>
                <form action="javascript:void(0);" data-id="<?= $ejercicio['idEjercicio_Rutina']?>">
                    <label>Repeticiones</label><input type="number" id="repeticiones" name="repeticiones" placeholder="REPETICIONES" value="<?= $ejercicio['repeticiones'] ?>" />
                      <label>Series</label><input type="number" id="series" name="series" placeholder="SERIES" value="<?= $ejercicio['series'] ?>" />
                    <label>Descanso</label><input type="text" id="descanso" name="descanso" placeholder="DESCANSO" value="<?= $ejercicio['descanso'] ?>" />
                    <label>RM</label><input type="text" id="rm" name="rm" placeholder="% RM" value=" <?= $ejercicio['rm'] ?>" />%
                    <label>Ritmo</label><input type="text" id="ritmo" name="ritmo" placeholder="RITMO" value="<?= $ejercicio['ritmo']?>" />
                    <button>
                    	Guardar cambios
                    </button>
                </form>
            </div>
            <?php
		}
	}
?>
	</div>
    
    <script>
		function getEjercicio()
		{
			var id = $("#idEjercicio").val();	
			$.get("responsesPhp/ejercicios.php?idEjercicio="+id,function(e){
				$("#ejercicios").append(e);
				$(".deleteThis").unbind("click").click(function(){
					$(this).parent().remove();	
				});
				initE();
			});
		}
		
		function getRutina()
		{
			var id = $("#idRutina").val();
			if(id!= "")
			{
				$.get("responsesPhp/rutinaEjercicioForm.php?idRutina="+id,function(e){
					$("#planes").html(e);
					initE();		
				});	
			}
		}
		function initE()
		{
			$(".agregaEjercicio").unbind("click")
								.click(function()
								{
									var idE = $(this).parent().data("id");
									
									if(idE == "" || typeof idE == typeof undefined)
									{
										alert("Ocurrio un error al identificar el ejercicio");
										return;	
									}
									var idRu = $("#idRutina").val();
									if(idRu == "" || typeof idRu == typeof undefined)
									{
										alert("Ocurrio un error al identificar el ejercicio");
										return;	
									}
									var params = $(this).parent().serialize()+"&idEjercicio="+idE+"&idRutina="+idRu;
									$.get("responsesPhp/ejercicioRutina.php?"+params,function(e){
										alert(e);
									});
								});
		}
	</script>