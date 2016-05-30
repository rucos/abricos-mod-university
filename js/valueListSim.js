var Component = new Brick.Component();
Component.requires = {
    mod: [
        {name: 'sys', files: ['editor.js']},
        {name: '{C#MODNAME}', files: ['lib.js', 'addValueModal.js']}
    ]
};
Component.entryPoint = function(NS){

    var Y = Brick.YUI,
        COMPONENT = this,
        SYS = Brick.mod.sys;

    NS.ValueListSimpleWidget = Y.Base.create('valueListSimpleWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance){
        	this.addValueModal = new NS.AddValueModalWidget({
                srcNode: this.template.gel('modal')
    		});
        },
        destroy: function(){
        	this.template.setHTML('values', "");
        },
        reloadList: function(){
        	var data = {
        		attrid: this.get('currentAttrid'),
        		type: this.get('currentType')
        	};
        	
        	this.set('waiting', true);
	        	this.get('appInstance').valueAttributeList(data, function(err, result){
	        		this.set('waiting', false);
		        		if(!err){
		        			this.set('valueAttributeList', result.valueAttributeList);
		        				this.renderList();
		        		}
	        	}, this);
        },
        renderList: function(){
        	var valueList = this.get('valueAttributeList'),
        		tp = this.template,
        		lst = "",
        		url = '/data-edu/' + this.get('nameSection') + '/';
        	
	        	valueList.each(function(val){
	        		var namedoc = val.get('namedoc'),
	        			value = val.get('value'),
	        			remove = val.get('remove'),
	        			objReplace = {
	        				id: val.get('id')
	        			};
	        		
	        		if(remove){
	        			objReplace.actremove = 'Восстановить';
	        			objReplace.cl = 'class="danger"';
	        			
	        		} else {
	        			objReplace.actremove = 'Удалить';
	        			objReplace.cl = '';
	        		}
	        		
	        		if(namedoc === ''){
	        			objReplace.value = value;
	        		} else {
	        			objReplace.value = tp.replace('refer', {
	        				nameurl: val.get('nameurl'),
	        				url: url + val.get('namedoc') + "_" + val.get('datedoc') + value
	        			});
	        		}
	        		lst += tp.replace('row', objReplace);
	        	}, this);
	        	
	        	tp.setHTML('values', tp.replace('table', {
	        		rows: lst
	        	}));
        },
        removeShow: function(show, id){
        	this.template.toggleView(show, 'row.removegroup-' + id, 'row.remove-' + id);
        },
        reqRemoveValue: function(valueid, remove){
        	var data = {
        		valueid: valueid,
        		remove: remove
        	};
        	
        	this.set('waiting', true);
	        	this.get('appInstance').removeValueAttribute(data, function(err, result){
	        		 this.set('waiting', false);
		        		if(!err){
		        			this.reloadList();
		        		}
	        	}, this);
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget,table,row,refer'},
            valueAttributeList: {value: null},
            currentAttrid: {value: null},
            currentType: {value: null},
            nameSection: {value: ''}
        },
        CLICKS: {
        	"modal-show": {
        		event: function(e){
        			var targ = e.target,
        				id = targ.getData('id'),
        				view = targ.getData('view'),
        				atrid = this.get('currentAttrid'),
        				valueItem = this.addValueModal.constrData(id, atrid);
        			
        			this.addValueModal.showModal(view, valueItem);
        		}
        	},
        	'remove-show': {
        		event: function(e){
        			var id = e.target.getData('id');
        			
        			this.removeShow(true, id);
        		}
        	},
        	'remove-cancel': {
        		event: function(e){
        			var id = e.target.getData('id');
    			
        			this.removeShow(false, id);
        		}
        	},
        	addValue: {
        		event: function(e){
        			var _self = this;
        			
        			this.addValueModal.actValue(function(respond){
        				if(respond){
        					_self.reloadList();
        				}
        			});
        		}
        	},
        	removeValue: {
        		event: function(e){
        			var id = e.target.getData('id');
        			
        			this.reqRemoveValue(id, 1);
        		}
        	},
        	restoreValue: {
        		event: function(e){
        			var id = e.target.getData('id');
        			
        			this.reqRemoveValue(id, 0);
        		}
        	}
        }
    });
};