 /*
This software is allowed to use under GPL or you need to obtain Commercial or Enterise License 
 to use it in non-GPL project. Please contact sales@dhtmlx.com for details
*/
gantt._tooltip = {}, gantt._tooltip_class = "gantt_tooltip", gantt.config.tooltip_timeout = 30, gantt._create_tooltip = function() {
    return this._tooltip_html || (this._tooltip_html = document.createElement("div"), this._tooltip_html.className = gantt._tooltip_class), this._tooltip_html
}, gantt._show_tooltip = function(t, e) {
    if (!gantt.config.touch || gantt.config.touch_tooltip) {
        var n = this._create_tooltip();
        n.innerHTML = t, gantt.$task_data.appendChild(n);
        var i = n.offsetWidth + 20, a = n.offsetHeight + 40, s = this.$task.offsetHeight, r = this.$task.offsetWidth, o = this.getScrollState();
        e.x += o.x, e.y += o.y, e.y = Math.min(Math.max(o.y, e.y), o.y + s - a), e.x = Math.min(Math.max(o.x, e.x), o.x + r - i), n.style.left = e.x + "px", n.style.top = e.y + "px"
    }
}, gantt._hide_tooltip = function() {
    this._tooltip_html && this._tooltip_html.parentNode && this._tooltip_html.parentNode.removeChild(this._tooltip_html), this._tooltip_id = 0
}, gantt._is_tooltip = function(t) {
    var e = t.target || t.srcElement;
    return gantt._is_node_child(e, function(t) {
        return t.className == this._tooltip_class
    })
}, gantt._is_task_line = function(t) {
    var e = t.target || t.srcElement;
    return gantt._is_node_child(e, function(t) {
        return t == this.$task_data
    })
}, gantt._is_node_child = function(t, e) {
    for (var n = !1; t && !n; )
        n = e.call(gantt, t), t = t.parentNode;
    return n
}, gantt._tooltip_pos = function(t) {
    if (t.pageX || t.pageY)
        var e = {x: t.pageX,y: t.pageY};
    var n = _isIE ? document.documentElement : document.body, e = {x: t.clientX + n.scrollLeft - n.clientLeft,y: t.clientY + n.scrollTop - n.clientTop}, i = gantt._get_position(gantt.$task);
    return e.x = e.x - i.x, e.y = e.y - i.y, e
}, gantt.attachEvent("onMouseMove", function(t, e) {
    this.config.tooltip_timeout ? (document.createEventObject && !document.createEvent && (e = document.createEventObject(e)), clearTimeout(gantt._tooltip_ev_timer), gantt._tooltip_ev_timer = setTimeout(function() {
        gantt._init_tooltip(t, e)
    }, gantt.config.tooltip_timeout)) : gantt._init_tooltip(t, e)
}), gantt._init_tooltip = function(t, e) {
    if (!this._is_tooltip(e) && (t != this._tooltip_id || this._is_task_line(e))) {
        if (!t)
            return this._hide_tooltip();
        this._tooltip_id = t;
        var n = this.getTask(t), i = this.templates.tooltip_text(n.start_date, n.end_date, n);
        i || this._hide_tooltip(), this._show_tooltip(i, this._tooltip_pos(e))
    }
}, gantt.attachEvent("onMouseLeave", function(t) {
    gantt._is_tooltip(t) || this._hide_tooltip()
}), gantt.templates.tooltip_date_format = gantt.date.date_to_str("%Y-%m-%d"), gantt.templates.tooltip_text = function(t, e, n) {
    return "<b>Task:</b> " + n.text + "<br/><b>Start date:</b> " + gantt.templates.tooltip_date_format(t) + "<br/><b>End date:</b> " + gantt.templates.tooltip_date_format(e)
};
