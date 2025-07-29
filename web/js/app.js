$('#shorten-form').on('submit', function(e) {
    e.preventDefault();

    console.log(111)

    $.ajax({
        url: '/site/shorten',
        type: 'post',
        data: $(this).serialize(),
        dataType: 'json',
        beforeSend: function() {
            $('#result-container').hide();
        },
        success: function(data) {
            if (data.status === 'success') {
                $('#short-url').text(data.shortUrl).attr('href', data.shortUrl);
                $('#qr-code-container').html(
                    '<img src="' + data.qrCode + '" alt="QR Code">' +
                    '<p class="help-block">Отсканируйте QR-код для перехода</p>'
                );
                $('#result-container').show();
            } else {
                alert(data.message);
            }
        },
        error: function() {
            alert('Произошла ошибка!');
        }
    });
});