/*global ActionModel */
var HomeManager;
(function(){
    "use strict";
    /**
    * @class HomeManager
    * @constructor
    * @property {String} [baseName = "home"] base name of the interface associated with
    * @property {ActionModel} action Instance of ActionModel
    * @description  Manage home
    */
    HomeManager = function(){
        this.basePath = "home/";
        this.action = ActionModel.getInstance();
        this.login = LoginHelper.getInstance();
        this.init();
    };

    /** initialize events */
    HomeManager.prototype.init = function(){
    };
})();