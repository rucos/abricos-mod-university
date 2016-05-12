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
        			act: 'Добавить',
        			edulevel: this.replaceEduLevel()
        		};
        	
        	if(programItem){
        		replaceObj.code = programItem.get('code');
        		replaceObj.name = programItem.get('name');
        		replaceObj.act = "Изменить";
        	}
        	
        	tp.setHTML('panel', tp.replace('panel', replaceObj));
        },
        replaceEduLevel: function(){
        	var tp = this.template,
        		programLevelList = this.get('programLevelList'),
        		lst = "";
        	
        	if(programLevelList){
            	programLevelList.each(function(level){
            		
            	});
        	} else {
        		lst += tp.replace('edulevel', {
        			akad: this.replaceEduForm('Бакалавриат академический'),
        			prik: this.replaceEduForm('Бакалавриат прикладной'),
        			spec: this.replaceEduForm('Специалитет')
        		});
        	}
        	return lst;
        },
        replaceEduForm: function(name){
        	return this.template.replace('eduform', {
        		name: name
        	});
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
        			tp.gel('edulevel.akad'),
        			tp.gel('edulevel.prik'),
        			tp.gel('edulevel.spec')
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
        	console.log(data);
        	this.set('waiting', true);
	        	this.get('appInstance').actProgram(data, function(err, result){
	        		this.set('waiting', false);
	        			this.go('program.view');
	        	}, this);
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget,panel,edulevel,eduform'},
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