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
        showModal: function(valueid, atrid, view){
        	this.set('view', view);
        	
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
        		arr = [];
        	
        	if(isEdit){
        		arr[0] = "class='hide'";
        		arr[1] = "Изменить";
        	}
        	
        	if(!click){
        		tp.setHTML('modal', tp.replace('modalFormAdd', this.constrReplace.apply(null, arr)));
        	}
	        	tp.setHTML('modalFormAdd.form', replace);
        },
        replaceForm: function(){
        	var valueItem = this.get('valueItem'),
        		view = this.get('view');
        	
        	return this.template.replace(view, valueItem);
        },
        actValue: function(respondCallback){
        	var valueItem = this.get('valueItem'),
        		view = this.get('view'),
        		tp = this.template;

        	valueItem.view = view;
        	
        	switch(view){
        		case 'file':
        			valueItem.nameurl = tp.gel('file.nameurl').value;
            		valueItem.namedoc = tp.gel('file.namedoc').value;
            		valueItem.datedoc = tp.gel('file.datedoc').value;
            		valueItem.file = tp.gel('fileInput.inputFile') ? tp.gel('fileInput.inputFile').files[0] : '';
            			return this.reqActFiles(valueItem, respondCallback);
        		case 'url':
            		valueItem.nameurl = tp.gel('url.nameurl').value;
            		valueItem.value = tp.gel('url.value').value;
        				return this.reqActValue(valueItem, respondCallback);
        		case 'value':
            		valueItem.value = tp.gel('value.value').value;
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
        		value = valueAttributeItem.get('value'),
        		nameurl = valueAttributeItem.get('nameurl'),
        		arr = [id, atrid, value],
        		date = '';
        	
        	if(!!nameurl){
        		arr[3] = nameurl;
        		value.replace(/(\w+_)(\d+)\.(\d+)\.(\d+)/, function(str, name, day, month, year){
        			arr[4] = name.slice(0, -1);
        			arr[5] = year + "-" + month + "-" + day;
        		});
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
		        		if(!err){
		        			this.template.setHTML('modal', '');
		        				respondCallback(true);
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
        		numrow: arguments[6] || this.get('numrow')
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
					return this.parseUrl(nameurl, value, false);
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
            numrow: {value: 0}
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
        	}
        }
    });
};