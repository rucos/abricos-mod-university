var Component = new Brick.Component();
Component.requires = {
    mod: [
        {name: '{C#MODNAME}', files: ['lib.js', 'programList.js']}
    ]
};
Component.entryPoint = function(NS){

    var Y = Brick.YUI,
        COMPONENT = this,
        SYS = Brick.mod.sys;
   
    
    NS.ManagerProgramWidget = Y.Base.create('managerProgramWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance, options){
        	
        	this.programList = new NS.ProgramListWidget({
                srcNode: this.template.gel('programList')
            });
        },
        destructor: function(){
        	if(this.programList){
        		this.programList.destroy();
        	}
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