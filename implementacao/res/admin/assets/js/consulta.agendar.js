/*
Template Name: Color Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.6
Version: 2.0.0
Author: Sean Ngu
Website: http://www.seantheme.com/color-admin-v2.0/admin/html/
*/

var handleCalendarDemo=function() {
    $("#external-events .fc-event").each(function() {
        $(this).data("event", {
            title: $.trim($(this).text()), stick: !0, color: $(this).attr("data-color")?$(this).attr("data-color"): ""
        }
        ), $(this).draggable( {
            zIndex: 999, revert: !0, revertDuration: 0
        }
        )
    }
    );
    var t=new Date,
    e=t.getFullYear(),
    a=t.getMonth()+1;
    a=10>a?"0"+a:a,
    $("#calendar").fullCalendar( {
        header: {
            left: "month,agendaWeek,agendaDay", center: "title", right: "prev,today,next "
        }
        , droppable:!0, drop:function() {
            $(this).remove()
        }
        , selectable:!0, selectHelper:!0, select:function(t, e) {
            var a, r=prompt("Event Title:");
            r&&(a= {
                title: r, start: t, end: e
            }
            , $("#calendar").fullCalendar("renderEvent", a, !0)), $("#calendar").fullCalendar("unselect")
        }
        , editable:!0, eventLimit:!0, events:[ ],
        editable: true,
		selectable: true,
		select : function(start, end, allDay) {//Ao clicar na celula do calendario
			data = new Date(start);
			dia	= data.getDate();
			if(dia<10){ dia = "0"+dia; }
            $('#dataAtualizar').val(data.getFullYear()+"-"+(data.getMonth()+1)+"-"+dia);
            $('#formAtualizar').submit(); 
        }        
    }
    )
}

,
Calendar=function() {
    "use strict";
    return {
        init:function() {
            handleCalendarDemo()
        }
    }
}

();