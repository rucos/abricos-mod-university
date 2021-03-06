var Component = new Brick.Component();
Component.requires = {
    mod: [
        {name: '{C#MODNAME}', files: ['sectionList.js', 'tagsList.js', 'lib.js']}
    ]
};
Component.entryPoint = function(NS){

    var Y = Brick.YUI,
        COMPONENT = this,
        SYS = Brick.mod.sys;
   
    
    NS.ManagerTagsWidget = Y.Base.create('managerTagsWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance, options){
            var tp = this.template;
            
            this.sectionListWidget = new NS.SectionListWidget({
                srcNode: tp.gel('sectionList')
            });
         
            	this.tagsListWidget = new NS.TagsListWidget({
            		srcNode: tp.gel('tagsList')	
            	});
        },
        destructor: function(){
            if (this.sectionListWidget){
                this.sectionListWidget.destroy();
            } else if(this.tagsListWidget){
            	this.tagsListWidget.destroy();
            }
        }
    }, {
        ATTRS: {
            component: {value: COMPONENT},
            templateBlockName: {value: 'widget'}
        },
        CLICKS: {
        	pickSection:{
        		event: function(e){
        			var targ = e.target,
        				a = targ.getDOMNode(),
        				sectionid = targ.getData('id');
        			
        			if(!a.href){
        				return;
        			}
        			
        			this.sectionListWidget.setPrimarySection(sectionid);
        			this.tagsListWidget.set('sectionid', sectionid);
        			this.tagsListWidget.reloadList();
        		}
        	}
        }
    });
};