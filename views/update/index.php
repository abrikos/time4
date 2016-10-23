<?php
/* @var $this yii\web\View */
?>
<h1>Процедура обновления</h1>

<h3>Первый раз:</h3>
<pre>
    cd C:\xampp\htdocs\
    git clone https://github.com/abrikos/time4
    cd time4
    rm *
REM extract from source.zip with "vendor" folder
    git reset  --hard origin/master
    git pull
    cp ..\time2\db\database.db db\
</pre>

<a href="/update/pull" class="btn btn-primary">Pull Script changes</a>
<a href="/update/migrate" class="btn btn-primary">Alter database</a>
