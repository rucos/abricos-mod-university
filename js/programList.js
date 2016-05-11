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

    NS.ProgramListWidget = Y.Base.create('programListWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance){
        	this.reloadList();
        },
        reloadList: function(){
        	this.set('waiting', true);
	        	this.get('appInstance').programList(function(err, result){
	        		this.set('waiting', false);
	        		if(!err){
	        			this.set('programList', result.programList);
	        				this.renderList();
	        		}
	        	}, this);
        },
        renderList: function(){
        	var tp = this.template,
        		programList = this.get('programList'),
        		lst = "",
        		n = 0;
        	
        	programList.each(function(prog){
        		var remove = prog.get('remove'),
        			objReplace = {
        				n: ++n,
        				danger: '',
        				act: 'Удалить'
        			};
        		
        		if(remove){
        			objReplace.danger = "class='danger'";
        			objReplace.act = 'Восстановить';
        		}
        		
        		lst += tp.replace('row', [objReplace, prog.toJSON()]);
        	});
        	
        	tp.setHTML('list', tp.replace('table', {
        		rows: lst
        	}))
        },
        removeShow: function(id, show){
        	this.template.toggleView(show, "row.removegroup-" + id, "row.remove-" + id);
        },
        remove: function(id, remove){
        	var data = {
        			programid: id,
        			remove: remove
        		};
        	
        	this.set('waiting', true);
	        	this.get('appInstance').removeProgram(data, function(err, result){
	        		this.set('waiting', false);
		        		if(!err){
		        			this.reloadList();
		        		}
	        	}, this);
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget,table,row'},
            programList: {value: null}
        },
        CLICKS: {
        	'remove-show': {
        		event: function(e){
        			var targ = e.target, 
        				id = targ.getData('id'),
        				act = +targ.getData('act');
        			
        			if(act){
        				this.remove(id, false);
        			} else {
        				this.removeShow(id, true);
        			}
        		}
        	},
        	'remove-cancel': {
        		event: function(e){
        			var id = e.target.getData('id');
        			
        			this.removeShow(id, false);
        		}
        	},
        	remove: {
        		event: function(e){
        			var id = e.target.getData('id');
        			
        			this.remove(id, true);
        		}
        	}
        }
    });
};