var Component = new Brick.Component();
Component.requires = {
    mod: [
        {name: 'sys', files: ['application.js']},
        {name: '{C#MODNAME}', files: ['model.js']}
    ]
};
Component.entryPoint = function(NS){

	NS.roles = new Brick.AppRoles('{C#MODNAME}', {
        isAdmin: 50
    });

    var COMPONENT = this,
        SYS = Brick.mod.sys;

    SYS.Application.build(COMPONENT, {}, {
        initializer: function(){
            this.cacheClear();
            NS.roles.load(function(){
                this.initCallbackFire();
            }, this);
        },
        cacheClear: function(){
        	
        }
    }, [], {
        REQS: {
        	
        },
        ATTRS: {
        	isLoadAppStructure: {value: true}
        },
        URLS: {
        	ws: "#app={C#MODNAMEURI}/wspace/ws/",
        	managerTags: {
        		view: function(){
        			 return this.getURL('ws') + 'managerTags/ManagerTagsWidget';
        		}
        	}
        }
    });
};