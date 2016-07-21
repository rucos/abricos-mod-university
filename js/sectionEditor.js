var Component = new Brick.Component();
Component.requires = {
    mod: [
        {name: 'sys', files: ['editor.js']},
        {name: '{C#MODNAME}', files: ['lib.js', 'valueListAttribute.js']}
    ]
};
Component.entryPoint = function(NS){

    var Y = Brick.YUI,
        COMPONENT = this,
        SYS = Brick.mod.sys;

    NS.SectionEditorWidget = Y.Base.create('sectionEditorWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance){
           	var tp = this.template;
           	
		    	this.reloadList();
        },
        destructor: function(){
            if (this.valueList){
                this.valueList.destroy();
            }
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
	        				
	        				this.set('title', result.sectionItem.get('title'))
	        				this.set('name', result.sectionItem.get('name'))
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
        			lst += tp.replace('attributeList', [{
        				n: ++n
        			},attr.toJSON()]);
        	});
        	
        	tp.setHTML('attributeList', lst);
        	
        	tp.setHTML('panelHead', tp.replace('panelHead', {
        		title: this.get('title'),
        		name: this.get('name')
        	}))
        },
        setActive: function(id){
        	var tp = this.template,
        		div = tp.one('attributeList.collapse-' + id).getDOMNode(),
        		valueList = tp.one('attributeList.valueList-' + id) || "";
        	
        		div.classList.add('in');
        		
        		if(valueList){
        	    	this.valueList = new NS.ValueListWidget({
    	                srcNode: valueList.getDOMNode(),
    	                sectionid: this.get('sectionid')
    	            });
        		}
        },
        unSetActive: function(){
        	var tp = this.template,
        		collect = tp.gel('attributeList').querySelectorAll('.panel-collapse'),
        		len = collect.length;
        	
        		for(var i = 0; i < len; i++){
        			collect[i].classList.remove('in');
        		}
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget,panelHead,attributeList,attributeItem'},
            sectionid: {value: 0},
            attributeList: {value: null},
            title: {value: ''},
            name: {value: ''}
        },
        CLICKS: {
        	close: {
        		event: function(){
        			this.go('struct.view');
        		}
        	},
        	pickAttr: {
        		event: function(e){
        			var targ = e.target,
        				a = targ.getDOMNode(),
        				attrid = targ.getData('id'),
        				type = targ.getData('type'),
        				insert = targ.getData('insert');
        			
        			if(!a.href){
        				return;
        			}
        			
        			this.unSetActive();
        			this.setActive(attrid);
        			
        			
        			this.valueList.set('currentAttrid', attrid);
        			this.valueList.set('currentType', type);
        			this.valueList.set('insert', insert);
        			this.valueList.reloadList();
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