<?php  

/*
	Class property of MovIT Business Everywhere (R)
	@autor:	José Eugenio Hernández Roldán
	
	Description: the MySQL class provides connection between PHP and MySQL Database, it also simplifies the PHP functions charged with 		MySQL databases such as mysql_connect, mysql_select_db, mysql_query, mysql_error, mysql_free_result, mysql_fetch_array, mysql_insert_id, mysql_affected_rows y mysql_num_rows. In order to do so, the class buffers in memory one query string and one result set to lighten the task of keeping a pointer to the current ResultSet normally entrusted to the user.
	MySQL class also provides several support functions to obtain more specific values from a database, perform quick validations or control the results obtained.
	
	Class includes protection against SQL Injection by using the mysql_real_escape_string incorporated in the squery function.
	
	:::::::::::::::::::::::::::::::::::: IMPORTANT ::::::::::::::::::::::::::::::::::::
	::  For security purposes, the most cases should use squery function instead of  ::
	:: query, this function allows the query as param with the wildcard(s) !Q%, and  ::
	::  an array containing the variables that will be placed in the wildcard(s) in  ::
	::     order of occurrence after being safe-checked for the secure function      ::
	:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
	
	v1.1	Code 02.2010
	v1.2	Code 06.2011
	v1.3	Code 08.2011	Added secure query functions
	v1.4	Code 10.2011	Added debug & log functions
	v1.6	Code 02.2012	Fixed setquery return value
	v1.8	Code 04.2012	Added function properties
	v1.9	Code 05.2012	Fixed log functions with chmod like options, added log functions
	
	v2.0	Code 06.2012	Added comment support (possible incompatibility with older versions), add **toJson method**, change debug & error format
	v2.1	Code 08.2012	Added startTrans, endTrans, & query_matrix methods
	v2.2	Code 02-2013	Added rollTrans & query_varray methods
	v2.3	Code 03-2013	Standarization of methods to the format setquery, queryid, starttrans, ... (Veracruz' style) except the constructor MySQL
	v2.4	Code 11-2013	Added lasterror, UPGRADED TO MySQLi functions to avoid deprecated functions as from PHP5.0.5
 */ 

class MySQL{ 

	/* BDD connection strings												*/
	 /* BDD Host */			private $host 				= "localhost"				 
	;/* BDD User */			private $user 				= "movitbec_myfitne"				 
	;/* BDD Password */		private $pssw 				= "1qazxsw2"						 
	;/* BDD name */			private $dbnm 				= "movitbec_myfitner"					

	/*	MySQL warnings & errors are shown?									*/
	;private $showErrors 								= true						
	/*	Class halt on error?												*/
	;private $closeOnError 								= false							
	/*	Debug ALL queries (print)											*/
	;private $debug 									= false						
	/*	mysql_connect Object with connection params							*/
	;private $connection														 
	/*	Counter of queries performed by the class in the current session	*/
	;private $query_total													
	/*	Query string buffered												*/
	;private $query															
	/*	mysql_query Object [ResultSet] buffered								*/
	;private $resultset	
	/*	Comment on the current query										*/
	;private $comment
	/*	Keep trace of error for transactions								*/
	;public $transaction								= true	
	;										
	
	/*									--LOGGING--																
	
	:::::::::::::::::::::::::::::::::::: IMPORTANT ::::::::::::::::::::::::::::::::::::
	::   In order to activate the logging function the configuration for the next    ::
	:: variables. Please set the log flag TRUE to enable de function. Please set the ::
	:: log_table variable to the name of the relation inside de database which will  ::
	::   keep the log records, each one containing the query executed, the date of   ::
	:: execution, the id user responsible (optional) and the error returned (if any) ::
	::		Default: MySQL_log(query,date,user,error)								 ::
	::																				 ::
	::		Please do not modify the log_user variable     							 ::
	::																				 ::
	:: When instanciated, the object may recieve the user id as a param for logging  ::
	:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
																			*/
	private $log									= false						
	/*	Log tablename														*/
	;private $log_table								= "MySQL_log"				
	/*	Log tablename														*/
	;private $log_user								= "''"						
	/*	Log options		INSERT | DELETE | UPDATE | SELECT (chmod like)		*/
	;private $log_options							= "1110"
	;
	
	/*
		Class constructor. Uses primitive methods mysqli_connect, mysqli_error
		
		$user [optional]	User id for logging, if missing no user is linked to the log
		
		return void
	 */
	 
	public function MySQL($user = "''"){
		$this->log_user = "'".$user."'"; 
		if(!isset($this->connection)){
			$this->connection = ($this->showErrors ? mysqli_connect($this->host,$this->user,$this->pssw,$this->dbnm) : @mysqli_connect($this->host,$this->user,$this->pssw,$this->dbnm)) or die($this->showErrors ? mysqli_error($this->connection) : @mysqli_error($this->connection));
			mysqli_query($this->connection,"SET NAMES 'utf8'");
			mysqli_query($this->connection,"SET group_concat_max_len = 9999");
		}  
	}
	
	/*
		Function that prints the connection' parameters as a formatted string (warning)

		return void
	 */
	public function properties(){
		echo "<span style=\"background-color:#FFF;color:red\"><strong>Host:</strong>".$this->host."&nbsp;<strong>DB:</strong>".$this->dbnm."&nbsp;<strong>User:</strong>".$this->user."&nbsp;<strong>Password:</strong>".$this->pssw."</span>";	
	}
	
	/*
		Function that sets the debug state for the class

		$d	[optional]	New debug state [default true]
		
		return void
	 */
	public function setdebug($d = true){
		$this->debug = $d;
	}
	
	/*
		Function that sets the log state for the class

		$d	[optional]	New log state [default true]
		
		return void
	 */
	public function setlog($d = true){
		$this->log = $d;
	}
	
	/*
		Function that sets the log options for the class

		$ch	[optional]	New log options [default 1110]
		
		return void
	 */
	public function logoptions($ch = "1110"){
		$this->log_options = $ch;
	}

	/*
		Function that returns the buffered query

		return string, Buffered query
	 */
	public function getquery(){
		return $this->query;
	}
	
	/*
		Function that prints the buffered query
		
		return void, Prints the buffered query
	 */
	public function printquery(){
		echo $this->query.";";
	}
	
	/*
		Function that starts MySQL transaction
		
		return void, Initiates the variable
	 */
	public function starttrans(){
		$this->transaction = true;
		$this->query("START TRANSACTION");
	}
	
	/*
		Function that ends MySQL transaction
		
		return true if transaction was completed (COMMIT) or false if transaction was rejected (ROLLBACK)
	 */
	public function endtrans(){
		if($this->transaction){
			$this->query("COMMIT");
			return true;
		}
		else{
			$this->query("ROLLBACK");
			return false;
		}
	}
	
	/*
		Function that rollbacks a MySQL transaction
		
		return boolean, the value of the variable transaction that indicates if an error had occured since the last startTrans call
	 */
	public function rolltrans(){
		$this->query("ROLLBACK");
		return $this->transaction;
	}
	
	/*
		Function that secures a string against SQL injection. Uses primitive method mysql_real_escape_string which prepends the backslash (\) to characters \x00, \n, \r, \, ', ", \x1a.
		
		$data [optional]	String to secure (string <> NONEE), if missing an empty string is used instead
		
		return string, Secured string
	 */
	public function secure($data = "NONEE"){
		if($data === "NONEE")
			return "";
		return $this->showErrors ? mysqli_real_escape_string($this->connection,$data) : @mysqli_real_escape_string($this->connection,$data);
	}
	
	/*
		Function that secures a string against SQL injection given a specific set of parameters
		
		$str [optional]	String to secure (str <> NONEE), if missing, an empty is used instead			

		return Protected string
	 */ 
	public function securestring($str = "NONEE", $data_array = array()){
		if($str !== "NONEE"){
			$count = count($data_array);
			for($i = 0; $i < $count; $i++){
				$str = preg_replace('/!Q%/',$this->secure($data_array[$i]), $str, 1);
			}
			return $str;
		}
		else
			return "";
	}
	
	/*
		Function that sets the comment
		
		$str	New comment			

		return void
	 */ 
	public function comm($c){
		$this->comment = $c;
	}
	
	/*
		Function that returns the last error ocurred as string
		
		return Last error
	 */
	public function lasterror(){
		return mysqli_error($this->connection);	
	}

	/*
		Function that executes a query. Uses primitive methods mysql_query & mysql_error
		
		$query [optional]	Query to execute (string <> NONEE), if missing the buffered query is used instead	

		return ResultSet, ResultSet of the execution
	 */ 
	public function query($query = "NONEE"){
		
		if($update = $query === "NONEE")
			$query = $this->query; 					
		
		$this->query_total++;
		
		$query_check = trim($query);
		
		if($this->debug){
			echo "<br/><strong style=\"color:#E4C100\">&lt;DEBUG&gt;</strong><br/>";
			echo $this->comment != "" ? "<span style=\"color:#D3C26E;font-style:italic\">#".$this->comment."</span><br/>" : "";
			echo "<span style=\"color:#E4C100\">".$query."</span><br/><strong style=\"color:#E4C100\">&lt;/DEBUG&gt;</strong><br/>";
		}
		
		$resultset = $this->showErrors ? mysqli_query($this->connection,$query) : @mysqli_query($this->connection,$query);
		
		$proceed_log = $this->log && ( 
		(stripos($query_check,"INSERT") === 0 && $this->log_options{0} === '1')
		||
		(stripos($query_check,"DELETE") === 0 && $this->log_options{1} === '1')
		||
		(stripos($query_check,"UPDATE") === 0 && $this->log_options{2} === '1')
		||
		(stripos($query_check,"SELECT") === 0 && $this->log_options{3} === '1'));
		
		if(stripos($query_check,"INSERT") === 0 || stripos($query_check,"DELETE") === 0 || stripos($query_check,"UPDATE") === 0)
			if(!$resultset)
				$this->transaction = false;
		
		if($proceed_log){
			$log_fields = "query,date,user";
			$log_values="'".$this->secure($query)."',NOW(),".$this->log_user;
		}
		
		if(!$resultset){
			if($this->showErrors){ 
				echo "<br/><strong style=\"color:red\">&lt;QERROR&gt;</strong><br/>";
				echo $this->comment != "" ? "<span style=\"color:#E88888;font-style:italic\">#".$this->comment."</span><br/>" : $this->comment."nada";
				echo "<span style=\"color:red\">".mysqli_error($this->connection)." at ".$query_check."</span><br/><strong style=\"color:red\">&lt;/QERROR&gt;</strong><br/>";
			}
			if($proceed_log){
				$log_fields .= ",error";
				$log_values .= ",'".mysqli_error($this->connection)."'";	
			}
		}
		
		if($proceed_log){			
			@mysqli_query($this->connection,"INSERT INTO ".$this->log_table."($log_fields) VALUES($log_values)");
		}
		
		if(!$resultset && $this->closeOnError) 
			exit;
		
		if($update)
			$this->resultset = $resultset;
		
		return $resultset; 
	}
	
	/*
		Function that executes a secure query
		
		$query [optional]		Query to execute (string <> NONEE), if missing the buffered query is used instead. This method allows the use of wildcard !Q% which in execution would be replaced by the secured parameters, if missing the buffered query is used instead
		$data_array [optional]	Array with parameters, these parameters are placed in the query after being checked by secure() [default empty array]

		return ResultSet, ResultSet of the excecution
	 */ 
	public function squery($query = "NONEE", $data_array = array()){
		if($query === "NONEE")
			$query = $this->query;		
		return $this->query($this->securestring($query,$data_array));
	}
	
	/*
		Function that sets the buffered query
		
		$c						Comment for the query at hand
		$query					Query to buffer (string)
		$data_array [optional]	Array with parameters, these parameters are placed in the query after being checked by secure() [default empty array]
		$run [optional]			Allow the query to be executed instantly (see method query) [default true]

		return void if run is false or the resultSet if run is true
	 */	 
	public function setquery($c = "No comment!", $query = "NONEE", $data_array = array(), $run = true){
		$this->comment = $c;
		$query = $this->securestring($query,$data_array);
		$this->query = $query;
		
		if($run)
			return $this->resultset = $this->query($query);
	}

	/*
		Function that returns the first row of a ResultSet as an associative mixed array
		This function is better used when the ResultSet contains only one row
		
		$query [optional]		Query to be excecuted (string <> NONEE), if missing the buffered query is used instead
		$data_array [optional]	Array with parameters, these parameters are placed in the query after being checked by secure() [default empty array]
		$buffer [optional]		FLAG which indicates if the query would be buffered [default false]
		
		return array, Array with the first (only) row in the ResultSet
	 */
	public function qarray($c = "No comment!", $query = "NONEE", $data_array = array(), $buffer = false){
		$result1;
		if($buffer)	{		
			$this->setquery($c,$query, $data_array, true);
			return $this->fetch($this->resultset);					
		}else{
			$this->comment = $c;
			$result1 = $this->squery($query, $data_array);
			return $this->fetch($result1);
		}
	}

	/*
		Function that returns the first element of the first row of a ResultSet as a variable
		This function is better used when the ResultSet contains only one row and one column
		
		$query [optional]		Query to be excecuted (string <> NONEE), if missing the buffered query is used instead
		$data_array [optional]	Array with parameters, these parameters are placed in the query after being checked by secure() [default empty array]
		$buffer [optional]		FLAG which indicates if the query would be buffered [default false]
		
		return variable, Variable with the first (only) element of the first (only) in the ResultSet
	 */
	public function qvalue($c = "No comment!", $query = "NONEE", $data_array = array(), $buffer = false){
		$arr = $this->qarray($c, $query, $data_array, $buffer);
		return $arr[0];
	}
	
	/*
		Function that returns the ResultSet of a query as an associative mixed array
		
		$query [optional]		Query to be excecuted (string <> NONEE), if missing the buffered query is used instead
		$data_array [optional]	Array with parameters, these parameters are placed in the query after being checked by secure() [default empty array]
		$buffer [optional]		FLAG which indicates if the query would be buffered [default false]
		
		return array, Array with the ResultSet values
	 */
	public function qmatrix($c = "No comment!", $query = "NONEE", $data_array = array(), $buffer = false){
		$temp = "";
		if($buffer){
			$temp = $this->setquery($c,$query,$data_array,$buffer);	
		}
		else{
			$this->comm($c);
			$temp = $db->squery($query,$data_array);	
		}
		
		$returnArray = array();
		while($arr = $this->fetch($temp)){
			array_push($returnArray,$arr);
		}
		
		return $returnArray;
	}
	
	/*
		Function that returns the first field of a ResultSet as a vertical array
		
		$query [optional]		Query to be excecuted (string <> NONEE), if missing the buffered query is used instead
		$data_array [optional]	Array with parameters, these parameters are placed in the query after being checked by secure() [default empty array]
		$buffer [optional]		FLAG which indicates if the query would be buffered [default false]
		
		return array, Array with the Vertical Array
	 */
	public function qvarray($c = "No comment!", $query = "NONEE", $data_array = array(), $buffer = false){
		$temp = "";
		if($buffer){
			$temp = $this->setquery($c,$query,$data_array,$buffer);	
		}
		else{
			$this->comm($c);
			$temp = $db->squery($query,$data_array);	
		}
		
		$returnArray = array();
		while($arr = $this->fetch($temp)){
			array_push($returnArray,$arr[0]);
		}
		
		return $returnArray;
	}
	
	/*
		Function that returns the ID (primary key) of the last insertion. Uses primitive method mysql_insert_id

		return var, ID (primary key) of the last query identified with "INSERT"
	 */
	public function qid(){
		return $resultset = $this->showErrors ? mysqli_insert_id($this->connection) : @mysqli_insert_id($this->connection);
	}

	/*
		Function that returns the next row to be taken from the ResultSet. Uses primitive method mysql_fetch_array

		$resultset [optional]	ResultSet (var <> NONEE) to be fetched, if missing the buffered ResultSet is used instead

		return array, next row taken from the ResultSet
	 */
	public function fetch($resultset = "NONEE"){
		if($resultset === "NONEE")
			$resultset = $this->resultset;
		return $resultset = $this->showErrors ? mysqli_fetch_array($resultset) : @mysqli_fetch_array($resultset); 
	}

	/*
		Function that counts the number of rows obtained in a ResultSet. Uses primitive method mysql_num_rows

		$resultset [optional]	ResultSet (var <> NONEE) to analyze, if missing the buffered ResultSet is used instead

		return int, number of rows in the ResultSet
	 */
	public function rows($resultset = "NONEE"){
		if($resultset === "NONEE")
			$resultset = $this->resultset; 
		return $resultset = $this->showErrors ? mysqli_num_rows($resultset) : @mysqli_num_rows($resultset); 
	}
	
	/*
		Function that counts the number of rows obtained in a ResultSet. Uses primitive method mysql_num_rows

		$query [optional]	Query (var <> NONEE) to analyze, if missing the buffered query is used instead

		return int, number of rows retrieved by the query
	 */
	public function rowsdirect($c = "No comment!", $query = "NONEE"){
		$this->comment = $c;
		if($query === "NONEE")
			$query = $this->query; 
		$resultSet =  $this->query($query);		
		return $resultset = $this->showErrors ? mysqli_num_rows($resultSet) : @mysqli_num_rows($resultSet);  
	}

	/*
		Function that returns the number of records affected by the last execution. Uses primitive method mysql_affected_rows

		return int, number of records affected
	 */
	public function affected(){ 
		return $resultset = $this->showErrors ? mysqli_affected_rows($this->connection) : @mysqli_affected_rows($this->connection); 
	}

	/*
		Function that returns the number of queries executed by the specific instance of the class

		return int, number of executions
	 */
	public function countquery(){ 
		return $this->query_total;  
	}

	/*
		Function that frees one ResultSet. Uses primitive method mysql_free_result

		$resultset [optional] ResultSet (var <> NONEE) to be freed, if missing the buffered ResultSet is used instead

		return bool, function success
	 */
	public function free($resultset = "NONEE"){
		if($resultset === "NONEE")
			$resultset = $this->resultset;
		$resultset = $this->showErrors ? mysqli_free_result($resultset) : @mysqli_free_result($resultset); 
	}
	
	/*
	 *	COMPATIBILITY METHODS
	 *
	 *	Starting from version 2.3 all methods where standarized to the lowercase-joined-words format (example setquery, queryid, start), these methods use the
	 *	old (and deprecated) callings, is highly recommend to use the new version method's name instead of these ones which will eventually disappear and produce
	 *	incompatibility among versions
	 *
	 */
	 
	public function setlog_options($ch = "1110"){
		return $this->logoptions($ch);
	}
	
	public function secure_string($str = "NONEE", $data_array = array()){
		return $this->securestring($str,$data_array);
	}
	
	public function query_array($c = "No comment!", $query = "NONEE", $data_array = array(), $buffer = false){
		return $this->qarray($c,$query,$data_array,$buffer);
	}
	
	public function query_value($c = "No comment!", $query = "NONEE", $data_array = array(), $buffer = false){
		return $this->qvalue($c,$query,$data_array,$buffer);
	}
	
	public function query_matrix($c = "No comment!", $query = "NONEE", $data_array = array(), $buffer = false){
		return $this->qmatrix($c,$query,$data_array,$buffer);
	}
	
	public function query_varray($c = "No comment!", $query = "NONEE", $data_array = array(), $buffer = false){
		return $this->qvarray($c,$query,$data_array,$buffer);
	}
	
	public function query_id(){
		return $this->qid();	
	}
	
	public function num_rows($resultset = "NONEE"){
		return $this->rows($resultset);	
	}
	
	public function num_rows_direct($c = "No comment!", $query = "NONEE"){
		return $this->rowsdirect($c,$query);	
	}
	
	public function affected_rows(){ 
		return $this->affected();
	}
	
	public function count_query(){
		return $this->countquery();	
	}
}

?>