/*global ActionModel */
var LoginManager;
(function(){
    "use strict";
    /**
    * @class LoginManager
    * @constructor
    * @property {String} [baseName = "login"] base name of the interface associated with
    * @property {ActionModel} action Instance of ActionModel
    * @description  Manage login
    */
    LoginManager = function(){
        this.basePath = "login/";
        this.action = ActionModel.getInstance();
    };

    /** initialize events */
    LoginManager.prototype.init = function(){
    };
})();