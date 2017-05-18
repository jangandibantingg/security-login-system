
/** ASENGINE NAMESPACE
 ======================================== */
var asengine = {};

/**
 * Put button to loadin state.
 * @param {Object} button Button to be putted.
 * @param {string} loadingText Text that will be displayed while loading.
 */
asengine.loadingButton = function(button, loadingText) {
    oldText = button.text();
    button.attr("rel",oldText)
          .text(loadingText)
          .addClass("disabled")
          .attr('disabled', "disabled");
};


/**
 * Returns button from loadin state to normal state.
 * @param {Object} button Button object.
 */
asengine.removeLoadingButton = function (button) {
    var oldText = button.attr('rel');
    button.text(oldText)
                     .removeClass("disabled")
                     .removeAttr("disabled")
                     .removeAttr("rel");
};


/**
 * Append success message to provided parent element.
 * @param {Object} parentElement Parent element where message will be appended.
 * @param {String} message Message to be displayed.
 */
asengine.displaySuccessMessage = function (parentElement, message) {
    $(".alert-success").remove();
    var div = ("<div class='alert alert-success'>"+message+"</div>");
    parentElement.append(div);
};


/**
 * Append error message to an input element. If message is omitted, it will be set to empty string.
 * @param {Object} element Input element on which error message will be appended.
 * @param {String} message Message to be displayed.
 */
asengine.displayErrorMessage = function(element, message) {
    var controlGroup = element.parents(".control-group");
    controlGroup.addClass("error").addClass("has-error");
    if(typeof message !== "undefined") {
        var helpBlock = $("<span class='help-inline text-error'>"+message+"</span>");
        controlGroup.find(".controls").append(helpBlock);
    }
};


/**
 * Removes all error messages from all input fields.
 */
asengine.removeErrorMessages = function () {
    $(".control-group").removeClass("error").removeClass("has-error");
    $(".help-inline").remove();
};


/**
 * Validate email format.
 * @param {String} email Email to be validated.
 * @returns {boolean} TRUE if email is valid, FALSE otherwise.
 */
asengine.validateEmail = function (email) {
  var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
};



/**
 * Get an parameter from URL.
 * @param {string} name Parameter name.
 * @returns {string} Value of parameter with given name.
 */
asengine.urlParam = function(name){
    return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null;
};




