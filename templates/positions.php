<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Positions Of Project</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous"> 
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js" integrity="sha384-THPy051/pYDQGanwU6poAc/hOdQxjnOEXzbT+OuUAFqNqFjL+4IGLBgCJC3ZOShY" crossorigin="anonymous"></script>
        
        <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="table_wrap">
            <table class="table table-striped" id="table2Id">
                <thead class="thead-inverse">
                    <tr>
                        <th rowspan='3' class="text-center">Фраза</th>
                        <th rowspan='3' class="text-center">Гео</th>
                        <th rowspan='1' class="text-center">Yandex</th>                        
                        <th rowspan='1' class="text-center">Google</th>
                        <th rowspan='1' class="text-center">Google mobile</th>
                        <th rowspan='1' class="text-center">go Mail</th>
                    </tr>
                    <tr>
                        <th class="text-center"><?= $reg ?></th>
                        <th class="text-center"><?= $reg ?></th>
                        <th class="text-center"><?= $reg ?></th>
                        <th class="text-center"><?= $reg ?></th>
                    </tr>
                    <tr>
                        <th class="text-center"><?= $time_yandex ?></th>
                        <th class="text-center"><?= $time_google ?></th>
                        <th class="text-center"><?= $time_google_mobile ?></th>
                        <th class="text-center"><?= $time_go_mail ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <?php foreach ($ready as $info): ?>
                            <tr class="text-center">
                                <?php foreach ($info as $value): ?>
                                    <td class="text-center">
                                        <?= $value ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col">
            <a class="btn btn-primary" href="/" role="button">Главная страница</a>
        </div>
    </body>
</html>
