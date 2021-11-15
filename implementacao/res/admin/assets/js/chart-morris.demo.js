/*
Template Name: Color Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.6
Version: 2.0.0
Author: Sean Ngu
Website: http://www.seantheme.com/color-admin-v2.0/admin/html/
*/ var blue = "#348fe2",
    blueLight = "#5da5e8",
    blueDark = "#1993E4",
    aqua = "#49b6d6",
    aquaLight = "#6dc5de",
    aquaDark = "#3a92ab",
    green = "#00acac",
    greenLight = "#33bdbd",
    greenDark = "#008a8a",
    orange = "#f59c1a",
    orangeLight = "#f7b048",
    orangeDark = "#c47d15",
    dark = "#2d353c",
    grey = "#b6c2c9",
    purple = "#727cb6",
    purpleLight = "#8e96c5",
    purpleDark = "#5b6392",
    red = "#ff5b57";
var handleMorrisLineChart = function () {
    var e = [
        { period: "2011 Q3", licensed: 3407, sorned: 660 },
        { period: "2011 Q2", licensed: 3351, sorned: 629 },
        { period: "2011 Q1", licensed: 3269, sorned: 618 },
        { period: "2010 Q4", licensed: 3246, sorned: 661 },
        { period: "2009 Q4", licensed: 3171, sorned: 676 },
        { period: "2008 Q4", licensed: 3155, sorned: 681 },
        { period: "2007 Q4", licensed: 3226, sorned: 620 },
        { period: "2006 Q4", licensed: 3245, sorned: null },
        { period: "2005 Q4", licensed: 3289, sorned: null },
    ];
    Morris.Line({ element: "morris-line-chart", data: e, xkey: "period", ykeys: ["licensed", "sorned"], labels: ["Licensed", "Off the road"], resize: true, lineColors: [dark, blue] });
};
var handleMorrisBarChart = function () {
    Morris.Bar({
        element: "morris-bar-chart",
        data: [
            { device: "iPhone", geekbench: 136 },
            { device: "iPhone 3G", geekbench: 137 },
            { device: "iPhone 3GS", geekbench: 275 },
            { device: "iPhone 4", geekbench: 380 },
            { device: "iPhone 4S", geekbench: 655 },
            { device: "iPhone 5", geekbench: 1571 },
        ],
        xkey: "device",
        ykeys: ["geekbench"],
        labels: ["Geekbench"],
        barRatio: 0.4,
        xLabelAngle: 35,
        hideHover: "auto",
        resize: true,
        barColors: [dark],
    });
};
var handleMorrisAreaChart = function () {
    Morris.Area({
        element: "morris-area-chart",
        data: [
            { period: "2021 Q1", agendado: 2666, realizado: null },
            { period: "2021 Q2", agendado: 2778, realizado: 2294 },
            { period: "2021 Q3", agendado: 4912, realizado: 1969 },
            { period: "2021 Q4", agendado: 3767, realizado: 3597 },
            { period: "2021 Q1", agendado: 6810, realizado: 1914 },
            { period: "2021 Q2", agendado: 5670, realizado: 4293 },
            { period: "2021 Q3", agendado: 4820, realizado: 3795 },
            { period: "2021 Q4", agendado: 6073, realizado: 5967 },
            { period: "2021 Q1", agendado: 6807, realizado: 4460 },
            { period: "2021 Q2", agendado: 8432, realizado: 5713 },
        ],
        xkey: "period",
        ykeys: ["realizado", "agendado"],
        labels: ["Realizado", "Agendado"],
        pointSize: 2,
        hideHover: "auto",
        resize: true,
        lineColors: [red, orange, dark],
    });
};
var handleMorrisDonusChart = function () {
    Morris.Donut({
        element: "morris-donut-chart",
        data: [
            { label: "Público Geral", value: 25 },
            { label: "Pessoas Idosas", value: 40 },
            { label: "Trabalhadores de Educação", value: 25 },
            { label: "Trabalhadores de Saúde", value: 10 },
        ],
        formatter: function (e) {
            return e + "%";
        },
        resize: true,
        colors: [dark, orange, red, grey],
    });
};
var MorrisChart = (function () {
    "use strict";
    return {
        init: function () {
            //handleMorrisLineChart();
            //handleMorrisBarChart();
            handleMorrisAreaChart();
            handleMorrisDonusChart();
        },
    };
})();
