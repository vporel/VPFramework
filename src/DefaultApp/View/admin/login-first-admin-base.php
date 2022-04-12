<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_title ?> | Administration</title>
    <style>
        :root{
            --primary-color:rgb(150,150,0);
            --primary-color-lighten:rgb(180,180,0, .5);
        }
        body{
            background:rgb(230,230,230);
            font-family:calibri;
        }
        form{
            width:35%;margin:auto;margin-top:150px;background:white;
        }
        form h2{
            background:var(--primary-color); color:white; text-shadow:1px 1px 1px black; text-align:center;
            padding:5px 0;
        }
        form div{
            padding:15px;
        }
        form div label, form div input{
            display:block;
        }
        form div input{
            width:100%;
            border:0;
            border:1px solid lightgray;
            border-radius:5px;
            line-height:30px;
            margin:5px 0 10px 0;
        }
        form div input:hover, form div input:focus{
            color:var(--primary-color);
            border-color:var(--primary-color);
        }
        form>input{
            width:100%;
            background:var(--primary-color-lighten);
            border:0;
            padding:10px 0;
            transition:all .3s ease;
        }
        form>input:hover{
            background:var(--primary-color);
        }
        .error{
            color:red;display:block;text-align:center;
        }
    </style>
</head>
<body>
    <form method="post">
        <?= $_form ?>
    </form>
</body>
</html>