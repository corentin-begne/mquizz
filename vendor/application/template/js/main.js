/*global TemplateManager, LoginHelper */

var templateManager;
(function(){
    "use strict";
    /** on document ready */
    $(document).ready(init);

    /**
     * @name main#initTemplate
     * @event
     * @description initialize template
     */
    function init(){
        new JsHelper();
        templateManager = new TemplateManager();
    }
    
})();