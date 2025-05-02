function openNav() {
    document.getElementById("sidebar").style.width = "250px";
    document.getElementById("main").style.marginLeft = "250px";
}

function closeNav() {
    document.getElementById("sidebar").style.width = "0";
    document.getElementById("main").style.marginLeft = "0";
}

$(document).ready(function() {
    console.log('se incluye');
    $('#menu').click(function() {
        openNav();
    });

    $('.closebtn').click(function() {
        closeNav();
    });

    let timeout;

    function resetTimer() {
        clearTimeout(timeout);
        timeout = setTimeout(logout, 900000); // 15 minutes in milliseconds
    }

    function logout() {
        $.post('/logout', {
            _token: $('meta[name="csrf-token"]').attr('content')
        }).done(function() {
            window.location.href = '/';
        });
    }

    $(document).on('mousemove keydown click scroll', resetTimer);

    resetTimer();
});

window.openNav = openNav;
window.closeNav = closeNav;
