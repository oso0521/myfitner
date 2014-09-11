<?php
	include("../header.php");
	$idPlan= $_GET['idPlan'];
	
	
	$query="
		SELECT
			idRutina,
			nombre
		FROM
			Rutina
		WHERE
			habilitado = 1
			AND idRutina not in (
					SELECT
						idRutina
					FROM
						Rutina_Plan
					WHERE
						idPlan = !Q%
						AND habilitado = 1
				)
	";
	$db->setquery("Traer todas las rutinas que no están en el plan",$query,array($idPlan));
	?>
    <form id="agregaRutinaPlan" action="javascript:agregaRutina(<?= $idPlan?>)">
        <select id="rutinasPool" name="rutinasPool">
            <option value="">Seleccione..</option>
        <?php
        while($datos=$db->fetch())
        {
            ?>
             <option value="<?= $datos[idRutina]?>"><?= $datos[nombre]?></option>
            <?php
        }
        ?>
        </select><br />
       <input type="number" id="repeticiones" name="repeticiones" placeholder="REPETICIONES" maxlength="10" max="9999999" /> <br />
       <label for="circuito">¿Circuito?</label> <input type="checkbox" id="circuito" name="circuito" /><br />
       <input type="number" id="diasSemana" name="diasSemana" max="9999999" maxlength="10" placeholder="NUM DIAS POR SEMANA" /><br />
		<textarea id="descRutinaPlan" name="descripcionRutinaPlan" placeholder="DESCRIPCION"></textarea>
        <button type="submit">+</button>
    </form>
    <?php
	$query="
		SELECT
			r.idRutina,
			r.nombre,
			r.nivel,
			rp.repeticiones,						
			rp.idRutina_Plan
		FROM
			Rutina r,
			Rutina_Plan rp
		WHERE
			r.habilitado = 1
			AND r.idRutina = rp.idRutina
			AND rp.idPlan = !Q%
	";
	
	$db->setquery("Traer datos generales de las rutinas",$query,array($idPlan));
	while($datos = $db->fetch())
	{
		
		
		
		
	}
?>

<script>
	function agregaRutina(idPlan)
	{
		var idRutina = $("#rutinasPool").val();
		var params = $("#agregaRutinaPlan").serialize();
		if(idRutina == "")
		{
			alert("Debe seleccionar una rutina");
		}
		$.get("responsesPhp/rutinaPlan.php?idPlan="+idPlan+"&idRutina="+idRutina+"&"+params,function(e){
			alert(e);	
		});
	}
	
	
</script>