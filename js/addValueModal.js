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
        	console.log(view);
        	return this.template.replace(view, valueItem);
        },
        actValue: function(atrid){
        	var valueItem = this.get('valueItem'),
        		view = this.get('view'),
        		tp = this.template;
        	
        	if(view == 'file'){
        		valueItem.nameurl = tp.gel('file.nameurl').value;
        		valueItem.namedoc = tp.gel('file.namedoc').value;
        		valueItem.subject = tp.gel('file.subject').value;
        		valueItem.datedoc = tp.gel('file.datedoc').value;
        		valueItem.folder = tp.gel('file.folder').value;
        	} else {
        		valueItem.value = tp.gel('value.value').value;
        	}
        	valueItem.atrid = atrid;
        	
        	this.reqActValue(valueItem);
        },
        reqActValue: function(data){
        	this.set('waiting', true);
	        	this.get('appInstance').actValueAttribute(data, function(err, result){
	        		this.set('waiting', false);
		        		if(!err){
		        			this.template.setHTML('modal', '');
		        		}
	        	}, this);
        },
        constrData: function(id){
        	return {
        		id: id,
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