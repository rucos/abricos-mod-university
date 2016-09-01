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
    
 
    NS.SectionListWidget = Y.Base.create('sectionListWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance){
        	this.reloadList();
        },
        reloadList: function(){
        	var lib = this.get('appInstance'),
        		tp = this.template,
        		div = tp.gel('loading');
        	
        	lib.loadingLineShow(div, true);
        		lib.sectionList(function(err, result){
        			lib.loadingLineShow(div, false);
	        			this.set('sectionList', result.sectionList);
	        				this.renderList();
	        	}, this);
        },
        renderList: function(){
        	var sectionList = this.get('sectionList'),
        		stacked = this.get('stacked'),
        		tp = this.template,
        		lst = "";
        	
        	sectionList.each(function(section){
        		lst += tp.replace('liSection', [section.toJSON()]);
        	});
        	
        	tp.setHTML('section', tp.replace('ulSection', {
        		stacked: stacked ? 'nav-stacked' : '',
        		li: lst
        	}));
        },
        setPrimarySection: function(id){
        	var tp = this.template,
        		sections = tp.gel('ulSection.sections').children,
        		len = sections.length;
        			
        	for(var i = 0; i < len; i++){
        		if(sections[i].id === id){
        			sections[i].classList.add('active');
        		} else {
        			sections[i].classList.remove('active');
        		}
        	}
        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget,ulSection,liSection'},
            sectionList: {value: null},
            stacked: {value: false}
        }
    });
};