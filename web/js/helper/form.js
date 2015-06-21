/*global extendSingleton, getSingleton, isDefined */
var FormHelper;
(function(){
    "use strict";
    /**
    * @name FormHelper
    * @description To make ajax call
    * @property {String} [basePath] Base path used for ajax call
    * @constructor
    */
    FormHelper = function(){
        extendSingleton(FormHelper);
        this.isValid = true;
        this.errors  = {
            required: "* Champ requis",
            invalid: "* Format invalide",
            phone: {
                format: /\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/
            },
            postalCode: {
                format: /^(([0-8][0-9])|(9[0-5]))[0-9]{3}$/
            }
        };
    };

    /**
     * @member FormHelper#getInstance
     * @description get the single class instance
     * @return {FormHelper} the single class instance
     */
    FormHelper.getInstance = function(){
        return getSingleton(FormHelper);
    };

    FormHelper.prototype.check = function(container, elements){
        var that = this;
        this.isValid = true;
        $(container).find(".error").remove();
        $(elements).each(check);

        function check(i, element){
            if(!isDefined($(element).attr("disabled"))){
                switch(element.localName){
                    case "select":
                        if($(element).find(":selected").val() === ""){
                            that.addError($(element).parent(), that.errors.required);
                            that.isValid = false;
                        }
                        break;
                    case "textarea":
                    case "input":
                        var value = $(element).val();
                        if(value === ""){
                            if(!isDefined($(element).attr("optional"))){
                                that.addError($(element).parent(), that.errors.required);
                                that.isValid = false;
                            }
                        } else {
                            checkFormat();
                        }
                        break;
                }
            }

            function checkFormat(){
                var type = $(element).attr("rel");
                if(!isDefined(type) || !isDefined(that.errors[type]) || !isDefined(that.errors[type].format)){
                    return false;
                }
                var text = isDefined(that.errors[type].text) ? that.errors[type].text : that.errors.invalid;
                if(!that.errors[type].format.test(value)){
                    that.addError($(element).parent(), text);
                    that.isValid = false;
                }
            }
        }
    };

    FormHelper.prototype.addError = function(element, value){
        $(element).append("<div class='error'>"+value+"</div>");
    };

})();