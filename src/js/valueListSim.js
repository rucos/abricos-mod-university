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
        	var attrid = this.get('currentAttrid');
        	
        	this.set('waiting', true);
	        	this.get('appInstance').valueSimpleList(attrid, function(err, result){
	        		this.set('waiting', false);
		        		if(!err){
		        			this.set('valueSimpleList', result.valueSimpleList);
		        				this.renderList();
		        		}
	        	}, this);
        },
        renderList: function(){
        	var valueList = this.get('valueSimpleList'),
        		tp = this.template,
        		lst = "";
        	
	        	valueList.each(function(val){
	        		var view = val.get('view'),
	        			objReplace = {
	        				id: val.get('id'),
	        				view: view
	        			};
	        		
	        		if(val.get('remove')){
	        			objReplace.actremove = 'Восстановить';
	        			objReplace.cl = 'class="danger"';
	        			objReplace.click = 'restoreValue';
	        		} else {
	        			objReplace.actremove = 'Удалить';
	        			objReplace.cl = '';
	        			objReplace.click = 'remove-show';
	        		}
	        		objReplace.value = this.addValueModal.parseValue(view, val.get('nameurl'), val.get('value'));
	        		
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
            valueSimpleList: {value: null},
            currentAttrid: {value: null},
            currentType: {value: null}
        },
        CLICKS: {
        	"modal-show": {
        		event: function(e){
        			var targ = e.target,
        				id = targ.getData('id'),
        				view = targ.getData('view'),
        				atrid = this.get('currentAttrid');
        			
        			this.addValueModal.showModal(id, atrid, view);
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