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
        dataAttributeList: function(sectionid, isValue){
    		return {
    			sectionid: sectionid,
    			isValue: isValue
    		};
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
    		}
        },
        ATTRS: {
        	isLoadAppStructure: {value: true},
        	SectionList: {value: NS.SectionList},
        	AttributeList: {value: NS.AttributeList},
        	ValueAttributeList: {value: NS.ValueAttributeList},
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
        	}
        }
    });
};