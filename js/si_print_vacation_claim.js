var $j=jQuery.noConflict();

function GF()
	{
	var p=4;
	}

$j(document).ready(function(){

	$j('#print').bind('click', function(){
		$j('.no_print, .edit').hide();
		$j('#print').unbind('mouseleave');
		window.print();
		});
	
	$j('#print').bind('mouseover', function(){
		$j('.edit').hide();
		$j('div.links').children().hide();
		});	
	
	$j('#print').bind('mouseleave', function(){
		$j('.edit').show();
		$j('div.links').children().show();
		});		

	$j('.edit a:first-child').bind('click', function(){
			$j(this).hide()
					.next().show();
			var EditText=$j(this).parent().prev('span');
			
			if($j(this).parent('.edit').hasClass('textarea'))
				{
				$j('div.links').hide();	
				EditText.html('<textarea>'+EditText.html()+'</textarea>');
				}
			else
				{
				EditText.html('<input type="text" value="'+EditText.html()+'" />');
				}
			
			$j(this).parent().prev('span').children().focus();
		});	

	/*$j('.edit a:last-child').bind('click', function(){
		if($j(this).next('span.attribute').html())
			applyChange($j(this).parent().prev('span').children());	
		else
			{
			$j(this).hide();
			$j(this).parent('span').prev('.editing').html($j(this).parent('span').prev('.editing').children().val());
			$j(this).prev('a').show();
							
			}
		});	*/		
		
	$j('.editing input, .editing textarea').live('blur', function(){
		if(!$j('body').data('enter_up'))
			{
			if($j(this).parent('span').next('span.edit').children('span.attribute').html())
				applyChange($j(this));
			else
				{			
				$j(this).parent('span.editing').next('span.edit').children('a:first-child').show();
				$j(this).parent('span.editing').next('span.edit').children('a:last-child').hide();
				$j(this).parent('span.editing').html($j(this).val());	
				$j('div.links').show();
				}
			}
		});
		
	$j('.editing input').live('keyup', function(event){
		switch(event.keyCode)
			{		
			case 13:	
			$j('body').data('enter_up', true);
			if($j(this).parent('span').next('span.edit').children('span.attribute').html())
				{
				applyChange($j(this));
				}
			else
				{
				
				$j(this).parent('span.editing').next('span.edit').children('a:first-child').show();
				$j(this).parent('span.editing').next('span.edit').children('a:last-child').hide();
				$j(this).parent('span.editing').html($j(this).val());
				$j('body').data('enter_up', false);	
				$j('div.links').show();				
				}				
			break;
			}
		
		});	
		
	$j('div.links a').click(function(){
		$j('#claim_body span.editing').html($j(this).attr('title'));	
		});	
		
	
		
	function applyChange(object)
		{
		var Url='./pages/si_apply_vac_claim_change.php';
		var pars='attribute='+object.parent('span').next('span.edit').children('span.attribute').html()+'&dn='+object.parent('span').next('span.edit').children('span.dn').html()+'&value='+object.val();
		object.parent('span').next('span.edit').children('img.loader').show();
		object.parent('span').next('span.edit').children('a').hide();	
		
		$j.ajax({
			data: pars,
			url: Url, 
			dataType : "json",
			type: 'POST',
			success: function(data, textStatus){
				//alert(data);
				if(data.success=='true')
					{		
					
					object.parent('span').next('span.edit').children('img.loader').hide();
					object.parent('span').next('span.edit').children('a:first-child').show();

					object.parent('span').html(object.val());
					$j('body').data('enter_up', false);	
					$j('div.links').show();
					}
				}
			});		
		}
		
	
	
});