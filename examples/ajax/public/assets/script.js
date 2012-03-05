setInterval(function(){
    $.ajax({
        url: "#",
        dataType: 'json',
        context: document.body,
        success: proceedResponse
    });
}, 5000);

function proceedResponse(data) {
    var content = data.content || {};
    console.log(content);
    for (var key in content) {
        console.log("#ajax_"+key, content[key]);
        $("#ajax_"+key).fadeOut(100, function() {
            $(this).html(content[key]);
        }).fadeIn(200);
    }
}