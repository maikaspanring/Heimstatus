<?php 
	
	require "../main.inc.php";
	
	if(GETPOST('save') == 0)
	{
		if(empty($_GET['load_area']) && empty($_GET['load_heim']))
		{
			$sql = "SELECT * FROM `llx_zimmer` ";
			$req = $db->query($sql);
			if($req)
			{
				while($res = $db->fetch_object($sql))
				{
					$rooms[$res->name] = $res;
				}
			}

			if(!empty($rooms[GETPOST('room')]))
			{
				echo json_encode($rooms[GETPOST('room')]); 
			}
			else
			{
				echo json_encode($rooms); 
			}
		}
		else
		{
			$sql = "SELECT z.*, m.cords FROM `llx_zimmer` as z";
			$sql.= " Left Join llx_stockwerk as st on (st.rowid = z.fk_stockwerk and st.fk_heim = z.fk_heim)";
			$sql.= " Left Join llx_heim as h on (h.rowid = st.fk_heim)";
			$sql.= " Left Join llx_mapvalue as m on (m.rowid = z.fk_mapvalue)";
			$sql.= " Where z.fk_stockwerk = ".GETPOST('load_area')." AND z.fk_heim = ".GETPOST('load_heim');
			$req = $db->query($sql);

			if($req)
			{
				$ret = '';
				while($res = $db->fetch_object($sql))
				{
					$cords = preg_replace("#(\r|\n)#", '', $res->cords); 
					$ret.= '<area  id="'.$res->rowid.'_area" heim="'.$res->fk_heim.'" stockwerk="'.$res->fk_stockwerk.'" value="'.$res->name.'" data-color="'.$res->color.'" data-pu_text="'.$res->public_note.'" data-font-size="'.$res->font_size.'" shape="poly"  href="#"  onclick="changeRoom(\''.$res->name.'\')"  coords="'.$cords.'"  alt="room">';
				}
			}

			echo json_encode($ret);

		}
	}
	elseif(GETPOST('type') == 'public_text')
	{
		$sql = "UPDATE `llx_zimmer` SET `public_note` = '".GETPOST('val')."' WHERE `llx_zimmer`.`name` = ".GETPOST('room')."; ";
		$req = $db->query($sql);
		echo GETPOST('val');
	}
	elseif(GETPOST('type') == 'private_text')
	{
		$sql = "UPDATE `llx_zimmer` SET `private_note` = '".GETPOST('val')."' WHERE `llx_zimmer`.`name` = ".GETPOST('room')."; ";
		$req = $db->query($sql);
		echo GETPOST('val');
	}
	elseif(GETPOST('type') == 'color')
	{
		$sql = "UPDATE `llx_zimmer` SET `color` = '".GETPOST('val')."' WHERE `llx_zimmer`.`name` = ".GETPOST('room')."; ";
		$req = $db->query($sql);
		echo GETPOST('val');
	}
	elseif(GETPOST('type') == 'size')
	{
		$sql = "UPDATE `llx_zimmer` SET `font_size` = '".GETPOST('val')."' WHERE `llx_zimmer`.`name` = ".GETPOST('room')."; ";
		$req = $db->query($sql);
		echo GETPOST('val');
	}
?>