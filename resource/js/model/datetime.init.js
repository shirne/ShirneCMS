function transOption(option) {
    if (!option) return {};
    var newopt = {};
    for (var i in option) {
        newopt[i.replace(/(_|-)[a-z]/g, function (d) {
            return d.substring(1).toUpperCase()
        })] = option[i];
    }
    return newopt;
}

var tooltips = window.datetimepickerTooltips ? window.datetimepickerTooltips : {
    today: '定位当前日期',
    clear: '清除已选日期',
    close: '关闭选择器',
    selectMonth: '选择月份',
    prevMonth: '上个月',
    nextMonth: '下个月',
    selectYear: '选择年份',
    prevYear: '上一年',
    nextYear: '下一年',
    selectDecade: '选择年份区间',
    selectTime: '选择时间',
    prevDecade: '上一区间',
    nextDecade: '下一区间',
    prevCentury: '上个世纪',
    nextCentury: '下个世纪'
};

function datetimeConfig(input) {
    return $.extend({
        tooltips: tooltips,
        format: 'YYYY-MM-DD',
        locale: 'zh-cn',
        showClear: true,
        showTodayButton: true,
        showClose: true,
        keepInvalid: true
    }, transOption($(input).data()));
}

function daterangeConfig(input) {
    return $.extend({
        tooltips: tooltips,
        format: 'YYYY-MM-DD',
        locale: 'zh-cn',
        showClear: true,
        showTodayButton: true,
        showClose: true,
        keepInvalid: true
    }, transOption($(input).data()));
}

jQuery(function ($) {

    //日期组件
    if ($.fn.datetimepicker) {
        $('.datepicker').each(function () {
            $(this).datetimepicker(datetimeConfig(this));
        });

        $('.date-range').each(function () {
            var from = $(this).find('[name=fromdate],.fromdate'), to = $(this).find('[name=todate],.todate');
            var options = daterangeConfig(this);
            from.datetimepicker(options).on('dp.change', function () {
                if (from.val()) {
                    to.data('DateTimePicker').minDate(from.val());
                }
            });
            to.datetimepicker(options).on('dp.change', function () {
                if (to.val()) {
                    from.data('DateTimePicker').maxDate(to.val());
                }
            });
        });
    }
})