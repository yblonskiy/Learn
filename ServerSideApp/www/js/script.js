
function LoadTPs(rem, voltage, url, ddl) {

    $.ajax({
        url: url + 'api/handler.php',
        data: { action: 'loadtps', rem: rem, voltage: voltage },
        dataType: 'json',
        type: 'GET',
        cache: false,
        success: function (json) {

            // clear list
            $('#' + ddl + ' > option').remove();

            $('#' + ddl).append($('<option></option>').val('0').html('Не обрано...'));

            // add options
            $.each(json, function (i, item) {
                $('#' + ddl).append($('<option></option>').val(item.tp).html(item.tp));
            });

            if ($('#' + ddl + ' > option').length > 0) {
                $('#' + ddl).attr("disabled", false);
            }
            else {
                $('#' + ddl).attr("disabled", true);
            }
        }
    });
}

function LoadLines(tp, rem, voltage, url, ddl) {

    $.ajax({
        url: url + 'api/handler.php',
        data: { action: 'loadlines', tp: tp, rem: rem, voltage: voltage },
        dataType: 'json',
        type: 'GET',
        cache: false,
        success: function (json) {

            // clear list
            $('#' + ddl + ' > option').remove();

            $('#' + ddl).append($('<option></option>').val('0').html('Не обрано...'));

            // add options
            $.each(json, function (i, item) {
                $('#' + ddl).append($('<option></option>').val(item).html(item));
            });

            if ($('#' + ddl + ' > option').length > 0) {
                $('#' + ddl).attr("disabled", false);
            }
            else {
                $('#' + ddl).attr("disabled", true);
            }
        }
    });
}

function IsValidPassword(str) {
    var reg = new RegExp('^[a-zA-Z0-9]+$');

    if (!reg.test(str)) {
        return false;
    }

    return true;
}

function IsValidLogin(str) {
    var reg = new RegExp('^[a-zA-Z0-9\-_]+$');

    if (!reg.test(str)) {
        return false;
    }

    return true;
}

function CheckPassword(tbPasswordID, tbPasswordConfirmID) {

    var SourcePassword = $('#' + tbPasswordID).val();
    var pattern = new RegExp(".{6,12}");

    if (SourcePassword != $('#' + tbPasswordConfirmID).val()) {
        alert('Помилка! Пароль не співпадає. Будь-ласка, введіть ще раз.');
        return false;
    }
    else if (!pattern.test(SourcePassword) || !IsValidPassword(SourcePassword)) {
        alert('Довжина пароля від 6 до 12 латинських літер та цифр.');
        return false;
    }

    return true;
}

function CheckupEmail(emailValue, message) {

    if (emailValue == '') {
        return false;
    }

    var reg = new RegExp('^[a-zA-Z0-9][\\w\\.-]*[a-zA-Z0-9]*@[a-zA-Z0-9][\\w\\.-]*[a-zA-Z0-9]\\.[a-zA-Z][a-zA-Z\\.]*[a-zA-Z]$');

    if (!reg.test(emailValue)) {
        return false;
    }

    return true;
}

function getVoltageLevel(str) {
    var res = '';

    switch (str) {
        case '0.22':
            res = '0002';
            break;
        case '0.4':
            res = '0004';
            break;
        case '6':
            res = '0060';
            break;
        case '10':
            res = '0100';
            break;
        case '20':
            res = '0200';
            break;
    }

    return res;
}