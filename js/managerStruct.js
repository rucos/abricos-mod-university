var Component = new Brick.Component();
Component.requires = {
    mod: [
        {name: '{C#MODNAME}', files: ['sectionList.js', 'lib.js']}
    ]
};
Component.entryPoint = function(NS){

    var Y = Brick.YUI,
        COMPONENT = this,
        SYS = Brick.mod.sys;
   
    
    NS.ManagerStructWidget = Y.Base.create('managerStructWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance, options){
            var tp = this.template;
            
            this.sectionListWidget = new NS.SectionListWidget({
                srcNode: tp.gel('sectionList'),
                stacked: true
            });
        },
        destructor: function(){
            if (this.sectionListWidget){
                this.sectionListWidget.destroy();
            }
        }
    }, {
        ATTRS: {
            component: {value: COMPONENT},
            templateBlockName: {value: 'widget'}
        },
        CLICKS: {
        	pickSection: {
        		event: function(e){
        			var targ = e.target,
        				sectionid = targ.getData('id'),
        				a = targ.getDOMNode(),
        				sectionName = a.textContent;
        			
        			if(!a.href){
        				return;
        			}
        			
        			this.get('appInstance').set('currentSection', sectionName);
        			
        			this.go('struct.sectionItem', sectionid);
        		}
        	}
        }
    });
};