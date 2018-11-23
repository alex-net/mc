CKEDITOR.dialog.add('imageSelector',function(editor){
	

	cc=[];
	cc.push({
		id:'insertpreloadedimage',
		label:'Вставить загруженную картинку',
		elements:[
			{
				type:'select',
				label:'Доступный пресет',
				id:'preset',
				items:[],
				default:3,
				setup:function(st){
					this.clear();
					for(i=0;i<st.presets.length;i++)
						this.add(st.presets[i][0],st.presets[i][1]);
					this.setValue(st.defpreset);
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
						this.add(st.fileslist[i].filename);
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
			var o={presets:[],fileslist:[]};
			// скармливаем данные . 
			// списк пресетов 
			if (window.ck.imagepresets!=undefined){
				o.presets=window.ck.imagepresets;
				o.defpreset=ck.defpreset;
			}
			// список загруженных файлов. 
			if (window.fileman.filesuploadedlist!=undefined)
				o.fileslist=window.fileman.filesuploadedlist;
			// базовые настройки ..
			this.setupContent(o);
		},
		onOk:function(e){
			var a={};
			// получаем значения из полей ..
			this.commitContent(a);
			if (!a.fn || !a.preset)
				return; 
			imgpath=fileman.filesloadurl+'/'+a.preset+'/'+a.fn;
			var im=$('<img>');
			im.attr({
				src:imgpath,
				alt:a.fn,
			});
			
			editor.insertHtml(im[0].outerHTML);

		},

	};
});
