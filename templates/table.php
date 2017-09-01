<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Projects</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous"> 
        <link rel="stylesheet" href="bootstrap/css/gif.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
        <script>
            function funcSuccess(data){
                $("#tableId > tbody").empty();
                $('tbody').append(data);
                $("#blocks").css("display", "none");
            }
            function funcBefore(){
                $("#blocks").css("display", "block");
            }
            $(document).ready(function(){
                $("#reload").click(function(){
                    var btn = $(this)
                    btn.html('Идёт загрузка...');
                    btn.addClass('loading');
                    btn.prop("disabled", true);
                    $(this).button('loading');
                        $.ajax({
                            type: "POST",
                            url: "templates/brain.php",
                            beforeSend: funcBefore,
                            success: funcSuccess,
                            complete: function () {
                                btn.html('Обновить данные');
                                btn.removeClass('loading');
                                btn.prop("disabled", false);
                            }
                        });
                }); 
            });
        </script>
    </head>

    <body>
    <div class="text-md-center">
        <table class="table table-bordered" id="tableId">
            <thead class="thead-inverse">
                <tr>
                    <th rowspan='2' class="text-center">ID</th>
                    <th rowspan='2' class="text-center">Проекты</th>
                    <th rowspan='2' class="text-center">тИЦ</th>
                    <th colspan='2' class="text-center">Индексация</th>                        
                    <th rowspan='2' class="text-center">Ядро</th>
                    <th colspan='1' class="text-center">Последняя проверка</th>
                </tr>
                <tr>
                    <th class="text-center">Я</th>
                    <th class="text-center">G</th>
                    <th class="text-center">Дата</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php foreach ($data as $values): ?>
                        <?php $hyperkey = $values['list_project_id']; ?>
                            <?php foreach ($values as $value) : ?>
                                <td><a href="/project/<?= $hyperkey ?>"><?= $value ?></a></td>
                            <?php endforeach; ?>
                </tr>
                    <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="col">
        <div class="col">
            <a id="lin" class="btn btn-primary" href="/token" role="button">Сменить токен</a>
        </div>
        <div class="col">
            <button type="button" class="btn btn-outline-danger" data-loading-text="Загружается..." id="reload">Обновить данные</button>
            <div id="blocks">
                <div class="windows8">
                    <div class="wBall" id="wBall_1"><div class="wInnerBall"></div></div>
                    <div class="wBall" id="wBall_2"><div class="wInnerBall"></div></div>
                    <div class="wBall" id="wBall_3"><div class="wInnerBall"></div></div>
                    <div class="wBall" id="wBall_4"><div class="wInnerBall"></div></div>
                    <div class="wBall" id="wBall_5"><div class="wInnerBall"></div></div>
                </div>
            </div>
        </div>
    </div>
    </body>
    <style>
        #blocks{
            display: none;
        }
        #reload{
            margin-top: 20px;
            width: 200px;
        }
        #lin{
            margin-top: 20px;
            width: 200px;
        }
    </style>
</html>

