<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        :root{
            --primary-color:orange;
        }
        .text-primary{
            color:var(--primary-color);
        }
        .text-center{
            text-align:center;
        }
        body{
            font-family: century gothic;
            padding:0;
            margin:0;
            background:rgb(230,230,230)
        }
        header{
            background:var(--primary-color);
            height:auto;
        }
        header h1{
            color:white;
            text-shadow:1px 1px 1px black;
            text-align: center;
            margin:0;
            padding:5px 0
        }
        

        #content{
            width:60%;
            margin:auto;
            padding-top:50px;
        }

        #content h2, #content h3{
            text-align:center;
        }

    </style>
</head>
<body>
    <header>
        <h1>VPFramework</h1>
    </header>
    <div id="content">
        <h2>
            Tout est <strong class="text-primary">OK</strong>
        </h2>
        <p class="text-center">
            <i>Créez au moins une route pour commencer le développement de votre application</i>
        </p>
        <section>
            <h3>Comment créer une route <font class="text-primary">?</font></h3>
        </section>
    </div>
</body>
</html>