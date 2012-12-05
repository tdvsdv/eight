var $j=jQuery.noConflict();

$j(document).ready(function(){
	if(!$j('#people_table table.sqltable tr').length)
		$j('#people').hide();
	
	var birth_num=parseInt($j('#birth_vis_row_num').html())-1;
	$j('fieldset.birthdays table tr:gt('+birth_num+')').hide();
	if(parseInt($j('#birth_vis_row_num').html())<$j('fieldset.birthdays table tr').length)
		$j('#show_all_birth').css('display', 'block');

	$j('#show_all_birth').click(function(){
		if($j(this).data('showed'))
			{
			$j(this).html('&darr;');
			$j('fieldset.birthdays table tr:gt('+birth_num+')').hide();
			$j(this).data('showed', false);
			}
		else
			{
			$j(this).data('showed', true);
			$j(this).html('&uarr;');
			$j('fieldset.birthdays table tr').show();
			}
		
		});		
		
	var departments=[]; var departments_1={};
	$j('.dep_name').each(function(){
		departments.push($j(this).html());
		departments_1[$j(this).html()]=$j(this).parent().attr('id');
		});
	departments.sort();
	var length=departments.length; var last_el=false;
	for(i=0; i<length; i++)
		{
		if(departments[i].indexOf(last_el)==0)
			{
			delete departments[i];
			}
		else
			last_el=departments[i];
		}	
	var length=departments.length;
	var html=$j('#move_to_dep').html()+"<ul><li>";
	for(i=0, x=0; i<length; i++)
		{
		if(departments[i]!=undefined)
			{
			if(!(x%$j('#ALPH_ITEM_IN_LINE').html()))
				{
				if(i!=0)
					html+="</ul><ul><li>";
				}
			else
				{
				if(i!=0)
					html+='</li><li>';
				}
			html+='<a href="#'+departments_1[departments[i]]+'" class="in_link">'+departments[i]+'</a>';
			x++;
			}

	
		}
	html+="</li></ul>";
	$j('#move_to_dep').html(html);
	

	$j("a.letter").each(function (e){
		var target=$j(".sep_letter:contains('"+$j(this).html()+"')");
		if(target.length)
			{
			$j(this).attr('href', '#'+target.attr('id'));
			}
		else
			{
			$j(this).after('<span class="letter">'+$j(this).html()+'</span>').remove();
			}
		});

	$j("a[href*=#]:not([href=#])").click(function () {
		var elementClick = $j(this).attr("href")
		var destination = $j(elementClick).offset().top;
		$j('.aim').removeClass('aim');		
		$j("html:not(:animated),body:not(:animated)").animate({scrollTop: destination}, 1100, 'swing', function(){
			if($j(elementClick).children().hasClass('dep_name')||$j(elementClick).hasClass('sep_letter'))
				$j(elementClick).addClass('aim'); 
			});
		return false;
	});
	

	$j('#Name').focus()
			   .select();

	$j("#vac_apply").click(function(){sendVacation();});    
		
		
	$j("#vac_from, #vac_to").bind('keydown', function(event){
		if(event.keyCode==13)
			{		
			return false;		
			}
		});  
		
	$j("#vac_from, #vac_to").bind('keyup', function(event){
		if(event.keyCode==13)
			{		
			sendVacation();		
			}
		}); 

	$j("#vac_from, #vac_to").click(function(){
		$j(this).removeClass('error')
				.next('em').html('<i></i>')
				.prev().parent().removeClass('title');
				
		});
		

	function sendVacation()
		{
		var Url='./pages/si_add_vacation.php';
		var pars='vac_from='+$j('#vac_from').val()+'&vac_to='+$j('#vac_to').val();
		$j('#vac_from, #vac_to').attr('disabled', true);
		$j('#vac_apply').hide();
		$j('#vac_loader').show();

		$j.ajax({
			data: pars,
			url: Url, 
			dataType : "json",
			type: 'POST',
			success: function(data, textStatus){
				if(data.success=='true')	
					{
					$j('#vac_from').addClass('success');
					$j('#vac_to').addClass('success');
					}
				else
					{
					$j('#'+data.field).addClass('error')
									  .next('em').html(data.answer+'<i></i>')
									  .prev().parent().addClass('title');
									  
					}
				$j('#vac_loader').hide();
				$j('#vac_from, #vac_to').attr('disabled', false);
				$j('#vac_apply').show();					
				}
			});
		}
		

	$j("tr.odd, tr.even").live('mouseover', function(){
		$j(this).addClass('selected');
		});
	$j("tr.odd, tr.even").live('mouseout', function(){
		$j(this).removeClass('selected');
		});

	$j(document.body).on('click', 'a.out_xmpp_list', function(){
		var t = $j(this);
		//$j(this).toggleClass("in_xmpp_list out_xmpp_list")
		var a = $j(this).clone();
		var top=$j(this).offset().top;
		var left=$j(this).offset().left;
		$j(this).after(a);

		a.css('position', 'absolute')
			 .css('top', top)
			 .css('left', left)
			 .animate({opacity: 0, top: $j('#send_xmpp_message').offset().top, left: $j('#send_xmpp_message').offset().left}, 600, 'swing', function() {		
							var pars='login='+$j(this).attr('data-login');
							$j.ajax({
								data: pars,
								url: './pages/si_aj_add_in_xmpp_list.php', 
								type: 'GET',
								success: function(data, textStatus){
									//alert(data);
									t.toggleClass("in_xmpp_list out_xmpp_list")
									}
								});
			});

		return false;

		});

		$j(document.body).on('click', 'a.in_xmpp_list', function(){
		
			var pars='login='+$j(this).attr('data-login');
			var t = $j(this);
			$j.ajax({
						data: pars,
						url: './pages/si_aj_rem_in_xmpp_list.php', 
						type: 'GET',
						success: function(data, textStatus){
							t.toggleClass("out_xmpp_list in_xmpp_list");
							}
						});			
			return false;
			});

		$j('#send_xmpp_message').click(function(){
			Lightview.show({
				url: 'newwin.php?menu_marker=si_send_xmpp_message',
				type: 'iframe',
				options: {
			    	width: '80%',
			    	height: '100%',
			    	keyboard: {esc: true},
			    	skin: 'light'
			  		}
				});			
			});



	$j("a.fav_true").live('click', function(){
		var native_tr=$j(this).parents('tr').first()

		var url='./pages/si_aj_remove_favourite.php';
		var pars='current_user_dn='+$j('#current_user_dn').html()+'&favourite_user_dn='+$j(this).next().children('div.favourite_user_dn').html()
		$j.ajax({
			data: pars,
			url: url, 
			type: 'POST',
			success: function(data, textStatus){
				//alert(data);
				
				}
			});

		if(native_tr.hasClass('favourite'))
			{
			native_tr.animate({
				opacity: 0,
				}, 600, 'swing', function() {
					native_tr.remove();
					$j('#'+native_tr.attr('data-parent-id')).find('a.favourite').toggleClass("fav_false fav_true")
					});
			}
		else
			{
			var fav_tr=$j('tr.favourite[data-parent-id='+native_tr.attr('id')+']');
			$j(this).toggleClass("fav_false fav_true")
			fav_tr.remove();
			}
				
	});

	$j("a.fav_false").live('click', function(){

		var native_tr=$j(this).parents('tr').first()
		var tr=native_tr.clone();
		var native_table=$j(this).parents('table').first()
		var table=native_table.clone();
		var top=native_tr.offset().top;
		var left=native_tr.offset().left;

		table.find('tr').remove();
		table.html(tr)
		tr.attr('data-parent-id', tr.attr('id'));
		tr.attr('id', '');

		$j(this).parents('tr:first').children('td').map(function(index){
			table.find('tr td').eq(index).css('width', $j(this).width())
			});

		native_table.after(table);


		var url='./pages/si_aj_add_favourite.php';
		var pars='current_user_dn='+$j('#current_user_dn').html()+'&favourite_user_dn='+$j(this).next().children('div.favourite_user_dn').html()

		$j.ajax({
			data: pars,
			url: url, 
			type: 'POST',
			success: function(data, textStatus){
				//alert(data);
				
				}
			});
		
		table.css('position', 'absolute')
			 .css('top', top)
			 .css('left', left)
			 .css('width', native_table.width())
			 .animate({
						top: native_tr.offset().top-100, 
						opacity: 0,
						}, 300, 'swing', function() {	
										table.remove()
										native_table.find('tr:first th').parent().after(tr)
										tr.removeClass('selected')
										  .addClass('favourite')
										tr.find('a.favourite').toggleClass("fav_false fav_true")
										  
										native_tr.find('a.favourite').toggleClass("fav_false fav_true");


										});

		//$j(this).parents('tr').first().after(tr);



		});

		$j("textarea.auto_resizing").bind('keyup', function(){
			if($j(this).val().split("\n").length+1>$j(this).attr("rows"))
				$j(this).attr("rows", $j(this).val().split("\n").length+1);
			});


		$j("a.window").click(function(event){
			openWindow($j(this));
			return false;
			});

		$j("div.window").mouseleave(function(event){ 
			$j(this).hide();
			});

		$j("a.is_it_you").click(function(event){ 
			$j(this).parent().next('div.window').find('input[type=password]').focus();
			});

		if($j("form.auth_form_sent").length>0)
			{
			openWindow($j("form.auth_form_sent").parents('div.window').prev('div').children('a.window'));
			var destination = $j("form.auth_form_sent").offset().top-50;
			$j("html:not(:animated),body:not(:animated)").animate({scrollTop: destination}, 1100, 'swing');			
			}
		
	$j("form[data-ajax=true] input:submit").click(function(){
		$j('div.succes_mess').remove();
		var t = $j(this);
		t.attr('disabled', 'disabled')
		t.after('<div class="loader"></div>');
		var padding = t.css('padding-left')
		$j(this).css('padding-left', 25);
		var loader = t.next()
		loader.css('position', 'absolute')
					   .css('left', $j(this).offset().left+5)
					   .css('top', $j(this).offset().top+$j(this).height()/2-5)
		var form = $j(this).parents('form');

		$j.ajax({
			data: form.serialize(),
			url: form.attr('action'), 
			dataType : "json",
			type: 'POST',
			success: function(data, textStatus){
				//alert(data);
				if(data.success)
					{
					t.before('<div class="succes_mess">'+data.message+'</div>');
					}
				t.removeAttr('disabled').css('padding-left', padding)
				loader.remove();
				}
			});
		return false;
		});

	$j("#last_xmpp_messages").change(function(){
		$j("#xmpp_messages").html($j(this).val())
		});


	});


function openWindow(link)
	{
	var win=link.parent().next('div.window');
	var left=link.offset().left
	if(left+win.width()>=$j('body').width())
		left=left-win.width()+link.width();
	win.show()
		.css('position', 'absolute')
		.css('left', left)
		.css('top', link.offset().top);

	}

function scroll()
	{
	if(element=document.getElementById('IDForScroll'))
		element.scrollIntoView(false);
	}

function F()
	{
	}
	





	