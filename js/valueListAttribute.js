var Component = new Brick.Component();
Component.requires = {
    mod: [
        {name: 'sys', files: ['editor.js']},
        {name: '{C#MODNAME}', files: ['lib.js', 'valueListCom.js', 'valueListSim.js']}
    ]
};
Component.entryPoint = function(NS){

    var Y = Brick.YUI,
        COMPONENT = this,
        SYS = Brick.mod.sys;

    NS.ValueListWidget = Y.Base.create('valueListWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance){
        	var tp = this.template,
        		src = tp.gel('values'); 
        	
        	this.valueSimple = new NS.ValueListSimpleWidget({
                srcNode: src
    		});
        	
        	this.valueComplex = new NS.ValueListComplexWidget({
                srcNode: src
    		});
        },
        destructor: function(){
            if (this.valueSimple){
                this.valueSimple.destroy();
            }
            if (this.valueComplex){
                this.valueComplex.destroy();
            }
        },
        reloadList: function(){
        	var type = this.get('currentType');
    	
	    	if(type == 'simple'){
	    		this.renderList(this.valueSimple, type);
	    	} else {
	    		this.renderList(this.valueComplex, type);
	    	}
	    	
        }, 
        renderList: function(value, type){
        	var attrid = this.get('currentAttrid');
        	
        	value.set('currentType', type);
        	value.set('currentAttrid', attrid);
        	
        	value.reloadList();
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget'},
            valueAttributeList: {value: null},
            currentAttrid: {value: null},
            currentType: {value: null}
        }
    });
};