var Component = new Brick.Component();
Component.requires = {
    mod: [
        {name: '{C#MODNAME}', files: ['tagsList.js', 'lib.js']}
    ]
};
Component.entryPoint = function(NS){

    var Y = Brick.YUI,
        COMPONENT = this,
        SYS = Brick.mod.sys;
   
    
    NS.ManagerTagsWidget = Y.Base.create('managerTagsWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance, options){
            var tp = this.template;
            
            this.tagsListWidget = new NS.TagsListWidget({
                srcNode: tp.gel('list')
            });
         
        },
        destructor: function(){
            if (this.tagsListWidget){
                this.tagsListWidget.destroy();
            }
        }
    }, {
        ATTRS: {
            component: {value: COMPONENT},
            templateBlockName: {value: 'widget'}
        }
    });
};