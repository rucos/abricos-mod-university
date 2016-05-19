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

    NS.ValueListComplexWidget = Y.Base.create('valueListComplexWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance){
        
        },
        destroy: function(){
        	this.template.setHTML('values', "");
        },
        reloadList: function(){
        	var lib = this.get('appInstance'),
	    		sectionid = this.get('sectionid'),
	    		complexid = this.get('currentAttrid'),
	    		data = lib.dataAttributeList(sectionid, true, complexid);
        	
		    	this.set('waiting', true);
		        	this.get('appInstance').attributeList(data, this.renderAttributeList, this);
        },
        renderAttributeList: function(err, result){
        	var compositObj = {};
        	
    		this.set('waiting', false);
    			if(!err){
    				result.attributeList.each(function(attribute){
    					var id = attribute.get('id'),
    						cid = attribute.get('compositeid'),
    						name = attribute.get('nameattribute'),
    						type = attribute.get('typeattribute');
    						
    						switch(type){
    							case 'composite':
    								compositObj[id] = [name, []];
    									break;
    							case 'subcomposite':
    								compositObj[cid][1].push([id, name]);
    								this.set('rowSpan', 2);
    									break;
    						}
    				}, this);
    				this.set('compositeObj', compositObj);
    				this.renderList();
    			}
        },
        renderList: function(){
        	var tp = this.template,
        		compositeObj = this.get('compositeObj'),
        		rowspan = this.get('rowSpan'),
        		tdComp = "",
        		tdSubComp = "",
        		colspan = 0,
        		len = 0,
        		currArr = '',
        		col = '';
        		
        	
        	for(var i in compositeObj){
        		col = 'rowspan=' + rowspan;
        		
        		currArr = compositeObj[i][1];
        		len = currArr.length;
        		
        		if(len > 0){
        			for(var j = 0; j < len; j++){
            			tdSubComp += this.tDataReplace(currArr[j][0], currArr[j][1]); 
        			}
        			col = 'colspan=' + len;
        		}
        		tdComp += this.tDataReplace(i, compositeObj[i][0], col);
        	}
        	
        	tp.setHTML('values', tp.replace('table', {
        		th: this.renderThead(tdComp, tdSubComp),
        		rows: "" 
        	}))
        },
        tDataReplace: function(id, value, span){
        	return this.template.replace('td', {
        		id: id,
        		span: span || "",
        		value: value
        	});
        },
        renderThead: function(tdComp, tdSubComp){
        	return this.tRowReplace(tdComp) + this.tRowReplace(tdSubComp);
        },
        tRowReplace: function(td){
        	return this.template.replace('tr', {
        		td: td
        	});
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget,table,tr,td'},
            valueAttributeList: {value: null},
            currentAttrid: {value: null},
            currentType: {value: null},
            sectionid: {value: null},
            compositeObj: {value: null},
            rowSpan: {value: 0}
        },
        CLICKS: {
        	
        }
    });
};