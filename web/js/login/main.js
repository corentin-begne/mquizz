/*global LoginManager, LoginHelper */

var loginManager;
(function(){
    "use strict";
    /** on document ready */
    $(document).ready(init);

    /**
     * @name main#initLogin
     * @event
     * @description initialize login
     */
    function init(){
        new JsHelper();
        loginManager = new LoginManager();
    }
    
})();