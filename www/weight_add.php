<?php
include "_sql.php";
if(isset($_POST["weight"])){
    if(filter_var($_POST["weight"], FILTER_VALIDATE_FLOAT)){
        if(sql_insert_weight((int)($_POST["weight"]*100.0))){
            $msg = "Wert erfolgreich hinzugefügt!";
        }else{
            $msg = "Fehler beim hinzufügen!";
        }
    }else{
        $msg = "Angegebener Wert fehlerhaft, speichern abgebrochen!";
    }
}
?>

<head>
    <title>Gewicht hinzufügen - Persönliches Tracking</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="general.css">
</head>
<body>
    <div>
        <div>
            <h1>Gewicht hinzufügen</h1>
            <a href="./"><button type="button">Startseite</button></a>
            <?= '<p style="font-weight: bold;color: orange">' . ((isset($msg)) ? $msg : "") . '</p>' ?>
        </div>

        <div></div>

        <div>
            <table>
                <tr>
                    <td>Letzter Eintrag:</td>
                    <td colspan="2">
                        <?php
                        $last_value = sql_query_weight_last();
                        if($last_value){
                            echo ($last_value["weight"]/100.0), " kg (",
                            $last_value["timestamp"], ")";
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <form action="weight_add.php" method="post">
                        <td>Neuer Wert:</td>
                        <td>
                            <input
                                    type="number"
                                    id="weight"
                                    name="weight"
                                    min="0" max="200"
                                    step=".01"
                                    lang="de"
                            > kg
                        </td>
                        <td>
                            <button type="submit">Speichern</button>
                        </td>
                    </form>
                </tr>
            </table>
        </div>
    </div>
</body>
