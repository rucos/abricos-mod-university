var Component = new Brick.Component();
Component.requires = {
    mod: [
        {name: '{C#MODNAME}', files: ['lib.js', 'employeesList.js']}
    ]
};
Component.entryPoint = function(NS){

    var Y = Brick.YUI,
        COMPONENT = this,
        SYS = Brick.mod.sys;
   
    
    NS.ManagerEmployeesWidget = Y.Base.create('managerEmployeesWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance, options){
        	this.emploees = new NS.EmployeesListWidget({
        		srcNode: this.template.gel('employeesList')
        	});
        },
        destructor: function(){
        	if(this.emploees){
        		this.emploees.destroy();
        	}
        }
    }, {
        ATTRS: {
            component: {value: COMPONENT},
            templateBlockName: {value: 'widget'}
        },
        CLICKS: {
        	
        }
    });
};