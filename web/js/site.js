var WS;

function connect() {
    console.log('Connecting to API...');
    $.ajax({
        url: '/site/token',
        success: function (data) {
            if (data == "nologin") {
                console.log('You have to be logged in');
            } else {
                console.log('Connecting to server...');

                WS = new WebSocket("ws://localhost:8080" + data);

                WS.onopen = function (e) {
                    console.log('Connected!');
                };
                WS.onerror = function (e) {
                    console.log('Error');
                };
                WS.onclose = function (e) {
                    WS = null;
                    console.log('Connection lost');
                };
                WS.onmessage = function (e) {
                    data = JSON.parse(e.data);
                    console.log(data);

                    var source   = $("#chat-post").html();
                    var template = Handlebars.compile(source);
                    var html    = template(data);

                    $(html).appendTo('#chat');

                    $(html).animate({
                        scrollTop: $(html).height()
                    }, 300);
                };
            }
        }
    });
}

$(document).ready(function () {
    connect();

    $('form#send').on('submit', function (event) {
        event.preventDefault();

        var message = $('#message').val();

        WS.send(message);

        $('#message').val('');
    });
});


//var socket = new WebSocket('ws://localhost:8080');

//socket.onopen = function(e) {
//    console.log("Connection established!");
//};
//
//socket.onmessage = function(e) {
//    data = $.parseJSON(e.data);
//    $('<div class="log"><div class="author">' + data.author + '</div><div class="message">' + data.message + '</div></div>').insertAfter('.log:last');
//    console.log(data);
//};
//
//$('button').on('click', function (event) {
//    event.preventDefault();
//
//    socket.send($('#message').val());
//    $('#message').val('');
//});
