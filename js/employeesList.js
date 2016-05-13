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
        		var remove = employees.get('remove'),
        			objReplace = {
        				act: 'Удалить',
            			danger: ''
        			};
        		
        		if(remove){
        			objReplace.act = 'Восстановить';
        			objReplace.danger = "class='danger'";
        		}
        		lst += tp.replace('row', [objReplace, employees.toJSON()]);
        	}, this);
        	
        	tp.setHTML('emploees', tp.replace('table', {
        		rows: lst
        	}));
        },
        actEmployeesRowShow: function(employeesid, parent){
        	var tp = this.template,
        		tr = parent || tp.gel('table.table').insertRow(1),
        		objReplace = {
        			act: "Добавить",
        			fio: '',
        			employeesid: employeesid,
        			eventCancel: 'add-cancel'
        		};
        	
        	if(employeesid > 0){
        		objReplace.act = "Изменить";
        		objReplace.fio = tr.cells[0].textContent;
        		objReplace.eventCancel = 'edit-cancel';
        	}
        	
        	tr.outerHTML = tp.replace('rowAct', objReplace);
        },
        editCancelRow: function(id, tr){
        	var tp = this.template,
        		fio = tr.cells[0].firstChild.getAttribute('value');
        	
        	tr.outerHTML = tp.replace('row', {
        		id: id,
        		FIO: fio,
        		act: 'Удалить'
        	});
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
        			this.actEmployeesRowShow(0);
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
        				tr = targ.getDOMNode().parentNode.parentNode;
        			
        			this.actEmployeesRowShow(id, tr);
        		}
        	},
        	'edit-cancel': {
        		event: function(e){
        			var targ = e.target,
        				id = targ.getData('id'),
        				tr = targ.getDOMNode().parentNode.parentNode;
        			
        			this.editCancelRow(id, tr);
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
        			var targ = e.target, 
        				id = targ.getData('id');
        			
        				this.removeEmployees(id, 1);
        		}
        	}
        }
    });
};