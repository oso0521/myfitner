<?php
	/*
		Propiedad de MovIT Business Everywhere (C)
		@author: Osvaldo Ramírez Martínez
		
		code	v1	05.2012	
	 */

    session_start();
	date_default_timezone_set("America/Mexico_City");
	setlocale(LC_ALL,'esp');
	
	//error_reporting(0);
	//ini_set("display_errors", 0);
	
	/*******************************************************************************************************************
		
		CONFIGURACION HEADER
		
		En esta sección se definen las variables de configuración para el uso del header
		
	*******************************************************************************************************************/
	
	$session_user_variable = "idU"; // Nombre que recibe la variable que contiene el idUsuario en la variable de sesión
	$session_permission_variable = "type"; // Nombre que recibe la variable que contiene los permisos del usuario en la variable de sesión
	$db_path = "include/mysql-2.4.php"; //Ruta a la clase manejadora de la bd
	$mail_path = "include/mailing.php"; //Ruta hacia el archivo de mailing
	$encrypt_path = "include/keygen.php"; //Ruta hacia el archivo de funciones de encripción
	$domain = "http://myfitner.movitbe.com"; //Dominio actual del sitio
	$now = "convert_tz(UTC_TIMESTAMP(),'UTC','America/Mexico_City')"; //Para obtener la fecha y hora de mx de la BD
	$js_version = "3"; //Versión de JS para provocar Hard Refresh en usuarios
	$permission = array( //Arreglo con tipos de permiso del sistema
		'D' => 'administrativo',
		'S' => 'estudiante',
		'T' => 'profesor',  
		'P' => 'padre'
	);

	/*
	ENCRIPCION DE URLS
	 */
	$secretKey = "myfitner"; //preset key to use on all encrypt and decrypts.
	
	/*******************************************************************************************************************
		
		FIN CONFIGURACION HEADER
		
	*******************************************************************************************************************/
	
	/*******************************************************************************************************************
		
		INCLUDES
		
		En esta sección se definen las variables para incluir funciones específicas
		
	*******************************************************************************************************************/
	
	//Verificar Sesión Activa
	if(!isset($flag_session))
		$flag_session = 0; //1 - check session DEFAULT //0 - no check
	
	if($flag_session)
		//echo $_SESSION[$session_user_variable];
		if(!isset($_SESSION[$session_user_variable])){
			echo "El acceso a sido denegado. Debe <a href=\"".$domain."\">iniciar sesi&oacute;n</a>";
			header("Location:".$domain."/index.php");
			echo '<script> window.location = "'.$domain.'";</script>';
			exit;
		}
		else{
			$idUsuario = $_SESSION[$session_user_variable];	
		}
	
	//Incluir manejar de Base de Datos
	if(!isset($flag_mysql))
		$flag_mysql =1; //0 - no include //1 - include DEFAULT
	
	if($flag_mysql){		
		include_once($db_path);
		$db = new MySQL();
		$db2 = new MySQL();
	}
	
	//Incluir manejador de mail
	if(!isset($flag_mail))
		$flag_mail =0; //0 - no include //1 - include DEFAULT
	
	if($flag_mail){
		include_once($mail_path);
	}
	
	//Incluir manejador para encripcion
	if(!isset($flag_encrypt))
		$flag_encrypt =0; //0 - no include //1 - include DEFAULT
	
	if($flag_encrypt){
		include_once($encrypt_path);
	}
	
	//Método por default para obtener variables, se utiliza para la función getVar
	if(!isset($flag_method))
		$flag_method ="REQUEST"; //GET, POST or REQUEST
	
	/*******************************************************************************************************************
		
		FIN INCLUDES
		
	*******************************************************************************************************************/

	/*******************************************************************************************************************
		
		FUNCTIONS
		
		Seccion donde se definen diversas funciones
		
	*******************************************************************************************************************/
	
	/*
		Función que determina si una string (needle) existe dentro de otra string (haystack)
		
		@returns: True/False
	 */
	function contains($haystack,$needle){
		return strpos($haystack,$needle) !== false;
	}
	
	/*
		Función que convierte una date en formato SQL a una fecha legible en español
		
		@returns: Fecha en formato dd de MTEXT de yyyy
	 */
	function fecha($fechaSQL){
		str_replace('\'',"",$fechaSQL);
		for($i=0;$i<strlen($fechaSQL);$i++)
		{
			
			if($fechaSQL[$i] == "'")
				$fechaSQL[$i] = "";
		}
		$fecha=explode("-",$fechaSQL);
		if(count($fecha) < 2)
		{
			return "";	
		}
		$mesArray = array( 1 => "Enero", 2 => "Febrero", 3 => "Marzo", 4 => "Abril", 5 => "Mayo", 6 => "Junio", 7 => "Julio", 8 => "Agosto", 9 => "Septiembre", 10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre" );
		$mes=$fecha[1]*1;		
		return $fecha[2].' de '.$mesArray[$mes].' de '.$fecha[0];			
	}
	
	/*
		Función que covierte una datetime en formato SQL a una fechahora legible en español
		
		@returns: FechaHora en formato dd de MTEXT de yyyy a las hh:mm:ss.tz
	 */
	function fechaHora($fechaSQL){
		for($i=0;$i<strlen($fechaSQL);$i++)
		{
			
			if($fechaSQL[$i] == "'")
				$fechaSQL[$i] = "";
		}
		$fechaHora = explode(" ",$fechaSQL);
		$fecha=explode("-",$fechaHora[0]);
		$mesArray = array( 1 => "Enero", 2 => "Febrero", 3 => "Marzo", 4 => "Abril", 5 => "Mayo", 6 => "Junio", 7 => "Julio", 8 => "Agosto", 9 => "Septiembre", 10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre" );
		$mes=$fecha[1]*1;		
		return $fecha[2].' de '.$mesArray[$mes].' del '.$fecha[0].' a las '.$fechaHora[1];			
	}
	
	
	/*
		Función que convierte la primera palabra de una cadena en mayúscula
	*/
	function firstToUpper($var)
	{
		return strtoupper($var[0]).substr($var,1,strlen($var)-1);		
	}
	
	/*
		Función que covierte un nombre a formato de nombre propio latino, considerando nombre y apellidos compuestos como de la Fuente, de los Santos, Domínguez y Ruiz, etc.
	 */
	function nombrePropio($nombre)
	{
		// definimos un array de artículos (en minúscula)
		$articulos = array(
		'0' => 'a',
		'1' => 'de',
		'2' => 'del',
		'3' => 'la',
		'4' => 'los',
		'5' => 'las',
		'6' => 'y'
		);
	 
		// explotamos el nombre
		$palabras = explode(' ', $nombre);
	 
		// creamos la variable que contendra el nombre
		// formateado
		$nuevoNombre = '';
	 
		// parseamos cada palabra
		foreach($palabras as $elemento)
		{
			// si la palabra es un articulo
			if(in_array(trim(strtolower($elemento)), $articulos))
				{
				// concatenamos seguido de un espacio
				$nuevoNombre .= strtolower($elemento)." ";
				} else {
				// sino, es un nombre propio, por lo tanto aplicamos
				// las funciones y concatenamos seguido de un espacio
				$nuevoNombre .= ucfirst(strtolower($elemento))." ";
				}
		}
		
		if(strpos($nuevoNombre,"a ") === 0)
			$nuevoNombre = "A ".substr($nuevoNombre,2);
	 
		return trim($nuevoNombre);
	}
	
	/*
		Función que regresa el valor de una variable o un valor definido en caso de que la variable sea nula o no existente
		Similar a la función COALESCHE de SQL
	 */
	function coalesce($var,$value_if_null){
		return $var === NULL || !isset($var) ? $value_if_null : $var;	
	}
	
	/*
		Función que permite obtener una variable pasada por parámetro fácilmente, considera los parámetros GET, POST, COOKIE y REQUEST (por default, el predefinido es REQUEST que contiene los tres primeros
		Considera los paso de parámetros que necesitan pasar por la función urldecode como el caso de POST
		
		@returns: La variable solicitada o vacío en caso de no existir
	 */
	function getVar($varName, $method = "default"){
		if($method == "default")
			$method = $flag_method;
		if($method == "GET" && isset($_GET[$varName]) && $_GET[$varName] != "")
			return $_GET[$varName];
		elseif($method == "POST" && isset($_POST[$varName]) && $_POST[$varName] != "")
			return urldecode($_POST[$varName]);
		elseif($method == "COOKIE" && isset($_COOKIE[$varName]) && $_COOKIE[$varName] != "")
			return $_COOKIE[$varName];
		elseif($method == "REQUEST" && isset($_REQUEST[$varName]) && $_REQUEST[$varName] != "")
			return $_REQUEST[$varName];
		else
			return "";	
	}
	
	/*
		Permite fácilmente revisar los permisos de un usuario utilizando la variable de sesión session_permision_variable (Definir en defaults)
		
		@params: String con los permisos que se quieren revisar, se pueden utilizar solamente iniciales o la string completa y se pueden revisar varios permisos separados por espacios, por ejemplo: checkUser('D P') = checkUser('Administrador Profesor') revisaría si el usuario tiene alguno de esos permisos activos
		@returns: True/False
	*/
	function checkUser($string){
		//echo $string."****<br />";
		$arr = explode(" ",$string);
		//echo $_SESSION['hg_permisos']."***<br />";
		$sessid = $_SESSION[$session_permission_variable]; $sesstext = $permission[$sessid];
		return in_array($sessid,$arr) || in_array($sesstext,$arr);
	}
	
	/*
		Devuelve como string los permisos con que cuenta el usuario utilizando la variable de sesión session_permission_variable (Definir en default)
	 */
	function getUserString(){
		global $session_permission_variable, $permission;
		$return = $permission[$_SESSION[$session_permission_variable]];
		return $return;
	}
	
	/*
		Función que permite encriptar el paso de variables vía GET, POST o REQUEST utilizando la llave secretKey (Definir en defaults).
		Esta función debe ser utilizada en forma individual en cada una de las variables que se desean encriptar, especialmente en el caso de los IDs se obtiene un máximo funcionamiento cuando se encripta la variable directamente obtenidoa de BDD y se incrusta encriptada en el objeto HTML en cuestión, variable javascript o arreglo de datos JSON, de forma que no pueda ser manipulada posteriormente utilizando un analizador de DOM.
		Esta función es URL-Safe.
	 */
	function encurl($sData){
		$sResult = '';
		for($i=0;$i<strlen($sData);$i++){
			$sChar    = substr($sData, $i, 1);
			$sKeyChar = substr($secretKey, ($i % strlen($secretKey)) - 1, 1);
			$sChar    = chr(ord($sChar) + ord($sKeyChar));
			$sResult .= $sChar;
		}
		$sBase64 = base64_encode($sResult);
		return str_replace('=', '', strtr($sBase64, '+/', '-_'));
	}
	
	/*
		Mirror de encurl, esta función permite desencriptar los datos que han pasado encriptados vía GET, POST o REQUEST utilizando la llave secretKey (Definir en defaults)
	 */
	function decurl($sData){
		$sResult = '';
		$sBase64 = strtr($sData, '-_', '+/');
		$sData = base64_decode($sBase64.'==');
		for($i=0;$i<strlen($sData);$i++){
			$sChar    = substr($sData, $i, 1);
			$sKeyChar = substr($secretKey, ($i % strlen($secretKey)) - 1, 1);
			$sChar    = chr(ord($sChar) - ord($sKeyChar));
			$sResult .= $sChar;
		}
		return $sResult;
	}
	
	/*
		Convierte una función en inglés a español en lo correspondiente al nombre del mes
	*/
	function dateToSpanish($str_date){
		$arr_search = array(
			"monday",
			"tuesday",
			"wednesday",
			"thursday",
			"friday",
			"saturday",
			"sunday",
			"january",
			"february",
			"march",
			"april",
			"may",
			"june",
			"july",
			"august",
			"september",
			"october",
			"november",
			"december"
		);
		
		$arr_replace = array(
			"Lunes",
			"Martes",
			"Miércoles",
			"Jueves",
			"Viernes",
			"Sábado",
			"Domingo",
			"Enero",
			"Febrero",
			"Marzo",
			"Abril",
			"Mayo",
			"Junio",
			"Julio",
			"Agosto",
			"Septiembre",
			"Octubre",
			"Noviembre",
			"Diciembre"
		);
		
		return str_ireplace($arr_search,$arr_replace,$str_date);
	}
	
	/*
		Regresa la URL de la página actual
	 */
	function curPageURL() {
		 $pageURL = 'http';
		 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		 $pageURL .= "://";
		 if ($_SERVER["SERVER_PORT"] != "80") {
		  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		 } else {
		  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		 }
		 return $pageURL . substr($_SERVER['PHP_SELF'],1);
	}
	
	/*
		Función para conversión de cadenas a 0 cuando se manipulan strings numéricas
	 */
	function nullToZero($var)
	{
		if($var == "")
			return 0;
		else
			return $var;	
	}
	
	/*
		Función que convierte un número dado a formato de moneda, considerando el símbolo de dinero y el formato financiero
	 */
	function currency($str, $symbol = "$"){
		if($str >= 0)
			return $symbol.number_format(round($str,2),2,'.',',');
		else
			return "-".$symbol.number_format(round($str * -1,2),2,'.',',');
	}
	
	/*
		Función que se asegura que una cadena de texto se divide en fragmentos separados por caracteres wrapped y non_wrapped para caber exactamente en un contenedor de tamaño definido, especialmente últil cuando el wrap default de HTML corta palabras que no desean ser cortadas, esta función puede sustutuir los espacios intermedios por el símbolo &nbsp; y evitar el wrapping
	 */
	function unwrap_text($string, $wrap_length, $when_wrapped = " ", $when_not_wrapped = "&nbsp;"){
		$str = str_word_count($string,1,'áéíóúÁÉÍÓÚñÑüÜ¡¿0123456789');
		$return = "";
		$length = 0;
		foreach($str as $s){
			if($length + strlen($s) < $wrap_length){
				$return .= $when_not_wrapped.$s;
				$length += strlen($s);	
			}
			else{
				$return .= $when_wrapped.$s;
				$length = strlen($s);	
			}
		}
		return substr($return,strlen($when_not_wrapped));
	}
	/*******************************************************************************************************************
		
		FIN FUNCIONES
		
	*******************************************************************************************************************/
?>