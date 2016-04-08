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

    NS.WorkspaceWidget = Y.Base.create('workspaceWidget', SYS.AppWidget, [
        SYS.AppWorkspace
    ], {
    	setActive: function(idLi){
    		var tp = this.template,
    			obj = tp.idMap.widget;
    		
    		for(var i in obj){
				var li = 'widget.' + i;
					if(i == idLi){
						tp.addClass(li, 'active');
					} else {
						tp.removeClass(li, 'active');
					}
    		}
    	}
    }, {
        ATTRS: {
            component: {value: COMPONENT},
            templateBlockName: {value: 'widget'},
            defaultPage: {
            	 value: {
                     component: 'managerTags',
                     widget: 'ManagerTagsWidget'
                 }
            }
        },
        CLICKS: {
        	changeActive: {
        		event: function(e){
        			var targ = e.target,
        				idManager = targ.getData('id'),
        				tp = this.template;
        			
        			if(!targ.getDOMNode().href){
        				return;
        			}
        			
	        		switch(idManager){
	        			case "tegsA" :
	        				this.setActive('tegsLi');
	        					this.go("managerTags.view");
	        						break;
	        			case "structA": 
	        				this.setActive('structLi');
	        						break;
	        		}
        		}
        	}
        }
    });

    NS.ws = SYS.AppWorkspace.build('{C#MODNAME}', NS.WorkspaceWidget);

};
