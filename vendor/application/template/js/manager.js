/*global ActionModel */
var TemplateManager;
(function(){
    "use strict";
    /**
    * @class TemplateManager
    * @constructor
    * @property {String} [baseName = "template"] base name of the interface associated with
    * @property {ActionModel} action Instance of ActionModel
    * @description  Manage template
    */
    TemplateManager = function(){
        this.basePath = "path/";
        this.action = ActionModel.getInstance();
    };

    /** initialize events */
    TemplateManager.prototype.init = function(){
    };
})();