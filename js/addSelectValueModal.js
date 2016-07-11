var Component = new Brick.Component();
Component.requires = {
    mod: [
        {name: 'sys', files: ['editor.js']},
        {name: '{C#MODNAME}', files: ['lib.js', 'employeesList.js']}
    ]
};
Component.entryPoint = function(NS){

	var Y = Brick.YUI,
        COMPONENT = this,
        SYS = Brick.mod.sys;
    
 
    NS.AddSelectValueModalWidget = Y.Base.create('addSelectValueModalWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance){
        	
        },
        showModal: function(valueid, attrid, numrow, relationid){
        	var tp = this.template;
        	
          	this.set('attrid', attrid);
        	this.set('valueid', valueid);
        	this.set('relationid', relationid);
        	this.set('numrow', numrow);
        	
	        	tp.setHTML('modal', tp.replace('modalForm', {
	        		none: 'block'
	        	}));
        	
        	this.employees = new NS.EmployeesListWidget({
                srcNode: tp.gel('modalForm.form'),
                select: true,
                relationid: relationid
    		});

        },
        destroy: function(){
        	if(this.employees){
        		this.employees.destroy();
        	}
        },
        unSetSuccess: function(tr){
        	var collect = tr.parentNode.childNodes;
        	
        	collect.forEach(function(item){
        		item.classList.remove('success');
        	});
        },
        selectValueAct: function(callback){
        	var data = {
        		valueid: this.get('valueid'),
        		attrid: this.get('attrid'),
        		relationid: this.get('relationid'),
        		numrow: this.get('numrow')
        	};
        	
        	this.set('waiting', true);
	        	this.get('appInstance').selectValueAct(data, function(err, result){
	        		this.set('waiting', false);
		        		if(!err){
		        			this.template.setHTML('modal', '');
		        				callback(result.selectValueAct);
		        		}
	        	}, this);
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget, modalForm'},
            valueid: {value: 0},
            attrid: {value: 0},
            relationid: {value: 0},
            numrow: {value: 0}
        },
        CLICKS: {
        	'addValue-cancel': {
        		event: function(){
        			this.template.setHTML('modal', '');
        		}
        	},
        	pickRow: {
        		event: function(e){
        			var targ = e.target,
        				tr = e.target.getDOMNode().parentNode;

        			if(tr.tagName != 'TR' || tr.id == ''){
        				return;
        			}
        			
        			this.unSetSuccess(tr);
        			tr.classList.add('success');
        			
        			this.set('relationid', tr.id.match(/-(\d)+/)[1]);
        		}
        	}
        }
    });
};