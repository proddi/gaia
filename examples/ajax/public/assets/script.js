setInterval(function(){
    $.ajax({
        url: "#",
        dataType: 'json',
        context: document.body,
        success: proceedResponse
    });
}, 500);

function proceedResponse(data) {
    var content = data.content || {};
    for (var key in content) {
        var cnt = content[key];
        flashEffect($("#ajax_"+key), content[key]);
    }
}

function flashEffect(node, content) {
    if (node.html() == content) return;
    node.fadeOut(100, function() {
        $(this).html(content);
    }).fadeIn(200);
}