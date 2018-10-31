CKEDITOR.dialog.add('imageSelector',function(editor){
	

	cc=[];
	cc.push({
		id:'insertprreloadedimage',
		label:'Вставить загруженную картинку',
		elements:[
			{
				type:'select',
				label:'Доступный пресет',
				id:'preset',
				items:[],
				setup:function(st){
					this.clear();
					for(i=0;i<st.presets.length;i++)
						this.add(st.presets[i][0],st.presets[i][1]);
				},
				commit:function(st){
					st.preset=this.getValue();
				}
			},
			{
				type:'select',
				label:'Картинка',
				id:'imgslist',
				items:[],
				setup:function(st){
					this.clear();
					for(i=0;i<st.fileslist.length;i++)
						this.add(st.fileslist[i]);
				},
				commit:function(st){
					st.fn=this.getValue();
					
				}
			}

		],
	});
	


	return {
		title:'Вставка загруженной картинки',
		minWidth:400,
		minHeight:200,
		contents:cc,
		onShow:function(e){
			this.setupContent({
				presets:imagepresets,
				fileslist:filesuploadedlist,
			});
		},
		onOk:function(e){
			var a={};
			this.commitContent(a);
			if (!a.fn || !a.preset)
				return; 
			imgpath=filesloadurl+'/'+a.preset+'/'+a.fn;
			var im=$('<img>');
			im.attr({
				src:imgpath,
				alt:a.fn,
			});
			

			//console.log(imgpath,this,im.html());
			
			editor.insertHtml(im[0].outerHTML);

		},

	};
});
