jQuery(function($){
	//слайдер на главной .. 
	$('.events-slider').slick({
		prevArrow:'<div class="prev"></div>',
		nextArrow:'<div class="next"></div>',
		dots:true,
	});

	// форма в подвале ...  
	$('.write-uns a').on('click',function (e){
		e.preventDefault();
		$.get('/write-uns',function(data){
			if (data.status=='ok')
				$.fancybox.open({
					src:data.html,
					type:'html',
					afterShow:function(inst){
						
						this.$content.find('form').on('submit',function(e){
							e.preventDefault();
							// запрещаем отпраку .. 
							var form=$(this);
							// удалили  класс ошибки ...
							form.find('.form-group').removeClass('error-field').find('.help-block .errr').remove();
							
							// отправили форму на сервак .. 
							$.post(this.action,$(this).serializeArray(),function(res){
								// не прошли валидацию 
								switch(res.status){
									case 'nook':
										for(ek in res.errs){
											// вешаем класс ошибки 
											var el=form.find('.form-group.field-writeunsform-'+ek)
											el.addClass('error-field');
											el.find('.help-block').append('<span class="errr">'+res.errs[ek].join('<br/>')+'</span>' );
										}
									break;
									case 'ok':
										$.fancybox.close();
										$.fancybox.open(res.mess);
									break;
								}
								
									
									
								//console.log(res,'result');
							});
							
						});
						//console.log(inst,this.content);
					}

				});
			
		});
/*
		$.fancybox.open({
			src:'/write-uns',
			type:'ajax',
			baseClass:'popup-content',
			ajax:{
				settings:{
					data:{
						act:'getform',
					}
				}
			}
		});*/
	});
});