$(document).ready(function() { // вся магия после загрузки страницы
    $("#ajaxform").submit(function() { // перехватываем все при событии отправки
        var form = $("#ajaxform"); // запишем форму, чтобы потом не было проблем с this don't work

        var message = {};
        message.email = $("#ajaxform #email").val();
        message.name = $("#ajaxform #name").val();
        message.subject = $("#ajaxform #subject").val();
        message.message = $("#ajaxform #message").val();

        console.log("Message=" + message);
        var error = false; // предварительно ошибок нет

        //TODO: надо здесь валидировать данные
        form.find('input, textarea').each(function() { // пробежим по каждому полю в форме
            if (error)
                return;
            if ($(this).val() == '') { // если находим пустое
                alert('Заполните, пожалуйста, все поля!'); // говорим заполняй!  + $(this).attr('placeholder') + '"!'
                error = true; // ошибка
            }
        });


        if (!error) { // если ошибки нет
            var data = JSON.stringify(message); // подготавливаем данные $('#ajaxform').serializeArray()
            console.log("data=" + data);
            $.ajax({// инициализируем ajax запрос
                type: 'POST', // отправляем в POST формате, можно GET
                url: 'mail/form_to_mail.php', // путь до обработчика, у нас он лежит в той же папке
                dataType: 'json', // ответ ждем в json формате
                data: data, // данные для отправки
                beforeSend: function(data) { // событие до отправки
                    form.find('button[type="submit"]').attr('disabled', 'disabled'); // например, отключим кнопку, чтобы не жали по 100 раз
                },
                success: function(data) { // событие после удачного обращения к серверу и получения ответа
                    console.log('Success' + data);
                    if (data['error']) { // если обработчик вернул ошибку
                        alert("Error - " + data['error']); // покажем её текст
                    } else { // если все прошло ок
                        alert('Письмо отвравлено! Наш менеджер ответит Вам в ближайшее время!'); // пишем что все ок
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) { // в случае неудачного завершения запроса к серверу
                    alert("Server answer - " + xhr.status); // покажем ответ сервера
                    alert("Error message body - " + thrownError); // и текст ошибки
                },
                complete: function(data) { // событие после любого исхода
                    form.find('button[type="submit"]').prop('disabled', false); // в любом случае включим кнопку обратно
                }

            });
        }
        return false; // вырубаем стандартную отправку формы
    });

    $("#emailSignUpButton").click(function() {
        var email = $("#emailInput").val();
        //TODO: надо здесь валидировать данные
        console.log(email);

        if (email != '') {

            //var data = '{"email":"'+email+'"}';
            $.ajax({
                type: 'POST',
                url: 'mail/email_to_mail.php',
                dataType: 'json',
                data: {'email': email},
                beforeSend: function(data) {
                    $('#emailSignUpButton').attr('disabled', 'disabled');
                },
                success: function(data) {
                    console.log('Success ' + data['error']);
                    if (data['error']) {
                        alert("Error - " + data['error']);
                    } else {
                        alert('Письмо отвравлено! Наш менеджер ответит Вам в ближайшее время!');
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert("Server answer - " + xhr.status + ", Error message body - " + thrownError)
                },
                complete: function(data) {
                    $("#emailSignUpButton").prop('disabled', false);
                }
            });
            return false;

        } else {
            alert('Заполните, пожалуйста, все поля!');
            return false;
        }
    });
});