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
        showModal: function(view, valueItem){
        	var tp = this.template,
        		replaceObj = {
        			hide: '',
        			act: 'Добавить',
        			none: 'block'
        		};
        	
        	if(valueItem.id > 0){
        		replaceObj.act = 'Изменить';
        		replaceObj.hide = 'class="hide"';
        	}
        	
        	this.set('valueItem', valueItem);
        	
        	tp.setHTML('modal', tp.replace('modalFormAdd', replaceObj));
        	
        	this.set('view', view);
        	this.fillForm();
        },
        fillForm: function(){
        	var tp = this.template,
        		replace = this.replaceForm();
        	
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
        		valueItem.subject = tp.gel('file.subject').value;
        		valueItem.datedoc = tp.gel('file.datedoc').value;
        		valueItem.folder = tp.gel('file.folder').value;
        		valueItem.file = tp.gel('file.exampleInputFile').files[0];
        		
        			return this.reqActFiles(valueItem, respondCallback);
        	} else {
        		valueItem.value = tp.gel('value.value').value;
        		return this.reqActValue(valueItem, respondCallback);
        	}
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
				
			xhr.onload = function() {
				var str = "" + xhr.response,
					result = str.match(/\$\d\d\d/)[0],
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
				case '$100': 
					alert('Не верно заполнена форма');
						break;
			}
			
			return false;
        },
        constrData: function(id, atrid){
        	return {
        		id: id,
        		atrid: atrid,
        		value: '',
        		nameurl: '',
        		namedoc: '',
        		subject: '',
        		datedoc: '',
        		folder: ''
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
            view: ''
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
        			this.fillForm();
        		}
        	}
        }
    });
};