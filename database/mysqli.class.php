<?php
	
	class DataBaseMysqli
	{
		var $location = "";
		var $name = "";
		var $user = "";
		var $pass = "";
		var $conn_id;
		var $connected = false;
		var $db;
		var $error;
		var $errno;

		function __construct()
		{
			global $conf;

			$this->user = $conf->db->user;
			$this->pass = $conf->db->pass;
			$this->name = $conf->db->name;
			$this->location = $conf->db->location;

			$this->connect();
		}

		/*
		 * Connect's to a DataBase
		 */
		function connect()
		{
			$this->db = new mysqli($this->location, $this->user, $this->pass, 'test');
			if ($this->db->connect_errno) 
			{
				 die("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
			}
			else
			{
				//$this->run_sql_file(DOCUMENT_ROOT.ROOT.'/sql/database.sql');
				$this->db->select_db('heimstatusDB');

				/* return name of current default database */
				if ($result = $this->db->query("SELECT DATABASE()")) {
				    $row = $result->fetch_row();
				   	$row[0];
				}
				if($row[0] == 'heimstatusDB')
				{
					$this->connected=true;
				}
			}
		}

		function run_sql_file($location){
		    //load file
		    $commands = file_get_contents($location);

		    //delete comments
		    $lines = explode("\n",$commands);
		    $commands = '';
		    foreach($lines as $line){
		        $line = trim($line);
		        if( $line && !$this->startsWith($line,'--') ){
		            $commands .= $line . "\n";
		        }
		    }

		    //convert to array
		    $commands = explode(";", $commands);

		    //run commands
		    $total = $success = 0;
		    foreach($commands as $command){
		        if(trim($command)){
		            $success += ($this->db->query($command)==false ? 0 : 1);
		            $total += 1;
		        }
		    }

		    //return number of successful queries and total number of queries found
		    return array(
		        "success" => $success,
		        "total" => $total
		    );
		}


		// Here's a startsWith function
		function startsWith($haystack, $needle){
		    $length = strlen($needle);
		    return (substr($haystack, 0, $length) === $needle);
		}

		function close()
    	{
        	if ($this->db)
        	{
         		$this->connected=false;
		    	return $this->db->close();
		    }
		    return false;
		}


	    /**
	     * 	Execute a SQL request and return the resultset
	     *
	     * 	@param	string	$query			SQL query string
	     * 	@param	int		$usesavepoint	0=Default mode, 1=Run a savepoint before and a rollbock to savepoint if error (this allow to have some request with errors inside global transactions).
	     * 									Note that with Mysql, this parameter is not used as Myssql can already commit a transaction even if one request is in error, without using savepoints.
	     *  @param  string	$type           Type of SQL order ('ddl' for insert, update, select, delete or 'dml' for create, alter...)
	     *	@return	bool|mysqli_result		Resultset of answer
	     */
	    function query($query,$usesavepoint=0,$type='auto')
	    {
	    	global $conf;
	        $query = trim($query);

	        $ret = $this->db->query($query);
	        
	        if (! preg_match("/^COMMIT/i",$query) && ! preg_match("/^ROLLBACK/i",$query))
	        {
	            // Si requete utilisateur, on la sauvegarde ainsi que son resultset
	            if (! $ret)
	            {
	                $this->lastqueryerror = $query;
	                $this->lasterror = $this->error();
	                $this->lasterrno = $this->errno();
					//if ($conf->global->SYSLOG_LEVEL < LOG_DEBUG) dol_syslog(get_class($this)."::query SQL Error query: ".$query, LOG_ERR);	// Log of request was not yet done previously
	                //dol_syslog(get_class($this)."::query SQL Error message: ".$this->lasterrno." ".$this->lasterror, LOG_ERR);
	            }
	            $this->lastquery=$query;
	            $this->_results = $ret;
	        }
	        return $ret;
	    }

	    /**
	     *	Renvoie la ligne courante (comme un objet) pour le curseur resultset
	     *
	     *	@param	mysqli_result	$resultset	Curseur de la requete voulue
	     *	@return	object|null					Object result line or null if KO or end of cursor
	     */
	    function fetch_object($resultset)
	    {
	        // Si le resultset n'est pas fourni, on prend le dernier utilise sur cette connexion
	        if (! is_object($resultset)) { $resultset=$this->_results; }
			return $resultset->fetch_object();
	    }

	    /**
	     *	Return datas as an array
	     *
	     *	@param	mysqli_result	$resultset	Resultset of request
	     *	@return	array|null|0				Array or null if KO or end of cursor or 0 if resultset is bool
	     */
	    function fetch_row($resultset)
	    {
	        // If resultset not provided, we take the last used by connexion
	        if (! is_bool($resultset))
	        {
	            if (! is_object($resultset)) { $resultset=$this->_results; }
	            return $resultset->fetch_row();
	        }
	        else
	        {
	            // si le curseur est un booleen on retourne la valeur 0
	            return 0;
	        }
	    }


	    /**
	     *	Return number of lines for result of a SELECT
	     *
	     *	@param	mysqli_result	$resultset  Resulset of requests
	     *	@return	int				Nb of lines
	     *	@see    affected_rows
	     */
	    function num_rows($resultset)
	    {
	        // If resultset not provided, we take the last used by connexion
	        if (! is_object($resultset)) { $resultset=$this->_results; }
	        return $resultset->num_rows;
	    }


		/**
		 *	Return generic error code of last operation.
		 *
		 *	@return	string		Error code (Exemples: DB_ERROR_TABLE_ALREADY_EXISTS, DB_ERROR_RECORD_ALREADY_EXISTS...)
		 */
		function errno()
		{
		    if (! $this->connected) {
		        // Si il y a eu echec de connexion, $this->db n'est pas valide.
		        return 'DB_ERROR_FAILED_TO_CONNECT';
		    } else {
		        // Constants to convert a MySql error code to a generic Dolibarr error code
		        $errorcode_map = array(
		        1004 => 'DB_ERROR_CANNOT_CREATE',
		        1005 => 'DB_ERROR_CANNOT_CREATE',
		        1006 => 'DB_ERROR_CANNOT_CREATE',
		        1007 => 'DB_ERROR_ALREADY_EXISTS',
		        1008 => 'DB_ERROR_CANNOT_DROP',
		        1022 => 'DB_ERROR_KEY_NAME_ALREADY_EXISTS',
		        1025 => 'DB_ERROR_NO_FOREIGN_KEY_TO_DROP',
		        1044 => 'DB_ERROR_ACCESSDENIED',
		        1046 => 'DB_ERROR_NODBSELECTED',
		        1048 => 'DB_ERROR_CONSTRAINT',
		        1050 => 'DB_ERROR_TABLE_ALREADY_EXISTS',
		        1051 => 'DB_ERROR_NOSUCHTABLE',
		        1054 => 'DB_ERROR_NOSUCHFIELD',
		        1060 => 'DB_ERROR_COLUMN_ALREADY_EXISTS',
		        1061 => 'DB_ERROR_KEY_NAME_ALREADY_EXISTS',
		        1062 => 'DB_ERROR_RECORD_ALREADY_EXISTS',
		        1064 => 'DB_ERROR_SYNTAX',
		        1068 => 'DB_ERROR_PRIMARY_KEY_ALREADY_EXISTS',
		        1075 => 'DB_ERROR_CANT_DROP_PRIMARY_KEY',
		        1091 => 'DB_ERROR_NOSUCHFIELD',
		        1100 => 'DB_ERROR_NOT_LOCKED',
		        1136 => 'DB_ERROR_VALUE_COUNT_ON_ROW',
		        1146 => 'DB_ERROR_NOSUCHTABLE',
		        1216 => 'DB_ERROR_NO_PARENT',
		        1217 => 'DB_ERROR_CHILD_EXISTS',
		        1396 => 'DB_ERROR_USER_ALREADY_EXISTS',    // When creating user already existing
		        1451 => 'DB_ERROR_CHILD_EXISTS'
		        );
		        if (isset($errorcode_map[$this->db->errno])) {
		            return $errorcode_map[$this->db->errno];
		        }
		        $errno=$this->db->errno;
		        return ($errno?'DB_ERROR_'.$errno:'0');
		    }
		}

		/**
		 *	Return description of last error
		 *
		 *	@return	string		Error text
		 */
		function error()
		{
		    if (! $this->connected) {
		        // Si il y a eu echec de connexion, $this->db n'est pas valide pour mysqli_error.
		        return 'Not connected. Check setup parameters in conf/conf.php file and your mysql client and server versions';
		    }
		    else {
		        return $this->db->error;
		    }
		}

	    /**
		 * Get last ID after an insert INSERT
		 *
		 * @param   string	$tab    	Table name concerned by insert. Ne sert pas sous MySql mais requis pour compatibilite avec Postgresql
		 * @param	string	$fieldid	Field name
		 * @return  int|string			Id of row
	     */
	    function last_insert_id($tab,$fieldid='rowid')
	    {
	        return $this->db->insert_id;
	    }

	    /**
	     *	Encrypt sensitive data in database
	     *  Warning: This function includes the escape, so it must use direct value
	     *
	     *	@param	string	$fieldorvalue	Field name or value to encrypt
	     * 	@param	int		$withQuotes		Return string with quotes
	     * 	@return	string					XXX(field) or XXX('value') or field or 'value'
	     *
	     */
	    function encrypt($fieldorvalue, $withQuotes=0)
	    {
	        global $conf;
	        // Type of encryption (2: AES (recommended), 1: DES , 0: no encryption)
	        $cryptType = (!empty($conf->db->dolibarr_main_db_encryption)?$conf->db->dolibarr_main_db_encryption:0);
	        //Encryption key
	        $cryptKey = (!empty($conf->db->dolibarr_main_db_cryptkey)?$conf->db->dolibarr_main_db_cryptkey:'');
	        $return = ($withQuotes?"'":"").$this->escape($fieldorvalue).($withQuotes?"'":"");
	        if ($cryptType && !empty($cryptKey))
	        {
	            if ($cryptType == 2)
	            {
	                $return = 'AES_ENCRYPT('.$return.',\''.$cryptKey.'\')';
	            }
	            else if ($cryptType == 1)
	            {
	                $return = 'DES_ENCRYPT('.$return.',\''.$cryptKey.'\')';
	            }
	        }
	        return $return;
	    }
	    /**
	     *	Decrypt sensitive data in database
	     *
	     *	@param	string	$value			Value to decrypt
	     * 	@return	string					Decrypted value if used
	     */
	    function decrypt($value)
	    {
	        global $conf;
	        // Type of encryption (2: AES (recommended), 1: DES , 0: no encryption)
	        $cryptType = (!empty($conf->db->dolibarr_main_db_encryption)?$conf->db->dolibarr_main_db_encryption:0);
	        //Encryption key
	        $cryptKey = (!empty($conf->db->dolibarr_main_db_cryptkey)?$conf->db->dolibarr_main_db_cryptkey:'');
	        $return = $value;
	        if ($cryptType && !empty($cryptKey))
	        {
	            if ($cryptType == 2)
	            {
	                $return = 'AES_DECRYPT('.$value.',\''.$cryptKey.'\')';
	            }
	            else if ($cryptType == 1)
	            {
	                $return = 'DES_DECRYPT('.$value.',\''.$cryptKey.'\')';
	            }
	        }
	        return $return;
	    }

	}

?>