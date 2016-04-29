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
        	var lib = this.get('appInstance'),
        		sectionid = this.get('sectionid'),
        		data = lib.dataAttributeList(sectionid, true); 
        	
        	this.set('waiting', true);
	        	this.get('appInstance').attributeList(data, function(err, result){
	        		this.set('waiting', false);
	        			if(!err){
	        				this.set('attributeList', result.attributeList);
	        					this.renderList();
	        			}
	        		
	        	}, this);
        },
        renderList: function(){
        	var attributeList = this.get('attributeList'),
        		tp = this.template,
        		lst = "",
        		n = 0;
        	
        	attributeList.each(function(attr){
        			lst += tp.replace('attributeItem', [{
        				n: ++n
        			},attr.toJSON()]);
        	});
        	
        	tp.setHTML('attributeList', tp.replace('attributeList', {
        		li: lst
        	}));
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget,panelHead,attributeList,attributeItem'},
            sectionid: {value: 0},
            attributeList: {value: null}
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