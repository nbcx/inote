$(document).ready(function () {
    //checkbox全选/取消全选
    $('.checkall').click(function() {
        var sel = $(this).attr("box");
        sel = sel?sel:'sel';
        $("."+sel+" input:checkbox").prop("checked",this.checked);
    })

    $("[form]").click(function() {
        var msg= $(this).attr("tips");
        if(msg && !confirm(msg)) {
            return false;
        }

        var name= $(this).attr("form");
        var url = $(this).attr("href");
        var form = $("form[name='"+name+"']");
        if(url) {
            form.attr('action',url);
		}
        form.submit();
        return false;
    })

    $("[confirm]").click(function(event) {
        var msg= $(this).attr("confirm");
        if(confirm(msg)) {
            return true;
        }
        return false;
    })

    $("[pageform]").each(function() {
        var name= $(this).attr("pageform");
        $(this).find('a').click(function () {
            var url = $(this).attr("href");
            var form = $("form[name='"+name+"']");
            if(url) {
                form.attr('action',url);
            }
            form.submit();
            return false;
        })
    })


});
