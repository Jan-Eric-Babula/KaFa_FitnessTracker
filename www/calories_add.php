<?php
include "_sql.php";
if(isset($_POST["type"])){
    if($_POST["type"]=="reference"){
        if(sql_insert_calories_reference($_POST["reference"])){
            $msg = "Eintrag hinzugefügt!";
        }else{
            $msg = "Fehler beim hinzufügen!";
        }
    }else{
        if(isset($_POST["save_ref"])){
            if(isset($_POST["calories"]) && isset($_POST["description"]) && isset($_POST["description_clean"])){
                if((filter_var($_POST["calories"], FILTER_VALIDATE_INT) || $_POST["calories"]=="0") && strlen($_POST["description"])>0){

                    $ret = sql_insert_reference((int)$_POST["calories"], $_POST["description"], $_POST["description_clean"]);
                    if($ret){

                        if(sql_insert_calories_reference($ret)){
                            $msg = "Eintrag gespeichert und hinzugefügt!";
                        }else{
                            $msg = "Eintrag gespeichert, aber nicht hinzugefügt!";
                        }

                    }else{
                        $msg = "Fehler beim speicher!";
                    }

                }else{
                    $msg = "Werte sind ungültig! Speichern abgebrochen!";
                }
            }else{
                $msg = "Werte fehlen! Speichern abgebrochen!";
            }
        }else{
            if(sql_insert_calories_custom((int)$_POST["calories"], $_POST["description"])){
                $msg = "Erfolgreich hinzugefügt!";
            }else{
                $msg = "Fehler beim einfügen!";
            }
        }
    }
}
?>

<head>
    <title>Kalorien hinzufügen - Persönliches Tracking</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="general.css">
    <script type="text/javascript" src="general.js"></script>
</head>
<body>
    <div>
        <div>
            <h1>Kalorien hinzufügen</h1>
            <a href="./"><button type="button">Startseite</button></a>
            <?= '<p style="font-weight: bold;color: orange">' . ((isset($msg)) ? $msg : "") . '</p>' ?>
        </div>

        <div></div>

        <div>
            <table>
                <form action="calories_add.php" method="get">
                    <tr>
                        <td>Suche:</td>
                        <td>
                            <script>
                                function clean_search(){
                                    document.getElementById("search").value = clean_text(document.getElementById("search_dirty").value);
                                }
                            </script>
                            <input
                                type="text"
                                id="search_dirty"
                                name="search_dirty"
                                minlength="0"
                                maxlength="255"
                                onchange="clean_search();"
                                onkeyup="clean_search();"
                                onpaste="clean_search();"
                                oninput="clean_search();"
                                value="<?= (isset($_GET["search_dirty"])) ? $_GET["search_dirty"] : "" ?>"
                            >
                            <input
                                type="hidden"
                                id="search"
                                name="search"
                                value="<?= (isset($_GET["search"])) ? $_GET["search"] : "" ?>"
                            >
                        </td>
                        <td>
                            <button type="submit">Suchen</button>
                            <button type="submit" onclick="document.getElementById('search_dirty').value ='';clean_search();">Zurücksetzen</button>
                        </td>
                    </tr>
                </form>
                <form action="calories_add.php" method="post">
                    <tr>
                        <td>Referenz:</td>
                        <td>
                            <select
                                name="reference"
                                id="reference"
                                onchange='document.getElementById("submit_reference").removeAttribute("disabled")'
                            >
                                <option disabled selected value> -- Wählen -- </option>
                                <?php
                                if(isset($_GET["search"]) && $_GET["search"]!=""){
                                    $data = sql_query_reference_search($_GET["search"]);
                                }else{
                                    $data = sql_query_reference_all();
                                }

                                foreach($data as $row){
                                    echo "<option value='".$row["id"]."'>";
                                    echo $row["description"];
                                    echo "</option>";
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <button id="submit_reference" type="submit" name="type" value="reference" disabled>Speichern</button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="height: 10px;"></td>
                    </tr>
                    <tr>
                        <td>Kalorien:</td>
                        <td>
                            <input
                                type="number"
                                id="calories"
                                name="calories"
                                min="0" max="2000"
                                step="1"
                            > kcal
                        </td>
                        <td>
                            <input
                                type="checkbox"
                                id="save_ref"
                                name="save_ref"
                                value="1"
                            > Merken?
                        </td>
                    </tr>
                    <tr>
                        <td>Name:</td>
                        <td>
                            <script>
                                function clean_description(){
                                    document.getElementById("description_clean").value = clean_text(document.getElementById("description").value);
                                }
                            </script>
                            <input
                                type="text"
                                id="description"
                                name="description"
                                minlength="0"
                                maxlength="255"
                                onchange="clean_description();"
                                onkeyup="clean_description();"
                                onpaste="clean_description();"
                                oninput="clean_description();"
                            >
                            <input
                                type="hidden"
                                id="description_clean"
                                name="description_clean"
                            >
                        </td>
                        <td>
                            <button id="submit_custom" type="submit" name="type" value="custom">Speichern</button>
                        </td>
                    </tr>
                </form>
            </table>
        </div>

        <div></div>
        <div></div>

        <div>
            <table class="content">
                <tr>
                    <th>Datum</th>
                    <th>Kalorien</th>
                    <th>Beschreibung</th>
                </tr>
                <?php
                $data = sql_query_calories_time(1);
                $total = 0;
                foreach($data as $row){
                    $total += $row["calories"];
                    echo "<tr>", "<td>", $row["timestamp"], "</td>";
                    echo "<td>", $row["calories"], " kcal</td>";
                    echo "<td>", $row["description"], "</td>", "</tr>";
                }
                echo "<tr style='font-weight: bold'>", "<td>Total:</td><td>", $total ," kcal</td><td>",sizeof($data) ," Einträge (",get_daily_percentage($total,1),"%)</td>";
                ?>
            </table>
        </div>
    </div>
</body>
