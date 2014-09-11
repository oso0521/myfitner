<?php
include("../header.php");
$idEjercicio = $_GET['idEjercicio'];
if($idEjercicio == "")
{
	echo "Insuficient data";
	exit;	
}
$query="
			SELECT
				e.*
			FROM
				Ejercicio e
			WHERE
				e.habilitado = 1 
				AND e.idEjercicio = $idEjercicio
		";	
		$db2->setquery("Traer los ejercicios de las rutinas",$query);
		while($ejercicio=$db2->fetch())
		{
			?>
            <div class="ejercicio">
            	<button class="deleteThis">
                	Eliminar
                </button>
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
                <form action="javascript:void(0)"  data-id="<?= $ejercicio['idEjercicio']?>">
                    <label>Repeticiones</label><input type="number" id="repeticiones" name="repeticiones" placeholder="REPETICIONES" value="<?= $ejercicio['repeticiones'] ?>" />
                    <label>Series</label><input type="number" id="series" name="series" placeholder="SERIES" value="<?= $ejercicio['repeticiones'] ?>" />
                    <label>Descanso</label><input type="text" id="descanso" name="descanso" placeholder="DESCANSO" value="<?= $ejercicio['descanso'] ?>" />
                    <label>RM</label><input type="text" id="rm" name="rm" placeholder="% RM" value=" <?= $ejercicio['rm'] ?>" />%
                    <label>Ritmo</label><input type="text" id="ritmo" name="ritmo" placeholder="RITMO" value="<?= $ejercicio['ritmo']?>" />
                    <button class="agregaEjercicio">
                    	Agregar
                    </button>
                </form>
            </div>
            <?php
		}
		?>
		