var Component = new Brick.Component();
Component.requires = {
    mod: [
        {name: 'sys', files: ['editor.js']},
        {name: '{C#MODNAME}', files: ['lib.js']}
    ]
};
Component.entryPoint = function(NS){

    var Y = Brick.YUI,
        COMPONENT = this,
        SYS = Brick.mod.sys;

    NS.SectionEditorWidget = Y.Base.create('sectionEditorWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance){
        	var tp = this.template,
    			sectionName = appInstance.get('currentSection');
    	
		    	tp.setHTML('panelHead', tp.replace('panelHead', {
		    		sectionName: sectionName
		    	}));
		    	
		    	this.reloadList();
        },
        reloadList: function(){
        	var sectionid = this.get('sectionid');
        	
        	this.set('waiting', true);
	        	this.get('appInstance').valueAttributeList(sectionid, function(err, result){
	        		this.set('waiting', false);
	        		
	        	}, this);
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget,panelHead,table,row'},
            sectionid: {value: 0}
        },
        CLICKS: {
        	close: {
        		event: function(){
        			this.go('struct.view');
        		}
        	}
        }
    });

    NS.SectionEditorWidget.parseURLParam = function(args){
        return {
        	sectionid: args[0]
        };
    };
};