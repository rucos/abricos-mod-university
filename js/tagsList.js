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
    
 
    NS.TagsListWidget = Y.Base.create('tagsListWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance){
        	
        },
        reloadList: function(){
        	var sectionid = this.get('sectionid');
        	this.set('waiting', true);
	        	this.get('appInstance').attributeList(sectionid, function(err, result){
	        		this.set('waiting', false);
	        			this.set('attributeList', result.attributeList);
	        				this.renderList();
	        	}, this);
        },
        renderList: function(){
        	var attributeList = this.get('attributeList'),
        		tp = this.template,
        		arrComplex = [],
        		arrComposit = [],
        		simpleRows = "",
        		rows = "",
        		lst = "";
        		
        	attributeList.each(function(atrr){
        		var type = atrr.get('typeattribute');
	        		switch(type){
	        			case 'simple':
	        				simpleRows += this.renderRow(atrr, 'row', 0);
	        					return;
	        			case 'complex':
	        				arrComplex.push(atrr);
	        					return;
	        			case 'composite':
	        				arrComposit.push(atrr);
	        					return;
	        		}
        	}, this);
        	
        	lst += this.parsingSimple(simpleRows);
        	
	        	for(var i = 0; i < arrComplex.length; i++){
	        			rows = this.parsingComposit(arrComplex[i].get('id'), arrComposit);
	        			
	        			lst += this.parsingComplex(arrComplex[i], rows);
	        	}
        	
        	tp.setHTML('tags', lst);
        },
        parsingSimple: function(rows){
        	var tp = this.template,
	    		panel = "";
    		
			if(rows){
				panel = tp.replace('panel', {
					view: 'success',
					type: 'Простые атрибуты',
					body: this.renderTable(rows, 0)
				});
			}

			return panel;
        },
        parsingComposit: function(id, arrComposit){
        	var len = arrComposit.length,
        		tp = this.template,
        		rows = "";
        	
 			for(var i = 0; i < len; i++){
				var complexid = arrComposit[i].get('complexid');
					if(complexid === id){
						rows += this.renderRow(arrComposit[i], 'row', id); 
					}
			}
 			
 			return rows;
        },
        parsingComplex: function(complex, rows){
        	var tp = this.template,
        		panelHead = "",
        		panel = "";
        		
				panelHead = this.renderRow(complex, 'panelhead', complex.get('id'));
				
				panel = tp.replace('panel', {
					view: 'warning',
					type: 'Сложный атрибут:' + panelHead,
					body: this.renderTable(rows, complex.get('id'))
				});
			
			return panel;
        },
        renderRow: function(attr, block, complexid){
        	var tp = this.template,
        		row = "";
        	
	          	row = tp.replace(block, [{
	          		compositid: attr.get('id'),
	          		complexid: complexid,
	          		locate: attr.get('locate') ? 'Да' : 'Нет',
	    			remove: attr.get('remove') ? 'Восстановить' : 'Удалить'
	    		}, attr.toJSON()]);
          	
          	return row;
        },
        renderTable: function(rows, complexid){
        	return this.template.replace('table', {
        		id: complexid,
        		rows: rows 
        	});
        },
        showFormAddAtr: function(show, type){
        	var tp = this.template,
        		block = show ? 
        				tp.replace(type, {
        					divs: tp.replace('addAtr', {type: type})
        				}) : '';
        	
        	tp.setHTML('formAddAtr', block);
        },
        appendAttribute: function(type, complexid){
        	var tp = this.template,
        		inputs = this.getNode(type, complexid),
        		nameattribute = inputs.nameattribute.value,
        		applyattribute = inputs.applyattribute.value,
        		locate = inputs.locate.checked,
        		data = this.constructDataAttribute(complexid, 0, type, nameattribute, applyattribute, locate);
        		
        		this.reqActAttribute(data);
        },
        getNode: function(type, id){
        	var tp = this.template,
        		ret = {
        			nameattribute: "",
        			applyattribute: "",
        			locate: ""
        		};
        	
        	if(id){//добавляем составной
        		ret.nameattribute = tp.one(type + '.nameattribute-' + id).getDOMNode();
        		ret.applyattribute = tp.one(type + '.applyattribute-' + id).getDOMNode();
        		ret.locate = tp.one(type + '.locate-' + id).getDOMNode();
        	} else {//добавляем простой или сложный
        		ret.nameattribute = tp.gel(type + '.nameattribute');
        		ret.applyattribute = tp.gel(type + '.applyattribute');
        		ret.locate = tp.gel('addAtr.locate');
        	}
        	
        	return ret;
        },
        reqActAttribute: function(data){
        	this.set('waiting', true);
	        	this.get('appInstance').actAttribute(data, function(err, result){
	        		this.set('waiting', false);
	        			if(!data.compositid){
		        			this.showFormAddAtr(false, '');
	        				this.reloadList(this.get('sectionid'));
	        			}
	        	}, this);
        },
        showAtributeRow: function(complexid, compositid, type, parent){
        	var tp = this.template,
        		table = tp.one('table.tbl-'+complexid).getDOMNode(),
        		tbody =  table.tBodies[0].innerHTML,
        		replaceObj = {
	        		nameattribute: "",
	        		applyattribute: "",
	        		check: "",
	        		edit: "Добавить",
	        		compositid: compositid,
	        		complexid: complexid,
	        		click: "appendAtr",
	        		type: type,
	        		key: parent ? compositid : complexid
        		};
        	
        	if(parent){
        		replaceObj.nameattribute = parent.cells[0].textContent;
        		replaceObj.applyattribute = parent.cells[1].textContent;
        		replaceObj.check = parent.cells[2].textContent === 'Да' ? 'checked' : '';
        		replaceObj.edit = "Изменить";
        		replaceObj.id = compositid;
        		replaceObj.click = 'actCompositAtr';
        			
        		parent.innerHTML = this.replaceAtributeRow(replaceObj);
			} else {
				table.tBodies[0].innerHTML = this.replaceAtributeRow(replaceObj) + tbody;
			}
        },
        renderEditAtributeRow: function(complexid, compositid, type, parent, edit){
        	var tp = this.template,
        		inputs = this.getNode("composite", compositid),
        		nameattribute = inputs.nameattribute,
        		applyattribute = inputs.applyattribute,
        		locate = inputs.locate,
        		replaceObj = this.constructDataAttribute(
        				complexid, 
        				compositid, 
        				type, 
        				nameattribute.getAttribute('value'), 
        				applyattribute.getAttribute('value'), 
        				locate.getAttribute('checked')
        		);

        	if(edit){
        		replaceObj.nameattribute = nameattribute.value;
        		replaceObj.applyattribute = applyattribute.value;
        		replaceObj.locate = locate.checked;
        		
        		this.reqActAttribute(replaceObj);
        	}
        	replaceObj.remove = "Удалить";
        	replaceObj.locate = replaceObj.locate ? "Да" : "Нет";
        	
        	parent.innerHTML = tp.replace('row', replaceObj);
        },
        replaceAtributeRow: function(obj){
        	return this.template.replace('composite', obj);
        },
        cancelAtributeRow: function(complexid, compositid, type, parent){
        	if(compositid > 0){
        		this.renderEditAtributeRow(complexid, compositid, type, parent, false);
        	} else {
        		parent.remove();	
        	}
        },
        constructDataAttribute: function(){
        	return {
    			sectionid: this.get('sectionid'),
    			complexid: arguments[0] ? arguments[0] : 0,
    			compositid: arguments[1],
    			type: arguments[2],
    			nameattribute: arguments[3],
    			applyattribute: arguments[4],
    			locate: arguments[5]
        	};
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget,panel,table,row,panelhead,addAtr,complex,simple,composite'},
            attributeList: {value: null},
            sectionid: {value: 0},
            flagAddComposite: {value: false}
        },
        CLICKS: {
        	'addAtr-show':{
        		event: function(e){
        			var type = e.target.getData('type'),
        				sectionid = this.get('sectionid');
        			
        			if(sectionid){
        				this.showFormAddAtr(true, type);
        			} else {
        				alert('Укажите раздел');
        			}
        		}
        	},
        	'addAtr-cancel':{
        		event: function(e){
        			this.showFormAddAtr(false, '');
        		}
        	},
        	actCompositAtr: {
        		event: function(e){
        			var targ = e.target,
        				complexid = targ.getData('id'),
        				compositid = targ.getData('aid'),
        				act = targ.getData('act'),
        				type = targ.getData('type'),
        				parent = e.target.getDOMNode().parentNode.parentNode;
        			
                	switch(act){
	    	    		case 'addShow':
	    	    			this.showAtributeRow(complexid, compositid, type, "");
	    	    				break;
	    	    		case 'editShow': 
	    	    			this.showAtributeRow(complexid, compositid, type, parent);
	    	    				break;
	    	    		case 'edit': 
	    	    			this.renderEditAtributeRow(complexid, compositid, type, parent, true);
	    	    				break;
	    	    		case 'cancel': 
	    	    			this.cancelAtributeRow(complexid, compositid, type, parent);
    	    					break;
	    	    	}
        		}
        	},
        	appendAtr: {
        		event: function(e){
        			var targ = e.target,
        				type = targ.getData('type'),
        				complexid = targ.getData('id');
        			
        				this.appendAttribute(type, complexid);
        		}
        	}
        }
    });
};