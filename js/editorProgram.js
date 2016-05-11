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

    NS.EditorProgramWidget = Y.Base.create('editorProgramWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance){
        	var programid = this.get('programid');

        	if(programid > 0){
        		this.editShow(programid);
        	} else {
        		this.reloadPanel();
        	}
        },
        editShow: function(programid){
        	this.set('waiting', true);
	        	this.get('appInstance').programItem(programid, function(err, result){
	        		this.set('waiting', false);
	        			if(!err){
	        				this.set('programItem', result.programItem);
	        				this.set('programLevelList', result.programLevelList);
	        					this.reloadPanel();
	        			}
	        	}, this);
        },
        reloadPanel: function(){
        	var tp = this.template,
        		lst = "",
        		programItem = this.get('programItem'),
        		replaceObj = {
        			code: '',
        			name: '',
        			act: 'Добавить'
        		};
        	
        	if(programItem){
        		replaceObj.code = programItem.get('code');
        		replaceObj.name = programItem.get('name');
        		replaceObj.act = "Изменить";
        	}
        	
        	tp.setHTML('panel', tp.replace('panel', replaceObj));
        },
        renderPanel: function(){
        	var tp = this.template,
        		data = {
        			programid: this.get('programid'),
        			code: tp.gel('panel.code').value,
        			name: tp.gel('panel.name').value,
        			eduLevel: this.renderEduLevel()
        		};
        	
        	this.reqActProgram(data);
        },
        renderEduLevel: function(){
        	var tp = this.template,
        		eduLevel = [
        			tp.gel('panel.akad'),
        			tp.gel('panel.prik'),
        			tp.gel('panel.spec')
        		];
        	
        	for(var i = 0; i < 3; i++){
        		for(var j = 1, value = ''; j < 4; j++){
        			value += eduLevel[i].cells[j].firstChild.value + ',';
        		}
        		
        		if(value.length > 3){
        			eduLevel[i] = value.slice(0, -1);        			
        		} else {
        			eduLevel[i] = "";
        		}
        	}
    		return eduLevel;
        },
        reqActProgram: function(data){
        	this.set('waiting', true);
	        	this.get('appInstance').actProgram(data, function(err, result){
	        		this.set('waiting', false);
	        			this.go('program.view');
	        	}, this);
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget,panel'},
            programid: {value: 0},
            programItem: {value: null},
            programLevelList: {value: null}
        },
        CLICKS: {
        	close: {
        		event: function(){
        			this.go('program.view');
        		}
        	},
        	append: {
        		event: function(){
        			this.renderPanel();
        		}
        	}
        }
    });

    NS.EditorProgramWidget.parseURLParam = function(args){
        return {
        	programid: args[0]
        };
    };
};