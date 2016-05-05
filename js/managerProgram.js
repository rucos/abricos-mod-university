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
   
    
    NS.ManagerProgramWidget = Y.Base.create('managerProgramWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance, options){
        	
        },
        destructor: function(){
        	
        }
    }, {
        ATTRS: {
            component: {value: COMPONENT},
            templateBlockName: {value: 'widget'}
        },
        CLICKS: {
        	appendProgram: {
        		event: function(e){
        			var programid = e.target.getData('id');
        			
        			this.go('program.act', programid);
        		}
        	}
        }
    });
};