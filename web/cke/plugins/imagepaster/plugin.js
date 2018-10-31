CKEDITOR.plugins.add('imagepaster',{
	icons:'imagepaster',
	init:function(e){
		
		// добавили диалог  
		CKEDITOR.dialog.add('imageSelector',this.path+'dialogs/image-presets-selector.js');

		// добавили команду с участием диалога 
		e.addCommand('imagepaster',new CKEDITOR.dialogCommand('imageSelector'));

		// добавили кнопку .. 
		e.ui.addButton('imgpaster',{
			label:'Картинка загруженная в статью',
			name:'sad',
			command:'imagepaster',
			toolbar:'insert',
			icon:'imagepaster',
			//icon:'plugins/icons.png'
		});

		
		
	}
});