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
        		lst = "",
        		n = 0;
        	
        	employeesList.each(function(employees){
        		lst += tp.replace('row', [{
        			n: ++n,
        			act: 'Удалить'
        		}, employees.toJSON()]);
        	}, this);
        	
        	tp.setHTML('emploees', tp.replace('table', {
        		rows: lst
        	}));
        },
        actEmployeesRowShow: function(employeesid){
        	var tp = this.template,
        		tr = tp.gel('table.table').insertRow(1),
        		objReplace = {
        			act: "Добавить",
        			fio: '',
        			employeesid: employeesid
        		};
        	
        	tr.outerHTML = tp.replace('rowAct', objReplace);
        },
        actEmployees: function(id){
        	var tp = this.template,
        		tr = tp.one('rowAct.rowAct-' + id).getDOMNode().cells,
        		data = {
	        		employeesid: id,
	        		fio: tr[1].firstChild.value
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
        	actEmployees: {
        		event: function(e){
        			var id = e.target.getData('id');
        			
        			this.actEmployees(id);
        		}
        	}
        }
    });
};