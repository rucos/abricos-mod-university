var Component = new Brick.Component();
Component.requires = {
    mod: [
        {name: 'sys', files: ['editor.js']},
        {name: '{C#MODNAME}', files: ['lib.js']}
    ]
};
Component.entryPoint = function(NS){

	var Y = Brick.YUI,
        COMPONENT = this,
        SYS = Brick.mod.sys;
    
 
    NS.AddValueModalWidget = Y.Base.create('addValueModalWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance){
        	
        },
        destructor: function(){
            if (this._Editor){
                this._Editor.destroy();
            }
        },
        showModal: function(valueid, atrid, view, numrow, mainid){
        	this.set('view', view);
        	
        	if(numrow){
				this.set('numrow', numrow);
				this.set('mainid', mainid);
        	}
        	
        	if(valueid > 0){
        		this.reqValueAttributeItem(valueid, atrid);
        	} else {
            	this.set('valueItem', this.constrData(valueid, atrid));
            		this.fillForm();
        	}
        },
        fillForm: function(isEdit, click){
        	var tp = this.template,
        		replace = this.replaceForm(),
        		arr = [],
        		view = this.get('view');
        	
        	if(isEdit){
        		arr[0] = "class='hide'";
        		arr[1] = "Изменить";
        	}
        	
        	if(!click){
        		tp.setHTML('modal', tp.replace('modalFormAdd', this.constrReplace.apply(null, arr)));
        	}
	        	tp.setHTML('modalFormAdd.form', replace);
	        	
	        	if(view == 'value'){
	                this._Editor = new SYS.Editor({
	                    appInstance: this.get('appInstance'),
	                    content: this.get('valueItem').value,
	                    srcNode: tp.gel('value.editor')
	                });
	        	}
        },
        replaceForm: function(){
        	var tp = this.template,
        		valueItem = this.get('valueItem'),
        		view = this.get('view'),
        		date = valueItem.datedoc;
        	
        	if(date === true){
        		valueItem.datedoc = "";
        		valueItem.disabled = 'disabled';
        		valueItem.active = 'active';
        	} else {
           		valueItem.disabled = '';
        		valueItem.active = '';
        	}
        	return tp.replace(view, valueItem);
        },
        actValue: function(respondCallback){
        	var valueItem = this.get('valueItem'),
        		view = this.get('view'),
        		tp = this.template,
        		date = "";

        	valueItem.view = view;
        	
        	switch(view){
        		case 'file':
        			date = tp.gel('file.datedoc');
        			
        			if(date.disabled){
        				valueItem.datedoc = -1;
        			} else {
        				valueItem.datedoc = tp.gel('file.datedoc').value;
        			}
        			
        			valueItem.nameurl = tp.gel('file.nameurl').value;
            		valueItem.namedoc = tp.gel('file.namedoc').value;
            		valueItem.file = tp.gel('fileInput.inputFile') ? tp.gel('fileInput.inputFile').files[0] : '';
            			return this.reqActFiles(valueItem, respondCallback);
        		case 'url':
            		valueItem.nameurl = tp.gel('url.nameurl').value;
            		valueItem.value = tp.gel('url.value').value;
        				return this.reqActValue(valueItem, respondCallback);
        		case 'value':
            		valueItem.value = this._Editor.get('content');
            			return this.reqActValue(valueItem, respondCallback);
        	}
        },
        reqValueAttributeItem: function(valueid, atrid){
        	this.set('waiting', true);
	        	this.get('appInstance').valueAttributeItem(valueid, function(err, result){
	        		this.set('waiting', false);
		        		if(!err){
		        			this.set('valueAttributeItem', result.valueAttributeItem);
			        		this.renderValueAttributeItem(atrid);
				        }
	        	}, this);
        },
        renderValueAttributeItem: function(atrid){
        	var tp = this.template,
        		valueAttributeItem = this.get('valueAttributeItem'),
        		id = valueAttributeItem.get('id'),
        		nameDoc = valueAttributeItem.get('value'),
        		nameurl = valueAttributeItem.get('nameurl'),
        		view = valueAttributeItem.get('view'),
        		arr = [id, atrid, nameDoc, nameurl],
        		date = "";
        	
        	if(view == 'file'){
        		nameDoc.replace(/_(\d{2})\.(\d{2})\.(\d{4})\.(\w{3,4})/, function(str, day, month, year){//Определяем дату и формат
           			date += str;
           			
           			arr[5] = year + "-" + month + "-" + day;
        		});
           		
           		if(date){
           			nameDoc = nameDoc.replace(date, "");//если дата есть, то убираем дату и формат из nameDoc
           		} else {
           			arr[5] = true;
           			nameDoc = nameDoc.replace(/(\.\w{3,4})$/, "");//убираем формат из nameDoc
           		}
           		
           		arr[4] = nameDoc.replace(/\w+[-/]/ig, "");//убираем путь до файла из nameDoc
        	}
        	this.set('valueItem', this.constrData.apply(this, arr));
        	this.fillForm(true);
        },
        reqActFiles: function(valueItem, respondCallback){
        	var tp = this.template,
        		form = new FormData(),
        		xhr = new XMLHttpRequest(),
        		_self = this;
        	
        	for(var i in valueItem){
        		form.append(i, valueItem[i]);
        	}
			xhr.open("post", "/university/upload/", true);
			xhr.send(form);
			
			xhr.onload = function(){
				var str = "" + xhr.response,
					result = str.match(/\$\d+/)[0],
					respond = _self.parseError(result);
				
				respondCallback(respond);
			};
        },
        reqActValue: function(data, respondCallback){
        	this.set('waiting', true);
	        	this.get('appInstance').actValueAttribute(data, function(err, result){
	        		this.set('waiting', false);

		        		if(!err && result.actValueAttribute){
		        			this.template.setHTML('modal', '');
		        				respondCallback(true);
		        		} else {
		        			alert('Заполните все поля!');
		        		}
	        	}, this);
        },
        parseError: function(result){
			switch(result){
				case '$200':
					this.template.setHTML('modal', '');
						return true;
				case '$1': 
					alert('Размер файла превышает допустимое значение UPLOAD_MAX_FILE_SIZE');
						break;
				case '$2': 
					alert('Размер файла превышает допустимое значение MAX_FILE_SIZE');
						break;
				case '$3': 
					alert('Не удалось загрузить часть файла');
						break;
				case '$4': 
					alert('Файл не был загружен');
						break;
				case '$6': 
					alert('Отсутствует временная папка');
						break;
				case '$7': 
					alert('Не удалось записать файл на диск');
						break;
				case '$8': 
					alert('PHP-расширение остановило загрузку файла');
						break;
				case '$9': 
					alert('Не верный тип файла');
						break;
				case '$10': 
					alert('Укажите документ для загрузки');
						break;
				case '$11': 
					alert('Укажите название ссылки на документ');
						break;
				case '$12': 
					alert('Укажите название документа');
						break;
				case '$13': 
					alert('Укажите дату утверждения');
						break;
				case '$14': 
					alert('Не верное название документа! Пример: Pril1_akkred_2014');
						break;
				case '$15': 
					alert('Файл с таким именем уже существует!');
						break;
			}
			
			return false;
        },
        constrData: function(id, atrid){
        	return {
        		id: id,
        		atrid: atrid,
        		value: arguments[2] || '',
        		nameurl: arguments[3] || '',
        		namedoc: arguments[4] || '',
        		datedoc: arguments[5] || '',
        		file: arguments[2] ? this.renderRef(arguments[2]) : this.template.replace('fileInput'),
        		numrow: arguments[6] || this.get('numrow'),
        		mainid: this.get('mainid') || 0
        	};
        },
        renderRef: function(url){
        	ref = this.template.replace('referfile', {
        		url: url
        	});
        	return ref;
        },
        constrReplace: function(hide, act){
         	return {
        		hide: hide || '',
        		act: act || 'Добавить',
        		none: 'block'
        	};
        },
        unSetActive: function(){
        	var tp = this.template,
        		collect = tp.gel('modalFormAdd.btnView').childNodes,
        		len = collect.length;
        	
    		for(var i = 0; i < len; i++){
    			collect[i].classList.remove('active');
    		}
        },
        parseValue: function(view, nameurl, value){
 			switch(view){
				case "value":
					return value;
				case "file":
					return this.parseUrl(nameurl, value, true);
				case "url":
					return this.parseUrl(nameurl, value);
			}
        },
        parseUrl: function(nameurl, value, isFile){
        	if(isFile){
        		value =  '/' + value;
        	}
        	return this.template.replace('refer', {
    			nameurl: nameurl,
				value: value
        	});
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget,modalFormAdd,value,file,url,fileInput,referfile,refer'},
            valueItem: {value: null},
            view: '',
            valueAttributeItem: {value: null},
            numrow: {value: 0},
            mainid: {value: 0}
        },
        CLICKS: {
        	'addValue-cancel': {
        		event: function(){
        			this.template.setHTML('modal', '');
        		}
        	},
        	checkView: {
        		event: function(e){
        			var targ = e.target,
        				button = targ.getDOMNode(),
        				view = targ.getData('view'); 
        			
        			if(!button.type){
        				return;
        			}
        			
        			this.unSetActive();
        			button.classList.add('active');
        			
        			this.set('view', view);
        			this.fillForm(false, true);
        		}
        	},
        	'remove-file': {
        		event: function(e){
        			var tp = this.template;
        			
        			tp.setHTML('file.file', tp.replace('fileInput'));
        		}
        	},
        	withOutDate: {
        		event: function(e){
        			var tp = this.template,
        				btn = e.target.getDOMNode(),
        				active = /active/.test(btn.classList.value);
        			
        			if(active){
        				btn.classList.remove('active');
        				tp.gel('file.datedoc').disabled = false;
        			} else {
        				btn.classList.add('active');
        				tp.gel('file.datedoc').disabled = true;
        			}
        		}
        	}
        }
    });
};