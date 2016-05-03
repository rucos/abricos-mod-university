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
        reloadList: function(attrid, type){
        	var data = {
        		attrid: attrid,
        		type: type
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
					id: val.get('id')
				})
			}, val.toJSON()]);
        },
        replaceTable: function(rows, block){
        	var tp = this.template;
        	
        	return tp.replace(block, {
        		addButton: tp.replace('addButton', {
        			mode: block 
        		}),
        		rows: rows
        	});
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget,tableValues,rowValues,tableFiles,rowFiles,rowEdit,addButton'},
            valueAttributeList: {value: null}
        },
        CLICKS: {
        	
        }
    });
};