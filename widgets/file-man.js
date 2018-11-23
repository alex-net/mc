$(function(){
	/**
	 * загрузка файлов ..
	 */
	$('.widget-fileman .files-input-element').change(function(){
		
		var ff=[];
		for(var i=0;i<this.files.length;i++)
			if (this.files[i].type.match(new RegExp(fileman.filesexts.join('|'),'gi')))
				ff.push(this.files[i]);
		this.value=null;
		if (!ff)
			return;

		var fd=new FormData();
		for (var i=0;i<ff.length;i++)
			fd.append('FilesModel[files]['+i+']',ff[i]);
		//fd.append(yii.getCsrfParam(),yii.getCsrfToken());
		// число файлов ...
		fd.append('filescount',ff.length);
		fd.append('cidis',fileman.contentnum);
		fd.append('ctype',fileman.contenttype);


		$.ajax({
			async: false,
			cache: false, 
			contentType: false,
			processData: false,

			url:'/files/up',
			type:'post',
			data:fd,
			success:function(res){
				window.fileman.filesuploadedlist=res.list;
				$('.widget-fileman').trigger('init');
			}
		});
		
	
		//console.log(this,this.files);
	});


	// удаление картинки ...из списка .. 
	$('.widget-fileman').on('click','li .kill',function (){
		var fn=$(this).parent().data('fn');
		var el=$(this).parent();
		$.post('/files/kill',{fn:fn},function(data){
			if (data.status=='ok'){
				window.fileman.filesuploadedlist=data.list;
				$('.widget-fileman').trigger('init');	
			}
		});
	});

	$('.widget-fileman').on('init',function(){


		if (!fileman.filesuploadedlist.length){
			$(this).find('.list-files-wrapper').html('Файлы не загружены');
			return ;
		}
		$(this).find('.list-files-wrapper').html('<ul class="list-files-widget"/>');
		for(var i=0;i<fileman.filesuploadedlist.length;i++){
			var li=$('<li/>').data({fn:fileman.filesuploadedlist[i]['filename']}).addClass('item');
			li.append('<span title="Удалить" class="kill"></span>');
			var el=$('<a>').attr('href',fileman.filesuploadedlist[i].url).attr('target','_blank');
			el.html(fileman.filesuploadedlist[i].filename);
			li.append(el);
			el=$('<img>').attr('src','/files/'+fileman.smallpreset+'/'+fileman.filesuploadedlist[i].filename);
			li.append(el);
			$(this).find('.list-files-wrapper ul').append(li);
		}
		// сортировка .. 
		$(this).find('.list-files-widget').sortable({
			items:'> li',
			opacity:0.8,
			grid:[1,1],
			stop:function(e,ui){
				console.log($(this));
				// собрать порядок  файлов .. 
				var files=[];
				$(this).find('li').each(function(){
					files.push($(this).data('fn'));
				});

				$.post('/set-files-weight',{fl:files},function(d){
					//console.log(d);
				});

			},
		})

				
	});
	
	
	$('.widget-fileman').trigger('init');

} )