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
};
