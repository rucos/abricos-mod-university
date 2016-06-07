var Component = new Brick.Component();
Component.requires = {
    mod: [
        {name: 'sys', files: ['editor.js']},
        {name: '{C#MODNAME}', files: ['lib.js', 'addValueModal.js']}
    ]
};
Component.entryPoint = function(NS){

    var Y = Brick.YUI,
        COMPONENT = this,
        SYS = Brick.mod.sys;

    NS.ValueListComplexWidget = Y.Base.create('valueListComplexWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance){
        	this.addValueModal = new NS.AddValueModalWidget({
                srcNode: this.template.gel('modal')
    		});
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
		    		lib.attributeList(data, this.renderAttributeList, this);
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
        		th: this.renderThead(tdComp, tdSubComp)
        	}))
        		this.reloadListValue();
        },
        tDataReplace: function(id, value, span){
        	var tp= this.template;
        	
        	return tp.replace('td', {
        		span: span || "",
        		value: tp.replace('referAdd', {
        			nameurl: value + "(+)",
        			id: id,
        			vid: 0,
        			view: 'value',
        			numrow: 0
        		}),
        		add: ''
        	});
        },
        renderThead: function(tdComp, tdSubComp){
        	return this.tRowReplace(tdComp) + this.tRowReplace(tdSubComp);
        },
        tRowReplace: function(td){
        	return this.template.replace('tr', {
        		td: td
        	});
        },
        actRowShow: function(){
        	var tp = this.template,
        		numrow = arguments[3];
        	
        	if(numrow == 0){
        		numrow = tp.gel('table.tBody').rows.length + 1;
        	}
        	
        	this.addValueModal.set('numrow', numrow);
        	
        	this.addValueModal.showModal.apply(this.addValueModal, arguments);
        },
        reloadListValue: function(){
           	var attrid = this.get('currentAttrid');
           	
	    	this.set('waiting', true);
	        	this.get('appInstance').valueComplexList(attrid, function(err, result){
	        		var value = result.valueComplexList;
	        			this.set('waiting', false);
	        			if(!err){
	        				if(value){
		        				this.set('valueComplexList', value);
	        						this.renderValueList();
	        				}
	        			}
	        	}, this);
        },
        renderValueList: function(){
        	var valueComplexList = this.get('valueComplexList'),
        		tp = this.template,
        		tr = "";
        	
        	for(var i in valueComplexList){
            	tr += tp.replace('tr', {
            		td: this.parseRowValue(valueComplexList[i], i) 
            	});   
        	}
        	
        	tp.setHTML('table.tBody', tr);
        },
        parseRowValue: function(objValue, numrow){
        	var tp = this.template,
        		td = "";
        	
        	for(var i in objValue){
        		var	curObj = objValue[i],
        			item = "";
        		
        		for(var j = 0; j < curObj.length; j++){
        			var value = "";
        			
        			switch(curObj[j].view){
        				case "value":
        					value = curObj[j].value;
        						break;
        				case "file":
        					value = this.parseUrl(curObj[j].nameurl, curObj[j].value, true);
        						break;
        				case "url":
        					value = this.parseUrl(curObj[j].nameurl, curObj[j].value, false);
        						break;
        			}
        			
        			item += tp.replace('item', {
        				value: value,
        				vid: curObj[j].id,
        				view: curObj[j].view,
        				id: curObj[j].attributeid,
        				numrow: i
        			});
        		}
        		
     			td += tp.replace('td', {
    				span: "",
    				value: item,
    				add: tp.replace('referAdd', {
            			nameurl: "(+)",
            			id: i,
            			vid: 0,
            			view: 'value',
            			add: '',
            			numrow: numrow
            		})
    			});
        	}
        	return td;
        },
        parseUrl: function(nameurl, value, isFile){
        	if(isFile){
        		value =  '/' + value;
        	}
        	
        	return this.template.replace('refer', {
    			nameurl: nameurl,
				value: value
        	});
        },
        removeValue: function(valueid){
        	var data = {
            		valueid: valueid,
            		remove: 1
            	};
        	
	    	this.set('waiting', true);
	        	this.get('appInstance').removeValueAttribute(data, function(err, result){
	        		this.set('waiting', false);
	        			if(!err){
	        				this.reloadListValue();
	        			}
	        	}, this);
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget,table,tr,td,refer,referAdd,item'},
            valueComplexList: {value: null},
            currentAttrid: {value: null},
            currentType: {value: null},
            sectionid: {value: null},
            compositeObj: {value: null},
            rowSpan: {value: 0}
        },
        CLICKS: {
        	'modal-show': {
        		event: function(e){
        			var targ = e.defineTarget,
        				valueid = targ.getData('id'),
        				view = targ.getData('view'),
        				atrid = targ.getData('atrid'),
        				numrow = targ.getData('numrow');
        			
        			this.actRowShow(valueid, atrid, view, numrow);
        		}
        	},
        	remove: {
        		event: function(e){
        			var targ = e.defineTarget,
        				valueid = targ.getData('id');

        			this.removeValue(valueid);
        		}
        	},
        	addValue: {
        		event: function(e){
        			var _self = this;
        			
        			this.addValueModal.actValue(function(respond){
        				if(respond){
        					_self.reloadListValue();
        				}
        			});
        		}
        	}
        }
    });
};