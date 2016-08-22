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
          	var lib = this.get('appInstance'),
	    		tp = this.template,
	    		div = tp.gel('loading');
          	
         	lib.loadingLineShow(div, true);
        		lib.employeesList(function(err, result){
        		 	lib.loadingLineShow(div, false);;
	        			this.set('employeesList', result.employeesList);
		        			if(!err){
		        				this.renderList();
		        			}
	        	}, this);
        },
        renderList: function(){
        	var tp = this.template,
        		employeesList = this.get('employeesList'),
        		lst = "",
        		select = this.get('select'),
        		relationid = this.get('relationid');
        	
        	employeesList.each(function(employees){
        			lst += this.rowReplaceObj(employees.toJSON());
        	}, this);
        	
        	tp.setHTML('emploees', tp.replace('table', {
        		addButton: select ? "" : tp.replace('addButton'),
        		rows: lst
        	}));
        	
        	if(relationid > 0){
        		this.setSuccess(relationid);
        	}
        },
        rowReplaceObj: function(replaceObj){
        	var select = this.get('select'),
        		tp = this.template;

        	if(replaceObj.remove == 1){
        		replaceObj.act = 'Восстановить';
        		replaceObj.danger = "class='danger'";
        	} else {
        		replaceObj.act = 'Удалить';
        		replaceObj.danger = "";
        	}
        	
        	if(select){
        		replaceObj.btnedit = "";
        	} else {
        		replaceObj.btnedit = tp.replace('btnedit', replaceObj);
        	}
        	
        	return tp.replace('row', replaceObj);
        },
        editCancelRow: function(id, tr, remove){
        	var replaceObj = {
        		id: id,
        		fio: tr.cells[0].firstChild.getAttribute('value'),
        		post: tr.cells[1].firstChild.getAttribute('value'),
        		telephone: tr.cells[2].firstChild.getAttribute('value'),
        		email: tr.cells[3].firstChild.getAttribute('value'),
        		remove: remove
        	};
        	
        	tr.outerHTML = this.rowReplaceObj(replaceObj);
        },
        actEmployees: function(id){
        	var tp = this.template,
        		tr = tp.one('rowAct.rowAct-' + id).getDOMNode().cells,
        		data = {
	        		employeesid: id,
	        		fio: tr[0].firstChild.value,
	        		post: tr[1].firstChild.value,
	        		telephone: tr[2].firstChild.value,
	        		email: tr[3].firstChild.value
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
        	this.template.toggleView(show, 'btnedit.removegroup-' + id, 'btnedit.remove-' + id);
        },
        constructActReplaceObj: function(id, remove){
        	return this.template.replace('rowAct', {
	        		id: id || 0,
	        		remove: remove || 0,
	        		act: id ? 'Править' : 'Добавить',
	        		fio: arguments[2] || "",
	        		post: arguments[3] || "",
	        		telephone: arguments[4] || "",
	        		email: arguments[5] || "",
	        		eventCancel: id ? 'edit-cancel' : 'add-cancel'
        	});
        },
        setSuccess: function(relationid){
        	var row = this.template.one('row.empl-' + relationid).getDOMNode();
        	
        		row.classList.add('success');
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget,table,row,rowAct,btnedit,addButton'},
            employeesList: {value: null},
            select: {value: false},
            relationid: {value: 0}
        },
        CLICKS: {
        	'add-show': {
        		event: function(){
                	var tr = this.template.gel('table.table').insertRow(1);
                	
                	tr.outerHTML = this.constructActReplaceObj();
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
        				tr = targ.getDOMNode().parentNode.parentNode,
        				arr = [
        				       targ.getData('id'),
        				       targ.getData('remove'),
        				       tr.cells[0].textContent,//фио
        				       tr.cells[1].textContent,//должность
        				       tr.cells[2].textContent,//тел
        				       tr.cells[3].textContent//email
        				];
        			
        			tr.outerHTML =  this.constructActReplaceObj.apply(this, arr);
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