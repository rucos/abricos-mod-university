var Component = new Brick.Component();
Component.requires = {
    mod: [
        {name: 'sys', files: ['appModel.js']}
    ]
};
Component.entryPoint = function(NS){

	var Y = Brick.YUI,
        SYS = Brick.mod.sys;
    
    
    NS.SectionItem = Y.Base.create('sectionItem', SYS.AppModel, [], {
        structureName: 'SectionItem'
    });

    NS.SectionList = Y.Base.create('sectionList', SYS.AppModelList, [], {
        appItem: NS.SectionItem
    });
    
    NS.AttributeItem = Y.Base.create('attributeItem', SYS.AppModel, [], {
        structureName: 'AttributeItem'
    });
    
    NS.AttributeList = Y.Base.create('attributeList', SYS.AppModelList, [], {
        appItem: NS.AttributeItem
    });
    
    NS.ValueAttributeItem = Y.Base.create('valueAttributeItem', SYS.AppModel, [], {
        structureName: 'ValueItem'
    });
    
    NS.ValueAttributeList = Y.Base.create('valueAttributeList', SYS.AppModelList, [], {
        appItem: NS.ValueAttributeItem
    });
    
    NS.ProgramItem = Y.Base.create('programItem', SYS.AppModel, [], {
        structureName: 'ProgramItem'
    });
    
    NS.ProgramList = Y.Base.create('programList', SYS.AppModelList, [], {
        appItem: NS.ProgramItem
    });
    
    NS.ProgramLevelItem = Y.Base.create('programLevelItem', SYS.AppModel, [], {
        structureName: 'ProgramLevelItem'
    });
    
    NS.ProgramLevelList = Y.Base.create('programLevelList', SYS.AppModelList, [], {
        appItem: NS.ProgramLevelItem
    });
};
