/*!
 Responsive 2.0.0
 2014-2015 SpryMedia Ltd - datatables.net/license
*/
(function(c) {
    "function" === typeof define && define.amd ? define(["jquery", "datatables.net"], function(l) {
        return c(l, window, document)
    }) : "object" === typeof exports ? module.exports = function(l, k) {
        l || (l = window);
        if (!k || !k.fn.dataTable) k = require("datatables.net")(l, k).$;
        return c(k, l, l.document)
    } : c(jQuery, window, document)
})(function(c, l, k, p) {
    var n = c.fn.dataTable,
        j = function(a, b) {
            if (!n.versionCheck || !n.versionCheck("1.10.3")) throw "DataTables Responsive requires DataTables 1.10.3 or newer";
            this.s = {
                dt: new n.Api(a),
                columns: [],
                current: []
            };
            this.s.dt.settings()[0].responsive || (b && "string" === typeof b.details && (b.details = {
                type: b.details
            }), this.c = c.extend(!0, {}, j.defaults, n.defaults.responsive, b), a.responsive = this, this._constructor())
        };
    c.extend(j.prototype, {
        _constructor: function() {
            var a = this,
                b = this.s.dt,
                d = b.settings()[0];
            b.settings()[0]._responsive = this;
            c(l).on("resize.dtr orientationchange.dtr", n.util.throttle(function() {
                a._resize()
            }));
            d.oApi._fnCallbackReg(d, "aoRowCreatedCallback", function(e) {
                -1 !== c.inArray(!1, a.s.current) &&
                    c("td, th", e).each(function(e) {
                        e = b.column.index("toData", e);
                        !1 === a.s.current[e] && c(this).css("display", "none")
                    })
            });
            b.on("destroy.dtr", function() {
                b.off(".dtr");
                c(b.table().body()).off(".dtr");
                c(l).off("resize.dtr orientationchange.dtr");
                c.each(a.s.current, function(e, b) {
                    !1 === b && a._setColumnVis(e, !0)
                })
            });
            this.c.breakpoints.sort(function(a, b) {
                return a.width < b.width ? 1 : a.width > b.width ? -1 : 0
            });
            this._classLogic();
            this._resizeAuto();
            d = this.c.details;
            !1 !== d.type && (a._detailsInit(), b.on("column-visibility.dtr",
                function() {
                    a._classLogic();
                    a._resizeAuto();
                    a._resize()
                }), b.on("draw.dtr", function() {
                a._redrawChildren()
            }), c(b.table().node()).addClass("dtr-" + d.type));
            b.on("column-reorder.dtr", function(b, d, c) {
                if (c.drop) {
                    a._classLogic();
                    a._resizeAuto();
                    a._resize()
                }
            });
            this._resize()
        },
        _columnsVisiblity: function(a) {
            var b = this.s.dt,
                d = this.s.columns,
                e, f, h = d.map(function(a, b) {
                    return {
                        columnIdx: b,
                        priority: a.priority
                    }
                }).sort(function(a, b) {
                    return a.priority !== b.priority ? a.priority - b.priority : a.columnIdx - b.columnIdx
                }),
                g = c.map(d,
                    function(b) {
                        return b.auto && null === b.minWidth ? !1 : !0 === b.auto ? "-" : -1 !== c.inArray(a, b.includeIn)
                    }),
                m = 0;
            e = 0;
            for (f = g.length; e < f; e++) !0 === g[e] && (m += d[e].minWidth);
            e = b.settings()[0].oScroll;
            e = e.sY || e.sX ? e.iBarWidth : 0;
            b = b.table().container().offsetWidth - e - m;
            e = 0;
            for (f = g.length; e < f; e++) d[e].control && (b -= d[e].minWidth);
            m = !1;
            e = 0;
            for (f = h.length; e < f; e++) {
                var i = h[e].columnIdx;
                "-" === g[i] && (!d[i].control && d[i].minWidth) && (m || 0 > b - d[i].minWidth ? (m = !0, g[i] = !1) : g[i] = !0, b -= d[i].minWidth)
            }
            h = !1;
            e = 0;
            for (f = d.length; e < f; e++)
                if (!d[e].control &&
                    !d[e].never && !g[e]) {
                    h = !0;
                    break
                }
            e = 0;
            for (f = d.length; e < f; e++) d[e].control && (g[e] = h); - 1 === c.inArray(!0, g) && (g[0] = !0);
            return g
        },
        _classLogic: function() {
            var a = this,
                b = this.c.breakpoints,
                d = this.s.dt,
                e = d.columns().eq(0).map(function(a) {
                    var b = this.column(a),
                        e = b.header().className,
                        a = d.settings()[0].aoColumns[a].responsivePriority;
                    a === p && (a = c(b.header).data("priority") !== p ? 1 * c(b.header).data("priority") : 1E4);
                    return {
                        className: e,
                        includeIn: [],
                        auto: !1,
                        control: !1,
                        never: e.match(/\bnever\b/) ? !0 : !1,
                        priority: a
                    }
                }),
                f = function(a,
                    b) {
                    var d = e[a].includeIn; - 1 === c.inArray(b, d) && d.push(b)
                },
                h = function(d, c, i, h) {
                    if (i)
                        if ("max-" === i) {
                            h = a._find(c).width;
                            c = 0;
                            for (i = b.length; c < i; c++) b[c].width <= h && f(d, b[c].name)
                        } else if ("min-" === i) {
                        h = a._find(c).width;
                        c = 0;
                        for (i = b.length; c < i; c++) b[c].width >= h && f(d, b[c].name)
                    } else {
                        if ("not-" === i) {
                            c = 0;
                            for (i = b.length; c < i; c++) - 1 === b[c].name.indexOf(h) && f(d, b[c].name)
                        }
                    } else e[d].includeIn.push(c)
                };
            e.each(function(a, e) {
                for (var d = a.className.split(" "), f = !1, j = 0, l = d.length; j < l; j++) {
                    var k = c.trim(d[j]);
                    if ("all" === k) {
                        f = !0;
                        a.includeIn = c.map(b, function(a) {
                            return a.name
                        });
                        return
                    }
                    if ("none" === k || a.never) {
                        f = !0;
                        return
                    }
                    if ("control" === k) {
                        f = !0;
                        a.control = !0;
                        return
                    }
                    c.each(b, function(a, b) {
                        var c = b.name.split("-"),
                            d = k.match(RegExp("(min\\-|max\\-|not\\-)?(" + c[0] + ")(\\-[_a-zA-Z0-9])?"));
                        d && (f = !0, d[2] === c[0] && d[3] === "-" + c[1] ? h(e, b.name, d[1], d[2] + d[3]) : d[2] === c[0] && !d[3] && h(e, b.name, d[1], d[2]))
                    })
                }
                f || (a.auto = !0)
            });
            this.s.columns = e
        },
        _detailsDisplay: function(a, b) {
            var d = this,
                e = this.s.dt,
                f = this.c.details.display(a, b, function() {
                    return d.c.details.renderer(e,
                        a[0], d._detailsObj(a[0]))
                });
            (!0 === f || !1 === f) && c(e.table().node()).triggerHandler("responsive-display.dt", [e, a, f, b])
        },
        _detailsInit: function() {
            var a = this,
                b = this.s.dt,
                d = this.c.details;
            "inline" === d.type && (d.target = "td:first-child");
            b.on("draw.dtr", function() {
                a._tabIndexes()
            });
            a._tabIndexes();
            c(b.table().body()).on("keyup.dtr", "td", function(a) {
                a.keyCode === 13 && c(this).data("dtr-keyboard") && c(this).click()
            });
            var e = d.target,
                d = "string" === typeof e ? e : "td";
            c(b.table().body()).on("mousedown.dtr", d, function(a) {
                a.preventDefault()
            }).on("click.dtr",
                d,
                function() {
                    if (c(b.table().node()).hasClass("collapsed") && b.row(c(this).closest("tr")).length) {
                        if (typeof e === "number") {
                            var d = e < 0 ? b.columns().eq(0).length + e : e;
                            if (b.cell(this).index().column !== d) return
                        }
                        d = b.row(c(this).closest("tr"));
                        a._detailsDisplay(d, false)
                    }
                })
        },
        _detailsObj: function(a) {
            var b = this,
                d = this.s.dt;
            return c.map(this.s.columns, function(c, f) {
                if (!c.never) return {
                    title: d.settings()[0].aoColumns[f].sTitle,
                    data: d.cell(a, f).render(b.c.orthogonal),
                    hidden: d.column(f).visible() && !b.s.current[f]
                }
            })
        },
        _find: function(a) {
            for (var b = this.c.breakpoints, d = 0, c = b.length; d < c; d++)
                if (b[d].name === a) return b[d]
        },
        _redrawChildren: function() {
            var a = this,
                b = this.s.dt;
            b.rows({
                page: "current"
            }).iterator("row", function(c, e) {
                b.row(e);
                a._detailsDisplay(b.row(e), !0)
            })
        },
        _resize: function() {
            var a = this,
                b = this.s.dt,
                d = c(l).width(),
                e = this.c.breakpoints,
                f = e[0].name,
                h = this.s.columns,
                g, m = this.s.current.slice();
            for (g = e.length - 1; 0 <= g; g--)
                if (d <= e[g].width) {
                    f = e[g].name;
                    break
                }
            var i = this._columnsVisiblity(f);
            this.s.current = i;
            e = !1;
            g = 0;
            for (d = h.length; g < d; g++)
                if (!1 === i[g] && !h[g].never) {
                    e = !0;
                    break
                }
            c(b.table().node()).toggleClass("collapsed", e);
            var j = !1;
            b.columns().eq(0).each(function(b, c) {
                i[c] !== m[c] && (j = !0, a._setColumnVis(b, i[c]))
            });
            j && this._redrawChildren()
        },
        _resizeAuto: function() {
            var a = this.s.dt,
                b = this.s.columns;
            if (this.c.auto && -1 !== c.inArray(!0, c.map(b, function(a) {
                    return a.auto
                }))) {
                a.table().node();
                var d = a.table().node().cloneNode(!1),
                    e = c(a.table().header().cloneNode(!1)).appendTo(d),
                    f = c(a.table().body().cloneNode(!1)).appendTo(d),
                    h = a.columns().header().filter(function(b) {
                        return a.column(b).visible()
                    }).to$().clone(!1).css("display", "table-cell");
                c(f).append(c(a.rows({
                    page: "current"
                }).nodes()).clone(!1)).find("th, td").css("display", "");
                if (f = a.table().footer()) {
                    var f = c(f.cloneNode(!1)).appendTo(d),
                        g = a.columns().header().filter(function(b) {
                            return a.column(b).visible()
                        }).to$().clone(!1).css("display", "table-cell");
                    c("<tr/>").append(g).appendTo(f)
                }
                c("<tr/>").append(h).appendTo(e);
                "inline" === this.c.details.type && c(d).addClass("dtr-inline collapsed");
                d = c("<div/>").css({
                    width: 1,
                    height: 1,
                    overflow: "hidden"
                }).append(d);
                d.insertBefore(a.table().node());
                h.each(function(c) {
                    c = a.column.index("fromVisible", c);
                    b[c].minWidth = this.offsetWidth || 0
                });
                d.remove()
            }
        },
        _setColumnVis: function(a, b) {
            var d = this.s.dt,
                e = b ? "" : "none";
            c(d.column(a).header()).css("display", e);
            c(d.column(a).footer()).css("display", e);
            d.column(a).nodes().to$().css("display", e)
        },
        _tabIndexes: function() {
            var a = this.s.dt,
                b = a.cells({
                    page: "current"
                }).nodes().to$(),
                d = a.settings()[0],
                e = this.c.details.target;
            b.filter("[data-dtr-keyboard]").removeData("[data-dtr-keyboard]");
            c("number" === typeof e ? ":eq(" + e + ")" : e, a.rows({
                page: "current"
            }).nodes()).attr("tabIndex", d.iTabIndex).data("dtr-keyboard", 1)
        }
    });
    j.breakpoints = [{
        name: "desktop",
        width: Infinity
    }, {
        name: "tablet-l",
        width: 1024
    }, {
        name: "tablet-p",
        width: 768
    }, {
        name: "mobile-l",
        width: 480
    }, {
        name: "mobile-p",
        width: 320
    }];
    j.display = {
        childRow: function(a, b, d) {
            if (b) {
                if (c(a.node()).hasClass("parent")) return a.child(d(), "child").show(), !0
            } else {
                if (a.child.isShown()) return a.child(!1),
                    c(a.node()).removeClass("parent"), !1;
                a.child(d(), "child").show();
                c(a.node()).addClass("parent");
                return !0
            }
        },
        childRowImmediate: function(a, b, d) {
            if (!b && a.child.isShown() || !a.responsive.hasHidden()) return a.child(!1), c(a.node()).removeClass("parent"), !1;
            a.child(d(), "child").show();
            c(a.node()).addClass("parent");
            return !0
        },
        modal: function(a) {
            return function(b, d, e) {
                if (d) c("div.dtr-modal-content").empty().append(e());
                else {
                    var f = function() {
                            h.remove();
                            c(k).off("keypress.dtr")
                        },
                        h = c('<div class="dtr-modal"/>').append(c('<div class="dtr-modal-display"/>').append(c('<div class="dtr-modal-content"/>').append(e())).append(c('<div class="dtr-modal-close">&times;</div>').click(function() {
                            f()
                        }))).append(c('<div class="dtr-modal-background"/>').click(function() {
                            f()
                        })).appendTo("body");
                    a && a.header && h.find("div.dtr-modal-content").prepend("<h2>" + a.header(b) + "</h2>");
                    c(k).on("keyup.dtr", function(a) {
                        27 === a.keyCode && (a.stopPropagation(), f())
                    })
                }
            }
        }
    };
    j.defaults = {
        breakpoints: j.breakpoints,
        auto: !0,
        details: {
            display: j.display.childRow,
            renderer: function(a, b, d) {
                return (a = c.map(d, function(a, b) {
                    return a.hidden ? '<li data-dtr-index="' + b + '"><span class="dtr-title">' + a.title + '</span> <span class="dtr-data">' + a.data + "</span></li>" : ""
                }).join("")) ? c('<ul data-dtr-index="' + b + '"/>').append(a) : !1
            },
            target: 0,
            type: "inline"
        },
        orthogonal: "display"
    };
    var o = c.fn.dataTable.Api;
    o.register("responsive()", function() {
        return this
    });
    o.register("responsive.index()", function(a) {
        a = c(a);
        return {
            column: a.data("dtr-index"),
            row: a.parent().data("dtr-index")
        }
    });
    o.register("responsive.rebuild()", function() {
        return this.iterator("table", function(a) {
            a._responsive && a._responsive._classLogic()
        })
    });
    o.register("responsive.recalc()", function() {
        return this.iterator("table", function(a) {
            a._responsive && (a._responsive._resizeAuto(), a._responsive._resize())
        })
    });
    o.register("responsive.hasHidden()", function() {
        var a = this.context[0];
        return a._responsive ? -1 !== c.inArray(!1, a._responsive.s.current) : !1
    });
    j.version = "2.0.0";
    c.fn.dataTable.Responsive = j;
    c.fn.DataTable.Responsive = j;
    c(k).on("init.dt.dtr", function(a, b) {
        if ("dt" === a.namespace && (c(b.nTable).hasClass("responsive") || c(b.nTable).hasClass("dt-responsive") || b.oInit.responsive || n.defaults.responsive)) {
            var d = b.oInit.responsive;
            !1 !== d && new j(b, c.isPlainObject(d) ? d : {})
        }
    });
    return j
});