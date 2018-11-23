$(function (){
	// сраница редактирования  контента ..
	// делаем на сранице аккордионы ..
	$('.content-creator-form').find('.field-content-teaser, .alias-settings, .add-menu, .file-settings').accordion({
		collapsible:true,
		active:false,
	});

	// жмём на функциональную кнопку . в фарме .. 
	$('form.content-creator-form .footer button').on('click',function(e){
		if (this.name=='kill' && !confirm('Действительно удалить?'))
			e.preventDefault();
		//  надо обновить элементы .
		if (window.CKEDITOR && this.name=='save'){
			for (var k in CKEDITOR.instances)
				CKEDITOR.instances[k].updateElement();
			
		}

	});


	// страница меню. //
	$('.sorter-eleemnts-menu').sortable({
		items:'> li',
		opacity:0.8,
		grid:[32,1],
		stop:function(e,ui){
			var level=ui.item.attr('class').match(/level-(\d+)/);
			level=level[1];
			var oldlevel=level;

			ui.item.removeClass('level-0').removeClass('level-1');
			if (ui.originalPosition.left>=ui.position.left || !ui.item.index())
				ui.item.addClass('level-0');
			
			if (ui.originalPosition.left<ui.position.left && !ui.item.hasClass('level-0')) 
				ui.item.addClass('level-1');

			if ($(this).children().eq(0).hasClass('level-1')){
				ui.item.removeClass('level-0').removeClass('level-1').addClass('level-'+oldlevel);
				$(this).sortable('cancel');
				return;
			}

			$(this).find('.status-control').each(checkcheckboxes);
		},
		create:function(e,ui){
			var _this=$(this);
			var btn=$('<input type="button" value="Сохранить" class="btn btn-success">');
			btn.on('click',function(){
				var res=[];
				_this.children().each(function(ind){
					var level=$(this).attr('class').match(/level-(\d+)/);
					res.push({
						id:$(this).data('mid'),
						level:level[1]-0,
						weight:ind,
						active:$(this).find('.status-control:input')[0].checked-0,

					});
				});
				$.post(location.href,{
					act:'updatemenu',
					menudata:res,
				});
			});
			$(this).after(btn);
			$(this).find('.status-control').on('change',checkcheckboxes);
		}

	});
	// разрулить чекбоксами ... 
	function checkcheckboxes(){
		// если сбросили флажок и это эелмент первого уровня .. то надо найти всех очк и с них тоже всё сбросить
		if (!this.checked && $(this).parent().hasClass('level-0')){
			var el=$(this).parent();
			do{
				el=el.next();
				
				if (el.hasClass('level-0') || !el.length)
					break;
				el.find('.status-control')[0].checked=false;
			}while(1);
		}
				
		if (this.checked && $(this).parent().hasClass('level-1') && !$(this).parent().prev().find('.status-control')[0].checked ){
			this.checked=false;
		}

				
	}


// --------------------
// страница блоков 
	// страница меню. //
	$('.sortable-blocks').sortable({
		items:'> li',
		opacity:0.8,
		grid:[1,1],
		create:function(e,ui){
			var _this=$(this);
			var btn=$('<input type="button" value="Сохранить" class="btn btn-success">');
			btn.on('click',function(){
				var res=[];
				_this.children().each(function(ind){
					res.push({
						id:$(this).data('bid'),
						weight:ind,
						active:$(this).find('.status-control:input')[0].checked-0,

					});
				});
				$.post(location.href,{
					act:'updateblocks',
					blocksdata:res,
				});
			});
			$(this).after(btn);
			$(this).find('.status-control').on('change',checkcheckboxes);
		}
	});




	// применяем ckeditor для всех textareas
	if (window.CKEDITOR)
		CKEDITOR.replaceAll();
	

	//	nicEditors.allTextAreas();



});