/*global HomeManager */

var homeManager;
(function(){
    "use strict";
    /** on document ready */
    $(document).ready(init);

    /**
     * @name main#initHome
     * @event
     * @description initialize home
     */
    function init(){
        new JsHelper([LoginHelper, window["AdminHelper"]]);
        homeManager = new HomeManager(); 
    }
    
})();