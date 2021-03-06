/*
Template Name: Color Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.6
Version: 2.0.0
Author: Sean Ngu
Website: http://www.seantheme.com/color-admin-v2.0/admin/html/
*/
var handleEmailActionButtonStatus = function() {
    if ($("[data-checked=email-checkbox]:checked").length !== 0) {
        $("[data-email-action]").removeClass("hide")
    } else {
        $("[data-email-action]").addClass("hide")
    }
};
var handleEmailCheckboxChecked = function() {
    $("[data-checked=email-checkbox]").live("click", function() {
        var e = $(this).closest("label");
        var t = $(this).closest("li");
        if ($(this).prop("checked")) {
            $(e).addClass("active");
            $(t).addClass("selected")
        } else {
            $(e).removeClass("active");
            $(t).removeClass("selected")
        }
        handleEmailActionButtonStatus()
    })
};

var InboxV2 = function() {
    "use strict";
    return {
        init: function() {
            handleEmailCheckboxChecked();
        }
    }
}()