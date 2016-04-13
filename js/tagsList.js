var Component = new Brick.Component();
Component.requires = {
    mod: [
        {name: '{C#MODNAME}', files: ['lib.js']}
    ]
};
Component.entryPoint = function(NS){

    var Y = Brick.YUI,
        COMPONENT = this,
        SYS = Brick.mod.sys;
    
 
    NS.TagsListWidget = Y.Base.create('tagsListWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance){
        	
        },
        reloadList: function(id){
        	
        },
        renderList: function(){
        	
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget,table,row'}
        },
        CLICKS: {
        	
        }
    });
};