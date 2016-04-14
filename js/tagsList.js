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
	        				simpleRows += this.renderRow(atrr, 'row');
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
					type: 'Простые атрибуты',
					body: this.renderTable(rows)
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
						rows += this.renderRow(arrComposit[i], 'row'); 
					}
			}
 			
 			return rows;
        },
        parsingComplex: function(complex, rows){
        	var tp = this.template,
        		panelHead = "",
        		panel = "";
        		
				panelHead = this.renderRow(complex, 'panelhead');
				
				panel = tp.replace('panel', {
					type: 'Сложный атрибут:' + panelHead,
					body: this.renderTable(rows)
				});
			
			return panel;
        },
        renderRow: function(attr, block){
        	var tp = this.template,
        		row = "";
        	
	          	row = tp.replace(block, [{
	    			remove: attr.get('remove') ? 'Восстановить' : 'Удалить'
	    		}, attr.toJSON()]);
          	
          	return row;
        },
        renderTable: function(rows){
        	return this.template.replace('table', { rows: rows });
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget,panel,table,row,panelhead'},
            attributeList: {value: null},
            sectionid: {value: 0}
        },
        CLICKS: {
        	
        }
    });
};