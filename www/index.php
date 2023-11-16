<?php
include "_sql.php";
if (isset($_GET["new_mode"])){ $_GET["mode"] = $_GET["new_mode"]; }
if (!isset($_GET["mode"])){ $_GET["mode"] = 2; }
if (!isset($_GET["t1"])){ $_GET["t1"] = 1; }
if (!isset($_GET["t2"])){ $_GET["t2"] = 3; }
?>

<head>
    <title>Übersicht - Persönliches Tracking</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="general.css">
</head>
<body>
    <form method="get">
        <div>
            <h1>Übersicht</h1>
        </div>

        <div></div>

        <div>
            <div>
                <table>
                    <tr>
                        <td style="width: 33%">Hinzufügen:</td>
                        <td style="width: 33%"><a href="weight_add.php"><button type="button">Gewicht</button></a></td>
                        <td style="width: 33%"><a href="calories_add.php"><button type="button">Kalorien</button></a></td>
                    </tr>
                    <tr>
                        <td>Verwaltung:</td>
                        <td><a href="references.php"><button type="button">Referenzen</button></a></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>

        <div></div>
        <div>
            <div style="width: 90%;height: 5px;background-color: black"></div>
        </div>
        <div></div>

        <div>
            <h2>Kalorien</h2>

            <table>
                <tr>
                    <td style="width: 33%">Modus:</td>
                    <td style="width: 33%"><button name="new_mode" type="submit" value="1">Zeitplan</button></td>
                    <td style="width: 33%"><button name="new_mode" type="submit" value="2">Kompakt</button></td>
                    <input type="hidden" id="mode" name="mode" value="<?= $_GET["mode"] ?>">
                </tr>
                <tr>
                    <td>Zeitraum:</td>
                    <td colspan="2">
                        <select name="t1" id="t1" onchange="this.form.submit()">
                            <option value="0" <?= ($_GET["t1"]==0) ? "selected=\"selected\"" : "" ?>>All</option>
                            <option value="1" <?= ($_GET["t1"]==1) ? "selected=\"selected\"" : "" ?>>Heute</option>
                            <option value="2" <?= ($_GET["t1"]==2) ? "selected=\"selected\"" : "" ?>>Gestern</option>
                            <option value="3" <?= ($_GET["t1"]==3) ? "selected=\"selected\"" : "" ?>>7 Tage</option>
                            <option value="4" <?= ($_GET["t1"]==4) ? "selected=\"selected\"" : "" ?>>30 Tage</option>
                        </select>
                    </td>
                </tr>
            </table>

            <br><br>

            <table class="content">
                <tr>
                    <th><?= ($_GET["mode"]==2) ? "Anzahl" : "Datum" ?></th>
                    <th>Kalorien</th>
                    <th>Beschreibung</th>
                </tr>
                <?php
                if($_GET["mode"]==1){
                    $data = sql_query_calories_time($_GET["t1"]);
                    $total = 0;
                    foreach($data as $row){
                        $total += $row["calories"];
                        echo "<tr>", "<td>", $row["timestamp"], "</td>";
                        echo "<td>", $row["calories"], " kcal</td>";
                        echo "<td>", $row["description"], "</td>", "</tr>";
                    }
                    echo "<tr style='font-weight: bold'>", "<td>Total:</td><td>", $total ," kcal</td><td>",sizeof($data) ," Einträge (",get_daily_percentage($total, $_GET["t1"]),"%)</td>";
                }
                else
                {
                    $data = sql_query_calories_compact($_GET["t1"]);
                    $total = 0;
                    $amount = 0;
                    foreach($data as $row){
                        $total += $row["calories"];
                        $amount += $row["amount"];
                        echo "<tr>", "<td>", $row["amount"], "</td>";
                        echo "<td>", $row["calories"], " kcal</td>";
                        echo "<td>", $row["description"], " (",$row["calories_single"], " kcal)</td>", "</tr>";
                    }

                    echo "<tr style='font-weight: bold'>", "<td>",$amount,"</td><td>", $total ," kcal</td><td>Total (",get_daily_percentage($total, $_GET["t1"]),"%)</td>";
                }
                ?>
            </table>
        </div>

        <div></div>
        <div>
            <div style="width: 90%;height: 5px;background-color: black"></div>
        </div>
        <div></div>

        <div>
            <h2>Gewicht</h2>

            <table>
                <tr>
                    <td>Zeitraum:</td>
                    <td colspan="2">
                        <select name="t2" id="t2" onchange="this.form.submit()">
                            <option value="0" <?= ($_GET["t2"]==0) ? "selected=\"selected\"" : "" ?>>All</option>
                            <option value="1" <?= ($_GET["t2"]==1) ? "selected=\"selected\"" : "" ?>>Heute</option>
                            <option value="2" <?= ($_GET["t2"]==2) ? "selected=\"selected\"" : "" ?>>Gestern</option>
                            <option value="3" <?= ($_GET["t2"]==3) ? "selected=\"selected\"" : "" ?>>7 Tage</option>
                            <option value="4" <?= ($_GET["t2"]==4) ? "selected=\"selected\"" : "" ?>>30 Tage</option>
                        </select>
                    </td>
                </tr>
            </table>

            <br><br>

            <table class="content">
                <tr>
                    <th>Datum</th>
                    <th>Gewicht</th>
                    <th>Veränderung</th>
                    <th>Zeitraum</th>
                </tr>
                <?php
                $data = sql_query_weight($_GET["t2"]);
                foreach($data as $row){
                    echo "<tr>", "<td>", $row["timestamp"], "</td>";
                    echo "<td>", ($row["weight"]/100.0), " kg</td>";
                    echo "<td>", ($row["diff"]/100.0), " kg</td>";
                    $dur = (int)$row["dur"];
                    $dur_unit = "";
                    if($dur > 86400){
                        $dur = round($dur/86400.0,2);
                        $dur_unit = " d";
                    }else{
                        $dur = round($dur/3600.0,1);
                        $dur_unit = " h";
                    }
                    echo "<td>", $dur, $dur_unit, "</td>", "</tr>";
                }
                ?>
            </table>
        </div>
    </form>
</body>
