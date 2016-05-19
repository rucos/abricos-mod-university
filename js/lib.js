var Component = new Brick.Component();
Component.requires = {
    mod: [
        {name: 'sys', files: ['application.js']},
        {name: '{C#MODNAME}', files: ['model.js']}
    ]
};
Component.entryPoint = function(NS){

	NS.roles = new Brick.AppRoles('{C#MODNAME}', {
        isAdmin: 50
    });

    var COMPONENT = this,
        SYS = Brick.mod.sys;

    SYS.Application.build(COMPONENT, {}, {
        initializer: function(){
            NS.roles.load(function(){
                this.initCallbackFire();
            }, this);
        },
        dataAttributeList: function(sectionid, isValue, complexid){
    		return {
    			sectionid: sectionid,
    			isValue: isValue,
    			complexid: complexid || 0
    		};
    	},
        setDate: function(date){
        	return date.split('.').reverse().join('-');
        },
        setCancelDate: function(date){
        	return date.split('-').reverse().join('.');
        }
    }, [], {
        REQS: {
        	sectionList: {
    			attribute: false,
    			type: 'modelList:SectionList'
    		},
    		attributeList: {
    			args: ['data'],
    			attribute: false,
    			type: 'modelList:AttributeList'
    		},
    		actAttribute: {
    			args: ['data']
    		},
    		removeAttribute: {
    			args: ['compositid', 'isComplex']
    		},
    		valueAttributeList: {
    			args: ['data'],
    			attribute: false,
    			type: 'modelList:ValueAttributeList'
    		},
    		actValueAttribute: {
    			args: ['data'],
    			attribute: false
    		},
    		removeValueAttribute: {
    			args: ['data']
    		},
    		actProgram: {
    			args: ['data']
    		},
    		programList: {
    			attribute: false,
    			type: 'modelList:ProgramList'
    		},
    		programItem: {
    			args: ['programid'],
    			attribute: false,
    			type: 'model:ProgramItem'
    		},
    		removeProgram: {
    			args: ['data']
    		},
    		programLevelList: {
    			attribute: false,
    			type: 'modelList:ProgramLevelList'
    		},
    		employeesList: {
    			attribute: false,
    			type: 'modelList:EmployeesList'
    		},
    		actEmployees: {
    			args: ['data']
    		}
        },
        ATTRS: {
        	isLoadAppStructure: {value: true},
        	SectionList: {value: NS.SectionList},
        	AttributeList: {value: NS.AttributeList},
        	ValueAttributeList: {value: NS.ValueAttributeList},
        	ProgramList: {value: NS.ProgramList},
        	ProgramItem: {value: NS.ProgramItem},
        	ProgramLevelList: {value: NS.ProgramLevelList},
        	EmployeesList: {value: NS.EmployeesList},
        	currentSection: {value: ''}
        },
        URLS: {
        	ws: "#app={C#MODNAMEURI}/wspace/ws/",
        	managerTags: {
        		view: function(){
        			 return this.getURL('ws') + 'managerTags/ManagerTagsWidget';
        		}
        	},
        	struct: {
        		view: function(){
        			 return this.getURL('ws') + 'managerStruct/ManagerStructWidget';
        		},
        		sectionItem: function(sectionid, sectionName){
        			return this.getURL('ws') + 'sectionEditor/SectionEditorWidget/' + sectionid + '/';
        		}
        	},
        	program: {
        		view: function(){
        			return this.getURL('ws') + 'managerProgram/ManagerProgramWidget';
        		},
        		act: function(programid){
        			return this.getURL('ws') + 'editorProgram/EditorProgramWidget/' + programid + '/';
        		}
        	},
        	employees: {
        		view: function(){
        			return this.getURL('ws') + 'managerEmployees/ManagerEmployeesWidget';
        		}
        	}
        }
    });
};