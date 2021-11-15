/*
Template Name: Color Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.6
Version: 2.0.0
Author: Sean Ngu
Website: http://www.seantheme.com/color-admin-v2.0/admin/html/
*/

var white="rgba(255,255,255,1.0)",
fillBlack="rgba(45, 53, 60, 0.6)",
fillBlackLight="rgba(45, 53, 60, 0.2)",
strokeBlack="rgba(45, 53, 60, 0.8)",
highlightFillBlack="rgba(45, 53, 60, 0.8)",
highlightStrokeBlack="rgba(45, 53, 60, 1)",
fillBlue="rgba(52, 143, 226, 0.6)",
fillBlueLight="rgba(52, 143, 226, 0.2)",
strokeBlue="rgba(52, 143, 226, 0.8)",
highlightFillBlue="rgba(52, 143, 226, 0.8)",
highlightStrokeBlue="rgba(52, 143, 226, 1)",
fillGrey="rgba(182, 194, 201, 0.6)",
fillGreyLight="rgba(182, 194, 201, 0.2)",
strokeGrey="rgba(182, 194, 201, 0.8)",
highlightFillGrey="rgba(182, 194, 201, 0.8)",
highlightStrokeGrey="rgba(182, 194, 201, 1)",
fillGreen="rgba(0, 172, 172, 0.6)",
fillGreenLight="rgba(0, 172, 172, 0.2)",
strokeGreen="rgba(0, 172, 172, 0.8)",
highlightFillGreen="rgba(0, 172, 172, 0.8)",
highlightStrokeGreen="rgba(0, 172, 172, 1)",
fillPurple="rgba(114, 124, 182, 0.6)",
fillPurpleLight="rgba(114, 124, 182, 0.2)",
strokePurple="rgba(114, 124, 182, 0.8)",
highlightFillPurple="rgba(114, 124, 182, 0.8)",
highlightStrokePurple="rgba(114, 124, 182, 1)",
randomScalingFactor=function() {
    return Math.round(100*Math.random())
}

,
radarChartData= {
    labels:[
	    "Combustível consumido",
	    "Custo com combustível",
	    "Manutenções realizadas",
	    "Custo com manutenções",
	    "Viagens realizadas",
	    "Distancia percorrida"
    ],
    datasets:[ {
        label: "My First dataset", fillColor: "rgba(45,53,60,0.2)", strokeColor: "rgba(45,53,60,1)", pointColor: "rgba(45,53,60,1)", pointStrokeColor: "#fff", pointHighlightFill: "#fff", pointHighlightStroke: "rgba(45,53,60,1)", data: [65, 59, 90, 81, 90, 76]
    }
    ,
    {
        label: "My Second dataset", fillColor: "rgba(52,143,226,0.2)", strokeColor: "rgba(52,143,226,1)", pointColor: "rgba(52,143,226,1)", pointStrokeColor: "#fff", pointHighlightFill: "#fff", pointHighlightStroke: "rgba(52,143,226,1)", data: [28, 48, 40, 19, 89, 65]
    }
    ]
}
;
Chart.defaults.global= {
    animation:!0,
    animationSteps:60,
    animationEasing:"easeOutQuart",
    showScale:!0,
    scaleOverride:!1,
    scaleSteps:null,
    scaleStepWidth:null,
    scaleStartValue:null,
    scaleLineColor:"rgba(0,0,0,.1)",
    scaleLineWidth:1,
    scaleShowLabels:!0,
    scaleLabel:"<%=value%>",
    scaleIntegersOnly:!0,
    scaleBeginAtZero:!1,
    scaleFontFamily:'"Open Sans", "Helvetica Neue", "Helvetica", "Arial", sans-serif',
    scaleFontSize:12,
    scaleFontStyle:"normal",
    scaleFontColor:"#707478",
    responsive:!0,
    maintainAspectRatio:!0,
    showTooltips:!0,
    customTooltips:!1,
    tooltipEvents:["mousemove",
    "touchstart",
    "touchmove"],
    tooltipFillColor:"rgba(0,0,0,0.8)",
    tooltipFontFamily:'"Open Sans", "Helvetica Neue", "Helvetica", "Arial", sans-serif',
    tooltipFontSize:12,
    tooltipFontStyle:"normal",
    tooltipFontColor:"#ccc",
    tooltipTitleFontFamily:'"Open Sans", "Helvetica Neue", "Helvetica", "Arial", sans-serif',
    tooltipTitleFontSize:12,
    tooltipTitleFontStyle:"bold",
    tooltipTitleFontColor:"#fff",
    tooltipYPadding:10,
    tooltipXPadding:10,
    tooltipCaretSize:8,
    tooltipCornerRadius:3,
    tooltipXOffset:10,
    tooltipTemplate:"<%if (label){%><%=label%>: <%}%><%= value %>",
    multiTooltipTemplate:"<%= value %>",
    onAnimationProgress:function() {}
    ,
    onAnimationComplete:function() {}
}

;
var handleGenerateGraph=function(l) {
    var l=l?l: !1;
    l||$('[data-render="chart-js"]').each(function() {
        var l=$(this).attr("id"), a=$(this).closest("div");
        $(a).empty(), $(a).append('<canvas id="'+l+'"></canvas>')
    }
    );
    
    var a=document.getElementById("radar-chart").getContext("2d"),
    i=(new Chart(a).Radar(radarChartData, {
        animation: l
    }));

    /*
    var a=document.getElementById("line-chart").getContext("2d"),
    t=(new Chart(a).Line(lineChartData, {
        animation: l
    }
    ), document.getElementById("bar-chart").getContext("2d")),
    e=(new Chart(t).Bar(barChartData, {
        animation: l
    }    
    ),
    document.getElementById("radar-chart").getContext("2d")),
    i=(new Chart(e).Radar(radarChartData, {
        animation: l
    }
    ), document.getElementById("polar-area-chart").getContext("2d")),
    o=(new Chart(i).PolarArea(polarData, {
        animation: l
    }
    ), document.getElementById("pie-chart").getContext("2d"));
    window.myPie=new Chart(o).Pie(pieData, {
        animation: l
    }
    );
    var r=document.getElementById("doughnut-chart").getContext("2d");
    window.myDoughnut=new Chart(r).Doughnut(doughnutData, {
        animation: l
    }
    )
    */
}

,
handleChartJs=function() {
    $(window).load(function() {
        handleGenerateGraph(!0)
    }
    ),
    $(window).resize(function() {
        handleGenerateGraph()
    }
    )
}

,
ChartJs=function() {
    "use strict";
    return {
        init:function() {
            handleChartJs()
        }
    }
}

();