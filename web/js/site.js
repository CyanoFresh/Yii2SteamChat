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

                    scrollToBottom();
                };
            }
        }
    });
}

function scrollToBottom() {
    var wtf    = $('.chat .panel-body');
    var height = wtf[0].scrollHeight;
    wtf.scrollTop(height);
}

$(document).ready(function () {
    connect();

    $('form#send').on('submit', function (event) {
        event.preventDefault();

        var message = $('#message').val();

        if (message !== '') {
            WS.send(message);

            $('#message').val('');
        }
    });

    scrollToBottom();
});
