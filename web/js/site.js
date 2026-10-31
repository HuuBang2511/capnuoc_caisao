
$(function () {
    $('.navbar-nav .nav-item a').each(function () {
        if ($(this).prop('href') == window.location.href) {
            if ($(this).hasClass('navbar-nav-link')) {
                $(this).addClass('active');
            } else {
                $(this).parent().siblings('.navbar-nav-link').addClass('active');
            }
        }
    })

    $('.navbar-nav .nav-item a.navbar-nav-link').each(function () {
        if ($(this).prop('href') == window.location.href) {
            $(this).addClass('active');
        }
    })
})

function fixImageHeight() {
    var cardimage = $('.card-img-actions')
    cardimage.height(cardimage.width()*2/3)
}

function formatDate(dtStr) {
    var dt = new Date(dtStr);
    var formatted = dt.getDate().toString().padStart(2, '0')
        + '/' + (dt.getMonth() + 1).toString().padStart(2, '0')
        + '/' + dt.getFullYear();

    return formatted;
};

function formatDateTime(dtStr) {
    if(!dtStr) return null;
    
    var dt = new Date(dtStr);
    var formatted = dt.getDate().toString().padStart(2, '0')
        + '/' + (dt.getMonth() + 1).toString().padStart(2, '0')
        + '/' + dt.getFullYear()
        + ' ' + dt.getHours().toString().padStart(2, '0')
        + ':' + dt.getMinutes().toString().padStart(2, '0')
        + ':' + dt.getSeconds().toString().padStart(2, '0');

    return formatted;
};

function timeSince(timeStr) {
    var date = new Date(timeStr),
        timediff = Math.floor((new Date() - date) / 1000),
        timestring,
        remain;

    var days = Math.floor(timediff / 86400);
    remain = timediff % 86400;

    var hours = Math.floor(remain / 3600);

    remain = Math.floor(remain % 3600);
    var mins = Math.floor(remain / 60);
    var secs = Math.floor(remain % 60);

    if(secs == 0) timestring = 'Just now';
    if (secs > 0) timestring = secs + ' seconds ago';
    if (mins > 0) timestring = mins + ' mins ago';
    if (hours > 0) timestring = hours + ' hours ago';
    if (days > 0) timestring = days + ' days ago';

    return timestring;
}

Vue.use(Toasted, Option);
function toastMessage(type, message) {
    Vue.toasted.show(message, {
        type: type,
        theme: "bubble",
        position: "top-center",
        duration: 4000
    });
};

function substringMatcher(words) {
    return function (q, cb) {
        var matches, substrRegex;
        matches = [];
        substrRegex = new RegExp(q, 'i');
        $.each(words, function (i, word) {
            if (substrRegex.test(word)) {
                matches.push(word);
            }
        });
        cb(matches);
    };
};

function reverseDate(date) {
    var reverse = date.split("-").reverse().join("/");
    return reverse;
}

function offset(el) {
    var rect = el.parent(),
    scrollLeft = window.pageXOffset || document.documentElement.scrollLeft,
    scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    return { top: rect.top + scrollTop, left: rect.left + scrollLeft }
}

function getAvatarPath(avatar) {
    var path = '/' + (avatar ? 'uploads/' + avatar : 'resources/images/no_avatar.jpg')
    return path
}

function sendAjax(api, data, callback, type = 'POST') {
    $.ajax({
        url: api,
        type: type,
        data: data,
        success: function(resp) {
            callback(resp)
        },
        error: function(msg) {
            // toastMessage('error', 'Lỗi!')
            console.log('msg')
        }
    })
}