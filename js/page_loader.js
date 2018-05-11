
	var activ_menu = "";
	var menu_info = {};
	var ani_time = 200;
	var ajax_loader_url = "";

	var quad;
	var canvas;
	var stage;
	var shape;
	var drawColor;

	var pointShape;

	var g;

	var stylePaddingLeft;
	var stylePaddingTop ;
	var styleBorderLeft ;
	var styleBorderTop  ;
	// Some pages have fixed-position bars (like the stumbleupon bar) at the top or left of the page
	// They will mess up mouse coordinates and this fixes that
	var html;
	var htmlTop = 0;
	var htmlLeft = 0;

	$( document ).ready(function(){
		//change_link_to_ajax();
		draw_svg();
		$('area').click(function( event ) {
			event.preventDefault();
		});
		$('.main_tab').click(function( event ) {
			$('.main_tab').css('background-color', '');
			$('.main_tab').css('color', '');
			$(this).css('background-color', '#BBBBBB');
			$(this).css('color', '#000000');
		});
		$('#map_tab').click(function( event ) {
			window.location.href="maps.php";
		});
		$('#ck_plan').click(function( event ) {
			//console.log(window.location.href);
			//if($('#marker').val() != "index.php") window.location.href="index.php";
			$('#HeimScreenTable').fadeOut('fast', function(){
				$('#HeimScreen').fadeIn('fast');
				
			});			
		});
		$('#ck_table').click(function( event ) {
			//if($('#marker').val() != "index.php") window.location.href="index.php";
			$('#HeimScreen').fadeOut('fast', function(){
				$('#HeimScreenTable').fadeIn('fast');	
			});
		});

		$('.HeimButton').click(function( event ) {
			$('.HeimButton').css('background', '');
			$('.HeimButton').css('color', '');
			$(this).css('background', '#12B100');
			//$(this).css('background-color', '#BBBBBB');
			$(this).css('color', '#000000');
			changeHeim($(this).attr('id'));
		});
		$('.StockwerkButton').click(function( event ) {
			$('.StockwerkButton').css('background', '');
			$('.StockwerkButton').css('color', '');
			//$(this).css('background-color', '#BBBBBB');
			$(this).css('background', '#12B100');
			$(this).css('color', '#000000');
			changeStock($(this).attr('id'));
		});
		
		$('#selected-text').keyup( function( event ) {
			name = $('#selected').html();
			$('#divof_' + name ).html(
				name + ': ' + $('#selected-text').val()
			);
			save_data(name, 'public_text', $('#selected-text').val());
		});
		$('#selected-size').on('change', function(){
			name  = $('#selected').html();
			size  = $('#selected-size').val();
			style = $('#divof_' + name ).attr('style');
			$('#divof_' + name ).attr('style', style + 'font-size:'+size+';');
			save_data(name, 'size', size);
		});
		$('#selected-color').on('change', function(){

			rgba_c = translate_color($('#selected-color').val());
			name  = $('#selected').html();
			//style = $('#divof_' + name ).attr('style');
			$('#poly_' + name ).attr('fill', rgba_c);
			save_data(name, 'color', $('#selected-color').val());
		});
	});

	var ak_heim = 1;
	function changeHeim(heim)
	{
		ak_heim = heim.slice('H');
		ak_heim = parseInt(ak_heim[1]);
		$('#heim').val(heim);
		newimg = heim + $('#Stockwerk').val();
		
		$('#heimimage').attr('src', 'heimstockwerke/' + newimg + '.png');
		ajax_heim_loder(ak_heim, ak_stock)
	}

	var ak_stock = 0;
	function changeStock(stock)
	{
		ak_stock = stock.slice('S');
		if(ak_stock == 'SE') 
		{
			ak_stock = 0;
		}
		else
		{
			ak_stock = parseInt(ak_stock[1]);
		}
		$('#Stockwerk').val(stock);
		newimg = $('#heim').val() + stock;

		$('#heimimage').attr('src', 'heimstockwerke/' + newimg + '.png');
		ajax_heim_loder(ak_heim, ak_stock)
	}

	function changeRoom(name)
	{
		//$("polygon").attr('stroke', 'none');
		$('#selected').html(name);
		ajax_room_loder(name);
	}
	
	function save_data(name, type, val)
	{
		$.ajax({
			method: "POST",
			url: "ajax/ajax_room.php",
			data: { 
				room: name, 
				type: type,
				val: val,
				save: 1 
			}
		})
	}

	function ajax_room_loder(room){
		$.ajax({
			method: "POST",
			url: "ajax/ajax_room.php",
			data: { save: 0 }
		})
		.done(function( dataraw ) {
			dataraw = $.parseJSON(dataraw);

			$.each(dataraw, function (index1, data) {
				$.each(data, function (index, value) {
					if(index == 'color')
					{
						if(room == index1) $('#selected-color').val(value);
						rgba_c = translate_color(value);
						style = $('#poly_' + index1 ).attr('fill', rgba_c);
						//$('#divof_' + index1 ).attr('style', style + 'background-color:'+rgba_c+';');
					}
					if(index == 'public_note')
					{
						$('#divof_' + index1 ).html(index1 + ': ' + value);
						if(room == index1) $('#selected-text').val(value);
					}
					if(index == 'font_size')
					{
						if(room == index1) $('#selected-size').val(value);
					}
					//console.log(index + ' ' + value);
				});
			});
			$('#poly_' + room).attr('fill', '#A4D3FF');
		});
	}

	function ajax_heim_loder(heim, stock){
		$.ajax({
			method: "GET",
			url: "ajax/ajax_room.php",
			data: { 
				load_area: ak_stock,
				load_heim: ak_heim
			}
		})
		.done(function( dataraw ) {
			dataraw = $.parseJSON(dataraw);
			$('map').html('');
			$('map').html(dataraw);
			$('svg').remove();
			draw_svg()
		});
	}

	function translate_color(color)
	{
		if(color == "white") 
			return "rgba(255, 255, 255, 0.5)";
		if(color == "red") 
			return "rgba(255, 0, 0, 0.5)";
		if(color == "green") 
			return "rgba(0, 255, 0, 0.5)";
		if(color == "blue")
			return "rgba(0, 0, 255, 0.5)";
		if(color == "yellow") 
			return "rgba(255, 255, 0, 0.5)";
		if(color == "orange") 
			return "rgba(255, 127, 0, 0.5)";
	}

	/*
	 * D3 SVG Shape
	 */

	var svg;
	function draw_svg()
	{
		
	 	svg = d3.select('#HeimScreen div').append("svg");
			svg.attr("width", '100%');
			svg.attr("height", '100%');
			svg.attr("style", 'position:absolute;top:0;left:0;pointer-events:none;');

		defs = svg.append('defs');
		filter = defs.append('filter')
			.attr('id', "f1")
			.attr('x', "0")
			.attr('y', "0");
		filter.append('feGaussianBlur')
			.attr('in', 'SourceGraphic')
			.attr('stdDeviation', '2');

		
		$.each($("#roommap area"), function(index){
			
			g = svg.append('g');
            
			cod = $(this).attr('coords').split(" ");
			raw_color = $(this).attr('data-color');
			color = translate_color(raw_color);
			public_note = $(this).attr('data-pu_text');
			font_size = $(this).attr('data-font-size');
			name = $(this).attr('value');

			area = g.append("polygon")
				.attr("id", 'poly_'+name)
				.attr("fill", color)
				.attr("stroke", "none")
				.attr("stroke-width", "3")
				.attr("filter", "url(#f1)")
				.attr("points", $(this).attr('coords'));

			x = "";
			y = "";
			xx = "";
			xy = "";
			$.each(cod, function(index, cordi){
				
				if(cordi != "" && isNaN(cordi))
				{
					cordi = cordi.split(",");
					tmp_x = parseInt(cordi[0].split(".")[0]);
					tmp_y = parseInt(cordi[1].split(".")[0]);
					if(x == "" || x > tmp_x) 
					{
						x = tmp_x;
					}
					if(xx == "" || xx < tmp_x) 
					{
						xx = tmp_x;
					}


					if(y == "" || y > tmp_y) 
					{
						y = tmp_y;
					}
					if(xy == "" || xy < tmp_y) 
					{
						xy = tmp_y;
					}
				}
			});

			//console.log(name+" xx:" + xx + " xy:" + xy);
			$("#roommap").append(
				'<div id="divof_'+name+'" style="z-index:99;position:absolute;top:'+y+'px;left:'+x+'px;width:'+(xx - x)+'px;height:'+(xy - y)+'px;pointer-events:none;">'
				+name+
				'</div>'
			);

			//style = $('#divof_'+name).attr('style');
			//if(color) $('#divof_'+name).attr('style', style + 'background-color:'+color+';');
			style = $('#divof_'+name).attr('style');
			if(font_size) $('#divof_'+name).attr('style', style + 'font-size:'+font_size+';');
			if(public_note) $('#divof_'+name).html(name + ': ' + public_note);
			/*
			g.append("text")
				.attr("x", x)
				.attr("y", y)
				.attr("font-family", "sans-serif")
				.attr("font-size", "20px")
				.attr("fill", "red")
				.call(wrap, 30, $(this).attr('value')); // wrap the text in <= 30 pixels;
			*/
		});
	}