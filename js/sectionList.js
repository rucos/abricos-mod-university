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
        	this.set('waiting', true);
	        	this.get('appInstance').sectionList(function(err, result){
	        		this.set('waiting', false);
	        			this.set('sectionList', result.sectionList);
	        				this.renderList();
	        	}, this);
        },
        renderList: function(){
        	var sectionList = this.get('sectionList'),
        		tp = this.template,
        		lst = "";
        	
        	sectionList.each(function(section){
        		lst += tp.replace('liSection', [section.toJSON()]);
        	});
        	
        	tp.setHTML('section', tp.replace('ulSection', {li: lst}));
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
            sectionList: {value: null}
        }
    });
};