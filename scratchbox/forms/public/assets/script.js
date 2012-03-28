window.gaia = window.gaia || {}


//setInterval(function(){
//    $.ajax({
//        url: "#",
//        dataType: 'json',
//        context: document.body,
//        success: proceedResponse
//    });
//}, 500);

$(function() {
    $("input.text").each(function(index) {
        var node = $(this);
        var offset = node.offset();
        var template = $('<div style="position: absolute; top: -100px; left: '+offset.left+'px; width: '+node.outerWidth()+'px; display: none; background-color: red">foo bar</div>');
        $(document.body).append(template);
        node.keypress(function() {
            var offset = node.offset();
            console.log("keypress");
            template.css({
                top: (offset.top + node.outerHeight()) + "px",
                left: offset.left + "px",
                width: node.outerWidth()
            }).show();
        });
        node.focusout(function() {
            console.log("focusout");
            template.css("opacity", 0);
            template.hide();
        });
    });
});
