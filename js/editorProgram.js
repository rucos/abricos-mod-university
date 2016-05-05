var Component = new Brick.Component();
Component.requires = {
    mod: [
        {name: 'sys', files: ['editor.js']},
        {name: '{C#MODNAME}', files: ['lib.js']}
    ]
};
Component.entryPoint = function(NS){

    var Y = Brick.YUI,
        COMPONENT = this,
        SYS = Brick.mod.sys;

    NS.EditorProgramWidget = Y.Base.create('editorProgramWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance){

        }
    }, {
        ATTRS: {
        	component: {value: COMPONENT},
            templateBlockName: {value: 'widget'},
            programid: {value: 0}
        },
        CLICKS: {
        	close: {
        		event: function(){
        			this.go('program.view');
        		}
        	}
        }
    });

    NS.EditorProgramWidget.parseURLParam = function(args){
        return {
        	programid: args[0]
        };
    };
};