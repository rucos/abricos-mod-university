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
        	var tp = this.template;
        		 
        	this.valueSimple = new NS.ValueListSimpleWidget({
                srcNode: tp.gel('valuesSimple')
    		});
        	
        	this.valueComplex = new NS.ValueListComplexWidget({
                srcNode: tp.gel('valuesComplex'),
                sectionid: this.get('sectionid')
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
	    		this.renderList(this.valueSimple);
	    		this.valueComplex.destroy();
	    	} else {
	    		this.renderList(this.valueComplex);
	    		this.valueSimple.destroy();
	    	}
        }, 
        renderList: function(value, type){
        	var attrid = this.get('currentAttrid'),
	        	type = this.get('currentType'),
	    		insert = this.get('insert');
        	
        	value.set('currentAttrid', attrid);
        	value.set('currentType', type);
        	value.set('insert', insert);
        	
        	value.reloadList();
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget'},
            valueAttributeList: {value: null},
            sectionid: {value: null},
            currentAttrid: {value: null},
            currentType: {value: null},
            insert: {value: 0}
        }
    });
};