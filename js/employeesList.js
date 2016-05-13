var Component = new Brick.Component();
Component.requires = {
    mod: [
        {name: '{C#MODNAME}', files: ['lib.js']}
    ]
};
Component.entryPoint = function(NS){

    var Y = Brick.YUI,
        COMPONENT = this,
        SYS = Brick.mod.sys;
    
 
    NS.EmployeesListWidget = Y.Base.create('employeesListWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance){
        	this.reloadList();
        },
        reloadList: function(){
        	this.set('waiting', true);
	        	this.get('appInstance').employeesList(function(err, result){
	        		this.set('waiting', false);
	        			this.set('employeesList', result.employeesList);
	        			if(!err){
	        				this.renderList();
	        			}
	        	}, this);
        },
        renderList: function(){
        	var tp = this.template,
        		employeesList = this.get('employeesList'),
        		lst = "";
        	
        	employeesList.each(function(employees){
        		var id = employees.get('id'),
        			fio = employees.get('FIO'),
        			remove = employees.get('remove');
        		
        			lst += this.rowReplaceObj(id, fio, remove);
        	}, this);
        	
        	tp.setHTML('emploees', tp.replace('table', {
        		rows: lst
        	}));
        },
        actEmployeesRowShow: function(id, remove, parent){
        	var tp = this.template,
        		tr = parent || tp.gel('table.table').insertRow(1);
        		
        		tr.outerHTML = this.rowActReplaceObj(id, remove, tr)
        },
        rowActReplaceObj: function(id, remove, tr){
        	var replaceObj = {
	    			id: id,
	        		remove: remove
        		},
        		collect = tr.cells[0]; 
        	
        	if(collect){
        		replaceObj.act = 'Править';
        		replaceObj.fio = collect.textContent;
        		replaceObj.eventCancel = 'edit-cancel';
        	} else {
        		replaceObj.act = 'Добавить';
        		replaceObj.fio = "";
        		replaceObj.eventCancel = 'add-cancel';
        	}
        	
        	return this.template.replace('rowAct', replaceObj);
        },
        rowReplaceObj: function(id, fio, remove){
        	var replaceObj = {
        			id: id,
            		fio: fio,
            		remove: remove
        		};
        	
        	if(remove == 1){
        		replaceObj.act = 'Восстановить';
        		replaceObj.danger = "class='danger'";
        	} else {
        		replaceObj.act = 'Удалить';
        		replaceObj.danger = "";
        	}
        	
        	return this.template.replace('row', replaceObj);
        },
        editCancelRow: function(id, tr, remove){
        	var fio = tr.cells[0].firstChild.getAttribute('value');
        	
        	tr.outerHTML = this.rowReplaceObj(id, fio, remove);
        },
        actEmployees: function(id){
        	var tp = this.template,
        		tr = tp.one('rowAct.rowAct-' + id).getDOMNode().cells,
        		data = {
	        		employeesid: id,
	        		fio: tr[0].firstChild.value
	        	};
        	
        	this.reqActEmployees(data);
        },
        removeEmployees: function(id, remove){
			var data = {
					employeesid: id,
					remove: remove
				};
			
			this.reqActEmployees(data);
        },
        reqActEmployees: function(data){
        	this.set('waiting', true);
	        	this.get('appInstance').actEmployees(data, function(err, result){
	        		this.set('waiting', false);
	        			if(!err){
	        				this.reloadList();
	        			}
	        	}, this);
        },
        removeShow: function(show, id){
        	this.template.toggleView(show, 'row.removegroup-' + id, 'row.remove-' + id);
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget,table,row,rowAct'},
            employeesList: {value: null}
        },
        CLICKS: {
        	'add-show': {
        		event: function(){
        			this.actEmployeesRowShow(0, 0);
        		}
        	},
        	'add-cancel': {
        		event: function(e){
        			var tr = e.target.getDOMNode().parentNode.parentNode;
        			
        			tr.remove();
        		}
        	},
        	'edit-show': {
        		event: function(e){
        			var targ = e.target,
        				id = targ.getData('id'),
        				tr = targ.getDOMNode().parentNode.parentNode,
        				remove = targ.getData('remove');
        			
        			this.actEmployeesRowShow(id, remove, tr);
        		}
        	},
        	'edit-cancel': {
        		event: function(e){
        			var targ = e.target,
        				id = targ.getData('id'),
        				tr = targ.getDOMNode().parentNode.parentNode,
        				remove = targ.getData('remove');
        			
        			this.editCancelRow(id, tr, remove);
        		}
        	},
        	actEmployees: {
        		event: function(e){
        			var id = e.target.getData('id');
        			
        			this.actEmployees(id);
        		}
        	},
        	'remove-show': {
        		event: function(e){
        			var targ = e.target, 
        				id = targ.getData('id'),
        				remove = targ.getData('remove');
        			
        			if(remove == 0){
        				this.removeShow(true, id);
        			} else {
        				this.removeEmployees(id, 0);
        			}
        		}
        	},
        	'remove-cancel': {
        		event: function(e){
        			var id = e.target.getData('id');
        			
        			this.removeShow(false, id);
        		}
        	},
        	removeEmployees: {
        		event: function(e){
        			var id = e.target.getData('id');
        			
        				this.removeEmployees(id, 1);
        		}
        	}
        }
    });
};