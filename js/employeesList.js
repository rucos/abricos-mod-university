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
        }
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget,table,row,rowAct'},
            employeesList: {value: null}
        },
        CLICKS: {
        }
    });
};