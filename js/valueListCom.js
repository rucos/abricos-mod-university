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
        renderList: function(){//Заполнение tHead
        	var tp = this.template,
        		compositeObj = this.get('compositeObj'),
        		rowspan = this.get('rowSpan'),
        		insert = this.get('insert'),
        		tdComp = "",
        		tdSubComp = "",
        		len = 0,
        		currArr = '',
        		col = '';
        	
        	for(var i in compositeObj){
        		col = 'rowspan=' + rowspan;
        		currArr = compositeObj[i][1];
        		len = currArr.length;
        		
        		if(len > 0){
        			for(var j = 0; j < len; j++){
            			tdSubComp += this.tdReplace(currArr[j][1], '', currArr[j][0]);
        			}
        			col = 'colspan=' + len;
        			
        			tdComp += this.tdReplace(compositeObj[i][0], col);
        		} else {
        			tdComp += this.tdReplace(compositeObj[i][0], col, i);
        		}
        	}
        	
        	tp.setHTML('values', tp.replace('table', {
        		th: this.renderThead(tdComp, tdSubComp)
        	}))
        		this.reloadListValue();
        },
        tdReplace: function(value, span, id){
        	var insert = this.get('insert');
        	
	        	return this.template.replace('td', {
	        		span: span,
	        		value: insert != 2 || !id ? value : this.referAddReplace(value, id),
	        		add: ''
	        	});
        },
        referAddReplace: function(value, atrid, numrow, valueid, view){
        	return this.template.replace('referAdd', {
    			nameurl: value + "(+)",
    			id: atrid,
    			vid: valueid || 0,
    			view: view || 'value',
    			numrow: numrow || 0
    		})
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
	        		var insert = this.get('insert');//тип таблицы
	        		
	        			this.set('waiting', false);
	        			if(!err){
	        				this.set('valueComplexList', result.valueComplexList);
	        					if(insert == 1){
	        						this.renderAutoValueList();
	        					} else {
	        						this.renderValueList();
	        					}
	        			}
	        	}, this);
        },
        renderAutoValueList: function(){//Заполнение tBody авто таблицы
        	var valueComplexList = this.get('valueComplexList'),
    			tp = this.template,
    			curArray = "",
    			tr = "",
    			td,
    			isSpan = "",
    			rowspan = "";
    		
       
        	for(var i in valueComplexList){
        		rowspan = "rowspan=" + this.determineSpan(valueComplexList[i]);
        		
        		isSpan = true;
        		
	        		for(var z = 0; z < 3; z++){
	        			td = "";
	        			
	            		for(var j in valueComplexList[i]){
	            			curArray = valueComplexList[i][j];
		            			if(curArray[z]){
		            				td += tp.replace('td', {
		            					span: isSpan ? rowspan : "",
		            					value: curArray[z].value,
		            					add: this.parseButtonGroup(curArray[z])
		            				});
		            				
		            				isSpan = false;
		            			}
	            		}
	            		tr += tp.replace('tr', {
	            			td: td
	            		});
	        		}
        	}
        	tp.setHTML('table.tBody', tr);
        },
        determineSpan: function(obj){
        	var cnt = 0;
        	
        	for(var i in obj){
        		if(cnt == 1){
        			return obj[i].length;
        		} else {
        			cnt++;
        		}
        	}
        },
        renderValueList: function(){//Заполнение tBody ручной таблицы
        	var valueComplexList = this.get('valueComplexList'),
        		tp = this.template,
        		tr = "";
        	
        	if(valueComplexList){
               	for(var i in valueComplexList){
	                	tr += tp.replace('tr', {
	                		td: this.parseRowValue(valueComplexList[i], i) 
	                	});
            	}
        	}
        	tp.setHTML('table.tBody', tr);
        },
        parseRowValue: function(objValue, numrow){
        	var tp = this.template,
        		td = "", isRelation, curObj, item;
        		
        	for(var i in objValue){
        		curObj = objValue[i];
        		isRelation = false;
        		item = "";
        		
        		for(var j = 0; j < curObj.length; j++){
        			if(curObj[j].relationid > 0){
        				isRelation = true;
        			}
        			
        			item += tp.replace('item', {
        				value: this.addValueModal.parseValue(curObj[j].view, curObj[j].nameurl, curObj[j].value),
        				btnGroup: this.parseButtonGroup(curObj[j])
        			});
        		}
        		
     			td += tp.replace('td', {
    				span: "",
    				value: item,
    				add: isRelation ? "" : this.referAddReplace("", i, numrow)
    			});
        	}
        	
        	return td;
        },
        parseButtonGroup: function(obj){
        	if(obj.relationid > 0){
        		return "";
        	} else {
            	return this.template.replace('btnGroup', {
            		vid: obj.id,
            		view: obj.view,
            		id: obj.attributeid,
            		numrow: obj.numrow
            	});
        	}
        },
        removeAct: function(valueid, show){
        	var tp = this.template,
        		remove = "";
        	
	        	if(show){
	        		remove = tp.replace('remove', {
	    				id: valueid
	    			})
	        	}
	        	
				tp.setHTML('btnGroup.remove-' + valueid, remove);
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
            templateBlockName: {value: 'widget,table,tr,td,referAdd,item,remove,btnGroup'},
            valueComplexList: {value: null},
            currentAttrid: {value: null},
            currentType: {value: null},
            sectionid: {value: null},
            compositeObj: {value: null},
            rowSpan: {value: 0},
            insert: {value: 0}
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
        	'remove-show': {
        		event: function(e){
        			var id = e.defineTarget.getData('id');
        			
        				this.removeAct(id, true);
        		}
        	},
        	'remove-hide': {
        		event: function(e){
        			var id = e.target.getData('id');
        			
        				this.removeAct(id);
        		}
        	},
        	removeValue: {
        		event: function(e){
        			var valueid = e.target.getData('id');

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