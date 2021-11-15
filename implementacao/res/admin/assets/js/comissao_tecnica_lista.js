/*
Template Name: Color Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.6
Version: 2.0.0
Author: Sean Ngu
Website: http://www.seantheme.com/color-admin-v2.0/admin/html/
*/
function calculateDivider() {
    var e = 4;
    if ($(this).width() <= 480) {
        e = 1
    } else if ($(this).width() <= 767) {
        e = 2
    } else if ($(this).width() <= 980) {
        e = 3
    }
    return e
}
var handleDatepicker = function() {
    $("#datepicker-default").datepicker({
        todayHighlight: !0,
        format: "dd/mm/yyyy",
        language: "pt-BR"        
    })
	},
	handleIsotopesGallery = function() {
	    "use strict";
	    $(window).load(function() {
	        var e = $("#gallery");
	        var t = calculateDivider();
	        var n = $(e).width() - 20;
	        var r = n / t;
	        $(e).isotope({
	            resizable: false,
	            masonry: {
	                columnWidth: r
	            }
	        });
	        $(window).smartresize(function() {
	            var t = calculateDivider();
	            var n = $(e).width() - 20;
	            var r = n / t;
	            $(e).isotope({
	                masonry: {
	                    columnWidth: r
	                }
	            })
	        });
	        var i = $("#options .gallery-option-set"),
	            s = i.find("a");
	        s.click(function() {
	            var t = $(this);
	            if (t.hasClass("active")) {
	                return false
	            }
	            var n = t.parents(".gallery-option-set");
	            n.find(".active").removeClass("active");
	            t.addClass("active");
	            var r = {};
	            var i = n.attr("data-option-key");
	            var s = t.attr("data-option-value");
	            s = s === "false" ? false : s;
	            r[i] = s;
	            $(e).isotope(r);
	            return false
	        })
	    })
	};
var Gallery = function() {
    "use strict";
    return {
        init: function() {
            handleIsotopesGallery(),
            handleDatepicker()
        }
    }
}()

/*
Template Name: Color Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.6
Version: 2.0.0
Author: Sean Ngu
Website: http://www.seantheme.com/color-admin-v2.0/admin/html/

var handleDatepicker = function() {
        $("#datepicker-default").datepicker({
            todayHighlight: !0
        }), $("#datepicker-inline").datepicker({
            todayHighlight: !0
        }), $(".input-daterange").datepicker({
            todayHighlight: !0
        }), $("#datepicker-disabled-past").datepicker({
            todayHighlight: !0
        }), $("#datepicker-autoClose").datepicker({
            todayHighlight: !0,
            autoclose: !0
        })
    },
    handleIonRangeSlider = function() {
        $("#default_rangeSlider").ionRangeSlider({
            min: 0,
            max: 5e3,
            type: "double",
            prefix: "$",
            maxPostfix: "+",
            prettify: !1,
            hasGrid: !0
        }), $("#customRange_rangeSlider").ionRangeSlider({
            min: 1e3,
            max: 1e5,
            from: 3e4,
            to: 9e4,
            type: "double",
            step: 500,
            postfix: " €",
            hasGrid: !0
        }), $("#customValue_rangeSlider").ionRangeSlider({
            values: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
            type: "single",
            hasGrid: !0
        })
    },
    handleFormMaskedInput = function() {
        "use strict";
        $("#masked-input-date").mask("99/99/9999"), $("#masked-input-phone").mask("(999) 999-9999"), $("#masked-input-tid").mask("99-9999999"), $("#masked-input-ssn").mask("999-99-9999"), $("#masked-input-pno").mask("aaa-9999-a"), $("#masked-input-pkey").mask("a*-999-a999")
    },
    handleFormColorPicker = function() {
        "use strict";
        $("#colorpicker").colorpicker({
            format: "hex"
        }), $("#colorpicker-prepend").colorpicker({
            format: "hex"
        }), $("#colorpicker-rgba").colorpicker()
    },
    handleFormTimePicker = function() {
        "use strict";
        $("#timepicker").timepicker()
    },
    handleFormPasswordIndicator = function() {
        "use strict";
        $("#password-indicator-default").passwordStrength(), $("#password-indicator-visible").passwordStrength({
            targetDiv: "#passwordStrengthDiv2"
        })
    },
    handleJqueryAutocomplete = function() {
        var e = ["ActionScript", "AppleScript", "Asp", "BASIC", "C", "C++", "Clojure", "COBOL", "ColdFusion", "Erlang", "Fortran", "Groovy", "Haskell", "Java", "JavaScript", "Lisp", "Perl", "PHP", "Python", "Ruby", "Scala", "Scheme"];
        $("#jquery-autocomplete").autocomplete({
            source: e
        })
    },
    handleBootstrapCombobox = function() {
        $(".combobox").combobox()
    },
    handleTagsInput = function() {
        $(".bootstrap-tagsinput input").focus(function() {
            $(this).closest(".bootstrap-tagsinput").addClass("bootstrap-tagsinput-focus")
        }), $(".bootstrap-tagsinput input").focusout(function() {
            $(this).closest(".bootstrap-tagsinput").removeClass("bootstrap-tagsinput-focus")
        })
    },
    handleSelectpicker = function() {
        $(".selectpicker").selectpicker("render")
    },
    handleJqueryTagIt = function() {
        $("#jquery-tagIt-default").tagit({
            availableTags: ["c++", "java", "php", "javascript", "ruby", "python", "c"]
        }), $("#jquery-tagIt-inverse").tagit({
            availableTags: ["c++", "java", "php", "javascript", "ruby", "python", "c"]
        }), $("#jquery-tagIt-white").tagit({
            availableTags: ["c++", "java", "php", "javascript", "ruby", "python", "c"]
        }), $("#jquery-tagIt-primary").tagit({
            availableTags: ["c++", "java", "php", "javascript", "ruby", "python", "c"]
        }), $("#jquery-tagIt-info").tagit({
            availableTags: ["c++", "java", "php", "javascript", "ruby", "python", "c"]
        }), $("#jquery-tagIt-success").tagit({
            availableTags: ["c++", "java", "php", "javascript", "ruby", "python", "c"]
        }), $("#jquery-tagIt-warning").tagit({
            availableTags: ["c++", "java", "php", "javascript", "ruby", "python", "c"]
        }), $("#jquery-tagIt-danger").tagit({
            availableTags: ["c++", "java", "php", "javascript", "ruby", "python", "c"]
        })
    },
    handleDateRangePicker = function() {
        $("#default-daterange").daterangepicker({
            opens: "right",
            format: "MM/DD/YYYY",
            separator: " to ",
            startDate: moment().subtract("days", 29),
            endDate: moment(),
            minDate: "01/01/2012",
            maxDate: "12/31/2018"
        }, function(e, t) {
            $("#default-daterange input").val(e.format("MMMM D, YYYY") + " - " + t.format("MMMM D, YYYY"))
        }), $("#advance-daterange span").html(moment().subtract("days", 29).format("MMMM D, YYYY") + " - " + moment().format("MMMM D, YYYY")), $("#advance-daterange").daterangepicker({
            format: "MM/DD/YYYY",
            startDate: moment().subtract(29, "days"),
            endDate: moment(),
            minDate: "01/01/2012",
            maxDate: "12/31/2015",
            dateLimit: {
                days: 60
            },
            showDropdowns: !0,
            showWeekNumbers: !0,
            timePicker: !1,
            timePickerIncrement: 1,
            timePicker12Hour: !0,
            ranges: {
                Today: [moment(), moment()],
                Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
                "Last 7 Days": [moment().subtract(6, "days"), moment()],
                "Last 30 Days": [moment().subtract(29, "days"), moment()],
                "This Month": [moment().startOf("month"), moment().endOf("month")],
                "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
            },
            opens: "right",
            drops: "down",
            buttonClasses: ["btn", "btn-sm"],
            applyClass: "btn-primary",
            cancelClass: "btn-default",
            separator: " to ",
            locale: {
                applyLabel: "Submit",
                cancelLabel: "Cancel",
                fromLabel: "From",
                toLabel: "To",
                customRangeLabel: "Custom",
                daysOfWeek: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
                monthNames: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                firstDay: 1
            }
        }, function(e, t) {
            $("#advance-daterange span").html(e.format("MMMM D, YYYY") + " - " + t.format("MMMM D, YYYY"))
        })
    },
    handleSelect2 = function() {
        $(".default-select2").select2(), $(".multiple-select2").select2({
            placeholder: "Select a state"
        })
    },
    handleDateTimePicker = function() {
        $("#datetimepicker1").datetimepicker(), $("#datetimepicker2").datetimepicker({
            format: "LT"
        }), $("#datetimepicker3").datetimepicker(), $("#datetimepicker4").datetimepicker(), $("#datetimepicker3").on("dp.change", function(e) {
            $("#datetimepicker4").data("DateTimePicker").minDate(e.date)
        }), $("#datetimepicker4").on("dp.change", function(e) {
            $("#datetimepicker3").data("DateTimePicker").maxDate(e.date)
        })
    },
    FormPlugins = function() {
        "use strict";
        return {
            init: function() {
                handleDatepicker(), 
                handleIonRangeSlider(), 
                handleFormMaskedInput(), 
                handleFormColorPicker(), 
                handleFormTimePicker(), 
                handleFormPasswordIndicator(), 
                handleJqueryAutocomplete(), 
                handleBootstrapCombobox(), 
                handleSelectpicker(), 
                handleTagsInput(), 
                handleJqueryTagIt(), 
                handleDateRangePicker(), 
                handleSelect2(), 
                handleDateTimePicker()
            }
        }
    }();
*/