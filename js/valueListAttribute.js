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

    NS.ValueListWidget = Y.Base.create('valueListWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance){
        	
        },
        reloadList: function(){
        	var data = {
        		attrid: this.get('currentAttrid'),
        		type: this.get('currentType')
        	};
        	
        	this.set('waiting', true);
	        	this.get('appInstance').valueAttributeList(data, function(err, result){
	        		this.set('waiting', false);
		        		if(!err){
		        			this.set('valueAttributeList', result.valueAttributeList);
		        			
		        			this.renderList();
		        		}
	        	}, this);
        },
        renderList: function(){
        	var valueList = this.get('valueAttributeList'),
        		tp = this.template,
        		lstVal = "",
        		lstFile = "",
        		tables = "";
        	
        	valueList.each(function(val){
        		if(val.get('value') != ''){
        			lstVal += this.replaceRow(val, 'rowValues');
        		} else {
        			lstFile += this.replaceRow(val, 'rowFiles');
        		}
        	}, this);
        	
        	tables = this.replaceTable(lstVal, 'tableValues');
        	
        	tables += this.replaceTable(lstFile, 'tableFiles');
        	
        	tp.setHTML('values', tables);
        },
        replaceRow: function(val, block){
        	var tp = this.template;
        	
        	return tp.replace(block, [{
				rowEdit: tp.replace('rowEdit', {
					id: val.get('id'),
					mode: block
				})
			}, val.toJSON()]);
        },
        replaceTable: function(rows, block){
        	var tp = this.template,
        		atrid = this.get('currentAttrid');
        	
        	return tp.replace(block, {
        		addButton: tp.replace('addButton', {
        			mode: block 
        		}),
        		rows: rows
        	});
        },
        addShow: function(mode){
        	var tp = this.template,
        		table = tp.gel(mode + '.' + mode),
        		row = table.insertRow(1),
        		block = "",
        		data = this.constructDataValue(0);
        	
        	switch(mode){
        		case 'tableValues':
        			block = 'rowActValues';
        				break;
        		case 'tableFiles':
        			block = 'rowActFiles';
        				break;
        	}
        	
        	row.innerHTML = this.addRowRender(block, 'rowEditAct', 'Добавить', data, 'appendValue');
        },
        editShow: function(id, mode, tr){
        	var tp = this.template,
        		data = this.constructDataValue(id);
        	
        	switch(mode){
	    		case 'rowValues':
	    			block = 'rowActValues';
	    			data.value = tr.cells[0].textContent;
	    				break;
	    		case 'rowFiles':
	    			block = 'rowActFiles';
	    			data.nameurl = tr.cells[0].textContent;
	    			data.namedoc = tr.cells[1].textContent;
	    			data.subject = tr.cells[2].textContent;
	    			data.datedoc = tr.cells[3].textContent;
	    			data.folder = tr.cells[4].textContent;
	    				break;
	    	}
        	
        	tr.innerHTML = this.addRowRender(block, 'rowEditAct', 'Изменить', data, 'editValue');
        },
        cancelEditShow: function(id, tr, mode){
        	var tp = this.template,
        		data = this.constructDataValue(id),
        		blockAct = "";
        	
        	switch(mode){
	    		case 'rowActValues':
	    			blockAct = "rowValues";
	    			data.value = tr.cells[0].firstChild.getAttribute('value');
	    				break;
	    		case 'rowActFiles':
	    			blockAct = "rowFiles";
	    			data.nameurl = tr.cells[0].firstChild.getAttribute('value');
	    			data.namedoc = tr.cells[1].firstChild.getAttribute('value');
	    			data.subject = tr.cells[2].firstChild.getAttribute('value');
	    			data.datedoc = tr.cells[3].firstChild.getAttribute('value');
	    			data.folder = tr.cells[4].firstChild.getAttribute('value');
	    				break;
	    	}
        	
        	tr.innerHTML = this.addRowRender(blockAct, 'rowEdit', 'Править', data);
        },
        addRowRender: function(blockAct, blockEdit, act, data, event){
        	var tp = this.template;
        	
        	return tp.replace(blockAct, {
        		value: data.value,
        		nameurl: data.nameurl,
        		namedoc: data.namedoc,
        		subject: data.subject,
        		datedoc: data.datedoc,
        		folder: data.folder,
        		rowEdit: tp.replace(blockEdit, {
        			id: data.id,
        			act: act,
        			event: event,
        			mode: blockAct
        		})
        	});
        },
        appendValue: function(tr, mode, id){
        	var data = "";
        	
        	switch(mode){
	    		case 'rowActValues':
	    			data = this.constructDataValue(id, tr.cells[0].firstChild.value);
	    				break;
	    		case 'rowActFiles':
	    			data = this.constructDataValue.apply(this, this.renderRowActFiles(id, tr));
	    				break;
	    	}
        	this.reqAppendValue(data);
        },
        renderRowActFiles: function(id, tr){
        	var cells = tr.cells,
        		len = cells.length - 2,
        		arr = [id, ''];
        	
        	for(var i = 0; i < len; i++){
        		arr.push(cells[i].firstChild.value);
        	}
        	return arr;
        },
        reqAppendValue: function(data){
        	this.set('waiting', true);
	        	this.get('appInstance').actValueAttribute(data, function(err, result){
	        		this.set('waiting', false);
	        			if(!err){
	        				this.reloadList();
	        			}
	        	}, this);
        },
        constructDataValue: function(){
        	return {
        		id: arguments[0],
        		value: arguments[1] || '',
        		nameurl: arguments[2] || '',
        		namedoc: arguments[3] || '',
        		subject: arguments[4] || '',
        		datedoc: arguments[5] || '',
        		folder: arguments[6] || '',
        		atrid: this.get('currentAttrid')
        	};
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget,tableValues,rowValues,tableFiles,rowFiles,rowEdit,addButton,rowActValues,rowActFiles,rowEditAct'},
            valueAttributeList: {value: null},
            currentAttrid: {value: null},
            currentType: {value: null}
        },
        CLICKS: {
        	'addValue-show': {
        		event: function(e){
        			var targ = e.target,
        				mode = targ.getData('mode');
        			
        			this.addShow(mode);
        		}
        	},
        	'act-cancel': {
        		event: function(e){
        			var targ = e.target,
        				tr = targ.getDOMNode().parentNode.parentNode,
        				mode = targ.getData('mode');
        				id = targ.getData('id');
        			
        			if(id > 0){
        				this.cancelEditShow(id, tr, mode);
        			} else {
        				tr.remove();
        			}
        		}
        	},
        	'editValue-show': {
        		event: function(e){
        			var targ = e.target,
        				id = targ.getData('id'),
        				mode = targ.getData('mode'),
        				tr = targ.getDOMNode().parentNode.parentNode;
        			
        			this.editShow(id, mode, tr);
        		}
        	},
        	appendValue: {
        		event: function(e){
        			var targ = e.target,
        				mode = targ.getData('mode'), 
        				tr = targ.getDOMNode().parentNode.parentNode;
        			
        			this.appendValue(tr, mode, 0);
        		}
        	},
        	editValue: {
        		event: function(e){
        			var targ = e.target,
        				id = targ.getData('id'),
        				mode = targ.getData('mode'), 
        				tr = targ.getDOMNode().parentNode.parentNode;
        			
        			this.appendValue(tr, mode, id);
        		}
        	}
        }
    });
};