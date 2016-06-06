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
        	sectionItem: {
      			attribute: false,
    			type: 'model:SectionItem'
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
    		valueSimpleList: {
    			args: ['attrid'],
    			attribute: false,
    			type: 'modelList:ValueAttributeList'
    		},
    		valueAttributeItem: {
    			args: ['valueid'],
    			attribute: false,
    			type: 'model:ValueAttributeItem'
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
    		},
    		valueComplexList: {
    			args: ['attrid']
    		}
        },
        ATTRS: {
        	isLoadAppStructure: {value: true},
        	SectionList: {value: NS.SectionList},
        	SectionItem: {value: NS.SectionItem},
        	AttributeList: {value: NS.AttributeList},
        	ValueAttributeList: {value: NS.ValueAttributeList},
        	ValueAttributeItem: {value: NS.ValueAttributeItem},
        	ProgramList: {value: NS.ProgramList},
        	ProgramItem: {value: NS.ProgramItem},
        	ProgramLevelList: {value: NS.ProgramLevelList},
        	EmployeesList: {value: NS.EmployeesList}
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