
	$( document ).ready(function(){
		var login_screen_is_on = false;

		$('#login').on('click', function(){
			$('body').append('<div class="filter-black-stroke" id="filter"></div>');
			$('#filter').append('<div class="login-box" id="loginbox"></div>');
			$('#loginbox').append('<div id="header2" style="font-size: 42px; color: #fff; padding-left: 5px;">Login</div>');
			$('#loginbox').append(
				'<form id="login_form" action="'+ $('#self').val() +'" method="post">' +
					'<div class="main">' + 
						'<table style="position: relative; top: 0px; left: 2.5%; width: 95%; height: 56%;">' + 
							'<tr>' + 
								'<td style="text-align: center;font-size: 30px;">' + 
									'Loginname:<br><input name="loginname" type="text" value="" style="text-align: center; font-size: 30px; width: 100%; height: 40px; border-radius: 12px; border: 3px solid #7D7D7D;">' +
								'</td>' + 
							'</tr>' +
							'<tr>' + 
								'<td style="text-align: center;font-size: 30px;">' +
									'Passwort:<br><input name="loginpass" type="password" value="" style="text-align: center; font-size: 30px; width: 100%; height: 40px; border-radius: 12px; border: 3px solid #7D7D7D;">' +
								'</td>' + 
							'</tr>' +
							'<tr>' + 
								'<td style="text-align: center;font-size: 30px;">' +
									'<br><input name="login" type="submit" value="Login" style="text-align: center; font-size: 30px; width: 100%; height: 40px; border-radius: 12px; border: 3px solid #7D7D7D;">' +
								'</td>' + 
							'</tr>' +
						'</table>' + 
					'</div>' +
				'</form>'
				);
			login_screen_is_on = true;
		});

		$( document ).keydown(function( event ) {
			if ( event.which == 27 && login_screen_is_on == true) {
				$('.filter-black-stroke').remove();
				$('.login-box').remove();
				
				$('#login_form').remove();
			}
		});
	});