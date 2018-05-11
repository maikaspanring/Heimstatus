<?php
	/*
	 * Main Libaray
	 */
	//die(print_r($_SERVER));
	set_include_path($_SERVER['DOCUMENT_ROOT'] . '/Heim-Status/');

	require_once 'conf/conf.php';
	require_once 'database/mysqli.class.php';

	define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);
	define('PHP_SELF', $_SERVER['PHP_SELF']);
	define('ROOT','/Heim-Status/');
	define('MAIN_ROOT', $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].':81/Heim-Status');

	$db = new DataBaseMysqli();

	session_start();

	if(!empty(GETPOST("login") ))
	{
		$login = md5(GETPOST("loginname"));
		$pass = md5(GETPOST("loginpass"));

		$sql = "SELECT * FROM `llx_user` ";
		$sql.= " Where md5(name) = '".$login."' ";
		$sql.= " And password_crypt = '".$pass."' ";
		$req = $db->query($sql);
		if($req)
		{
			if($res = $db->fetch_object($sql))
			{
				/*session is started if you don't write this line can't use $_Session  global variable*/
				$_SESSION["user"] = $res->name;
				$_SESSION["login_date"] = strtotime('now');
				header("Refresh:0");
				// unset($_SESSION["user"]); to delete
			}
			else
			{
				die("Wrong Pass or else");
			}
		}
		else
		{
			die("Wrong DB");
		}
	}


/**
	CHECK USER LOGIN	
*/

	if(!empty($_SESSION["user"]))
	{
		if($_SESSION["login_date"] < strtotime('now - 15 min.'))
		{
			$logout = true;
		}
	}

	if(!empty(GETPOST("logout")) || !empty($logout))
	{
		unset($_SESSION["user"]);
		unset($_SESSION["login_cert"]);
	}

	$sql = "SELECT * FROM `llx_conf` ";
	$req = $db->query($sql);
	if(!$req)
	{
		$suc = $db->run_sql_file('sql/heimstatusDB.sql');
		if($suc)
		{
			print 'Database Installed';
		}
		else
		{
			print 'Database can\'t installed';
		}
	}

	function main_header($error_l = E_ALL)
	{
		global $conf, $db;
		error_reporting($error_l);

		if(empty(GETPOST('heim'))) $_POST['heim'] = "H1";
		if(empty(GETPOST('stockwerk'))) $_POST['stockwerk'] = "SE";

		print '<html>';
			print '<head>';
				print '<meta charset="utf-8">';
				print '<link rel="stylesheet" type="text/css" href="css/main.theme.css">';
				print '<script src="js/jquery-2.2.0.min.js"></script>'; // jquery-2.2.0.min.js
				print '<script src="js/d3.js"></script>'; // jquery-2.2.0.min.js
				print '<script src="js/page_loader.js"></script>'; // jquery-2.2.0.min.js
				print '<script src="js/login.js"></script>'; // jquery-2.2.0.min.js
				print '<link rel="stylesheet" type="text/css" href="include/plugin/jquery-clockpicker.min.css">';
				print '<script src="include/plugin/jquery-clockpicker.min.js"></script>';
			print '</head>';
			print '<body style="margin:0px;">';
				print '<div id="header" style="clear:both;">';
					print '<img src="img/CybotPP5.png" class="logo">';
					print '<input type="hidden" id="self" name="self" value="'.$_SERVER['PHP_SELF'].'">';
					print '<input type="hidden" id="heim" name="heim" value="'.GETPOST('heim').'">';
					print '<input type="hidden" id="Stockwerk" name="Stockwerk" value="'.GETPOST('stockwerk').'">';
					print '<span style="float: left; color: #fff; font-size: 31px; font-family: initial; margin-right: 8px; margin-top: 6px;">|</span>';
					print '<table style="color:#ffffff; padding-top: 6px;">';
						print '<tr>';
							print '<td id="ck_plan" class="main_tab" style="background-color:BBBBBB;color:#000000;margin-right: 8px;">';
								print 'Plan';
							print '</td>';
							print '<td id="ck_table" class="main_tab">';
								print 'Tabelle';
							print '</td>';
							/*print '<td id="map_tab" class="main_tab">';
								print 'Karten';
							print '</td>';*/
						print '</tr>';
					print '</table>';

					print '<div class="login" id="login">';
						if(empty($_SESSION["user"])) 
						{
							print 'Login';
						}
						else
						{
							print $_SESSION["user"]; // .' '.date('Y-m-d H:i:s', $_SESSION["login_date"])
						}
					print '</div>';

				print '</div>';

			//require_once 'login.php';
	}

	function main_footer()
	{
		global $conf, $db;
				print '<div id="footer">';
					print '';
				print '</div>';
			print '</body>';
		print '</html>';
	}

		
	/**
	 *  Return value of a param into GET or POST supervariable
	 *
	 *  @param	string	$paramname   Name of parameter to found
	 *  @param	string	$check	     Type of check (''=no check,  'int'=check it's numeric, 'alpha'=check it's text and sign, 'aZ'=check it's a-z only, 'array'=check it's array, 'san_alpha'=Use filter_var with FILTER_SANITIZE_STRING (do not use this for free text string), 'custom'= custom filter specify $filter and $options)
	 *  @param	int		$method	     Type of method (0 = get then post, 1 = only get, 2 = only post, 3 = post then get, 4 = post then get then cookie)
	 *  @param  int     $filter      Filter to apply when $check is set to custom. (See http://php.net/manual/en/filter.filters.php for détails)
	 *  @param  mixed   $options     Options to pass to filter_var when $check is set to custom
	 *  @return string|string[]      Value found (string or array), or '' if check fails
	 */
	function GETPOST($paramname,$check='',$method=0,$filter=NULL,$options=NULL)
	{
		if (empty($method)) $out = isset($_GET[$paramname])?$_GET[$paramname]:(isset($_POST[$paramname])?$_POST[$paramname]:'');
		elseif ($method==1) $out = isset($_GET[$paramname])?$_GET[$paramname]:'';
		elseif ($method==2) $out = isset($_POST[$paramname])?$_POST[$paramname]:'';
		elseif ($method==3) $out = isset($_POST[$paramname])?$_POST[$paramname]:(isset($_GET[$paramname])?$_GET[$paramname]:'');
		elseif ($method==4) $out = isset($_POST[$paramname])?$_POST[$paramname]:(isset($_GET[$paramname])?$_GET[$paramname]:(isset($_COOKIE[$paramname])?$_COOKIE[$paramname]:''));
		else return 'BadThirdParameterForGETPOST';
		if (! empty($check))
		{
		    switch ($check)
		    {
		        case 'int':
		            if (! is_numeric($out)) { $out=''; }
		            break;
		        case 'alpha':
		            $out=trim($out);
		            // '"' is dangerous because param in url can close the href= or src= and add javascript functions.
		            // '../' is dangerous because it allows dir transversals
		            if (preg_match('/"/',$out)) $out='';
		            else if (preg_match('/\.\.\//',$out)) $out='';
		            break;
		        case 'san_alpha':
		            $out=filter_var($out,FILTER_SANITIZE_STRING);
		            break;
		        case 'aZ':
		            $out=trim($out);
		            if (preg_match('/[^a-z]+/i',$out)) $out='';
		            break;
		        case 'aZ09':
		            $out=trim($out);
		            if (preg_match('/[^a-z0-9]+/i',$out)) $out='';
		            break;
		        case 'array':
		            if (! is_array($out) || empty($out)) $out=array();
		            break;
		        case 'custom':
		            if (empty($filter)) return 'BadFourthParameterForGETPOST';
		            $out=filter_var($out, $filter, $options);
		            break;
		    }
		}
		return $out;
	}
?>
