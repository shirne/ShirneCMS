
jQuery.extend(jQuery.fn, {
    tags: function (nm, onupdate) {
        var data = [];
        var tpl = '<span class="badge badge-info">{@label}<input type="hidden" name="' + nm + '" value="{@label}"/><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></span>';
        var item = $(this).parents('.form-control');
        var labelgroup = $('<span class="badge-group"></span>');
        var input = this;
        this.before(labelgroup);
        this.on('keyup', function () {
            var val = $(this).val().replace(/，/g, ',');
            var updated = false;
            if (val && val.indexOf(',') > -1) {
                var vals = val.split(',');
                for (var i = 0; i < vals.length; i++) {
                    vals[i] = vals[i].replace(/^\s|\s$/g, '');
                    if (vals[i] && data.indexOf(vals[i]) === -1) {
                        data.push(vals[i]);
                        labelgroup.append(tpl.compile({ label: vals[i] }));
                        updated = true;
                    }
                }
                input.val('');
                if (updated && onupdate) onupdate(data);
            }
        }).on('blur', function () {
            var val = $(this).val();
            if (val) {
                $(this).val(val + ',').trigger('keyup');
            }
        }).trigger('blur');
        labelgroup.on('click', '.close', function () {
            var tag = $(this).parents('.badge').find('input').val();
            var id = data.indexOf(tag);
            if (id) data.splice(id, 1);
            $(this).parents('.badge').remove();
            if (onupdate) onupdate(data);
        });
        item.click(function () {
            input.focus();
        });
    }
});