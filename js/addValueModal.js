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
        	
        	if(view == 'file'){
        		valueItem.nameurl = tp.gel('file.nameurl').value;
        		valueItem.namedoc = tp.gel('file.namedoc').value;
        		valueItem.datedoc = tp.gel('file.datedoc').value;
        		valueItem.file = tp.gel('file.exampleInputFile').files[0];
        		
        			return this.reqActFiles(valueItem, respondCallback);
        	} else {
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
        		date = value.match(/\d+\.\d+\.\d+/)[0].split('.');
        		
        		arr[3] = nameurl;
        		arr[4] = value.match(/\w+_/g)[0].slice(0, -1);
        		arr[5] = date[2] + '-' + date[1] + '-' + date[0];
        		
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
				case '$9': 
					alert('Не верный тип файла');
						break;
				case '$10': 
					alert('Укажите документ для загрузки');
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
        		datedoc: arguments[5] || ''
        	};
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
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget,modalFormAdd,value,file'},
            valueItem: {value: null},
            view: '',
            valueAttributeItem: {value: null}
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
        	}
        }
    });
};