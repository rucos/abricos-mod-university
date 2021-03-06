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
        		this.reloadPanel(true);
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
        reloadPanel: function(add){
        	var tp = this.template,
        		lst = "",
        		programItem = this.get('programItem'),
        		replaceObj = {
        			code: '',
        			name: '',
        			act: 'Добавить'
        		};
        	
        	if(!add){
        		replaceObj.code = programItem.get('code');
        		replaceObj.name = programItem.get('name');
        		replaceObj.act = "Изменить";
        	}
        	
        	tp.setHTML('panel', tp.replace('panel', replaceObj));
        	
        		this.renderProgramLevelList();
        },
        renderProgramLevelList: function(){
        	var tp = this.template,
        		programLevelList = this.get('programLevelList');
        	
        	if(programLevelList){
        		programLevelList.each(function(level){
	        		var obj = level.toJSON(),
	        			lvl = '',
	        			tp = this.template;
	        		
	        		switch(obj.level){
	        			case 'бакалавриат академический':
	        				lvl = 'panel.akad';
	        					break;
	        			case 'бакалавриат прикладной':
	        				lvl = 'panel.prik';
	        					break;
	        			case 'специалитет':
	        				lvl = 'panel.spec';
	        					break;
	        		}
	        		
	        		for(var i = 0; i <= 2; i++){
	        			if(obj.eduform[i] > 0){
	        				tp.gel(lvl).cells[i + 1].firstChild.checked = true;	  
	        			}
	        		}
	        	}, this);
        	}
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
        		for(var j = 1, value = '', curCh = ''; j < 4; j++){
        			curCh = eduLevel[i].cells[j].firstChild;
        			
        			if(curCh.checked){
        				value += curCh.value;
        			} else {
        				value += 0;
        			}
        		}
        		eduLevel[i] = value;
        	}
    		return eduLevel;
        },
        reqActProgram: function(data){
        	this.set('waiting', true);
	        	this.get('appInstance').actProgram(data, function(err, result){
	        		this.set('waiting', false);
	        			if(result.actProgram !== false){
	        				this.go('program.view');	
	        			} else {
	        				alert( 'Укажите срок обучения' );
	        			}
	        	}, this);
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget,panel,eduform'},
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