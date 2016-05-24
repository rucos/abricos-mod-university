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
        showModal: function(view, id){
        	var tp = this.template,
        		replaceObj = {
        			id: id,
        			hide: '',
        			act: 'Добавить',
        			none: 'block'
        		};
        	
        	if(id > 0){
        		replaceObj.act = 'Именить';
        		replaceObj.hide = 'class="hide"';
        		//запрос
        	} else {
        		this.set('valueList', this.constrData());
        	}
        	
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
        	var valueList = this.get('valueList'),
        		view = this.get('view');
        		
        	return this.template.replace(view, valueList);
        },
        actValue: function(atrid){
        	var valueList = this.get('valueList'),
        		view = this.get('view'),
        		tp = this.template;
        	
        	if(view == 'file'){
        		valueList.nameurl = tp.gel('file.nameurl').value;
        		valueList.namedoc = tp.gel('file.namedoc').value;
        		valueList.subject = tp.gel('file.subject').value;
        		valueList.datedoc = tp.gel('file.datedoc').value;
        		valueList.folder = tp.gel('file.folder').value;
        	} else {
        		valueList.value = tp.gel('value.value').value;
        	}
        	valueList.atrid = atrid;
        	
        	this.reqActValue(valueList);
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
        constrData: function(){
        	return {
        		id: 0,
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
            valueList: {value: null},
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