<?php 
	
	require "main.inc.php";

	$sql = "SELECT z.*, m.cords FROM `llx_zimmer` as z";
	$sql.= " Left Join llx_mapvalue as m on (m.rowid = z.fk_mapvalue)";
	//echo $sql;
	$req = $db->query($sql);
	if($req)
	{
		while($res = $db->fetch_object($sql))
		{
			$image_map_rooms[] = $res;
		}
	}

	main_header();
		print '<input type="hidden" id="marker" value="maps.php">';

		print '<div style="margin-top: 40px; background-color: #ffffff; z-index: 100; position: absolute; padding: 5px; border: 1px solid black; right: 0px; top: 0px; height: 100%;">';
			// Info Table
			print '<table class="Info-Table">';
				print '<tr>';
					print '<td>';
						print 'Raum';
					print '</td>';
					print '<td>';
						print 'Text';
					print '</td>';									
					print '<td>';
						print 'Größe';
					print '</td>';
				print '</tr>';
				print '<tr>';
					print '<td>';
						print '<div id="selected">0</div>';
					print '</td>';
					print '<td>';
						print '<input type="text" id="selected-text">';
					print '</td>';
					print '<td>';
						print '<select id="selected-size">';
							for($ix = 1; $ix < 30; $ix++)
							print '<option value="'.$ix.'">'.$ix.'</option>';
						print '</select>';
					print '</td>';
				print '</tr>';
			print '</table>';
			// Info Table end
			print '<p>';
			print '<table class="Info-Table">';
				print '<tr>';
					print '<td>';
						print 'Farbe:';
					print '</td>';
				print '</tr>';
				print '<tr>';
					print '<td>';
						print '<select id="selected-color">';
							print '<option value="white" style="background-color:white;">Weis</option>';
							print '<option value="green" style="background-color:green;">Grün</option>';
							print '<option value="yellow" style="background-color:yellow;">Gelb</option>';
							print '<option value="orange" style="background-color:orange;">Orange</option>';
							print '<option value="red" style="background-color:red;">Rot</option>';
						print '</select>';
					print '</td>';
				print '</tr>';
			print '</table>';

			print '<p>';
			print '<table class="Info-Table" style="width:100%;">';
				print '<tr>';
					print '<td>';
						print 'Besonderheiten:';
					print '</td>';
				print '</tr>';
				print '<tr style="width:100%;">';
					print '<td style="width:100%;">';
						print '<textarea id="Besonderheiten" style="width:100%; height:100px;">';
						print '</textarea>';
					print '</td>';
				print '</tr>';
			print '</table>';

		print '</div>';

		// Screen
		print '<div class="MainScreen">';

			print '<table class="MainTable">';

				// T header
				print '<tr>';
					print '<td class="list_t">';
						print 'LBS';
					print '</td>';
					print '<td class="list_t HeimButton">';
						print 'H1';
					print '</td>';
					print '<td class="list_t HeimButton">';
						print 'H2';
					print '</td>';
					print '<td class="list_t HeimButton">';
						print 'H3';
					print '</td>';
					print '<td class="list_t LastHeim">';
					print '</td>';
				print '</tr>';

				// T Left
				print '<tr>';
					print '<td class="list_t StockwerkButton">';
						print 'E';
					print '</td>';
					// Heim Bild
					print '<td rowspan="4" colspan="4" class="HeimScreen" id="HeimScreen">';
						//print 'Image';
						print '<div style="position: relative;">';
							print '<img id="heimimage" style="top: 0; left: 0;" src="heimstockwerke/H1S1.gif" class="HeimStockwerkBild" usemap="#roommap">';
							
							print '<map style="position: absolute;top: 0; left: 0;" name="roommap" id="roommap">';
								foreach($image_map_rooms as $key => $area)
								{
									print '<area 
											id="'.$area->rowid.'_area" 
											value="'.$area->name.'" 
											data-color="'.$area->color.'"
											data-pu_text="'.$area->public_note.'"
											data-font-size="'.$area->font_size.'"
											shape="poly" 
											href="" 
											onclick="changeRoom(\''.$area->name.'\')" 
											coords="'.$area->cords.'" 
											alt="room"
										   >';
								}
							print '</map>';
						print '</div>';

					print '</td>';

					print '<td rowspan="4" colspan="4" class="HeimScreen" id="HeimScreenTable" style="display:none;vertical-align: top;">';
						print '<h2>Heim</h2>';
						print '<table class="heimScreenTable">';
							print '<tr>';
								print '<td>';
									print 'Nr.';
								print '</td>';
								print '<td>';
									print 'Text';
								print '</td>';
							print '</tr>';
						foreach($image_map_rooms as $key => $area)
						{
							print '<tr>';
								print '<td style="text-align: center;background-color:'.translate_color($area->color).';">';
									print $area->name;
								print '</td>';
								print '<td>';
									print $area->public_note;
								print '</td>';
							print '</tr>';
						}
						print '</table>';
					print '</td>';

				print '</tr>';

				print '<tr>';
					print '<td class="list_t StockwerkButton">';
						print '1';
					print '</td>';
				print '</tr>';

				print '<tr>';
					print '<td class="list_t StockwerkButton">';
						print '2';
					print '</td>';
				print '</tr>';

				// Footer
				print '<tr>';
					print '<td class="list_t LastStockwerk">';
						print '';
					print '</td>';
				print '</tr>';

			print '</table>';

		print '</div>';

	//main_footer();

	function translate_color($color)
	{
		if($color == "white") 
			return "rgba(255, 255, 255, 0.5)";
		if($color == "red") 
			return "rgba(255, 0, 0, 0.5)";
		if($color == "green") 
			return "rgba(0, 255, 0, 0.5)";
		if($color == "blue")
			return "rgba(0, 0, 255, 0.5)";
		if($color == "yellow") 
			return "rgba(255, 255, 0, 0.5)";
		if($color == "orange") 
			return "rgba(255, 127, 0, 0.5)";
	}
?>