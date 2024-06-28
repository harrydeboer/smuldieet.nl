if($('body').css('color') !== 'rgb(33, 37, 41)') {
    $("head").prepend('<link rel="stylesheet" href="/css/bootstrap.min.css">');
}

window.bootstrap || document.write('<script type="text/javascript" src="/dist/bootstrap.bundle.min.js"><\/script>');
