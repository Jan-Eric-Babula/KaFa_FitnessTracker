<?php
include "_sql.php";
if (!isset($_GET["id"])){ $_GET["id"] = 0; }
if (!isset($_GET["sort"])){ $_GET["sort"] = null; }
if(isset($_GET["search_dirty"])){if($_GET["search_dirty"]==""){
    unset($_GET["search_dirty"]);
    if(isset($_GET["search"])){unset($_GET["search"]);}
}}
if(isset($_GET["saction"])){
    if($_GET["saction"]=="reset"){
        header('Location: references.php?id='.urlencode($_GET["id"])."&sort=".urlencode($_GET["sort"]));
        exit();
    }
}
?>
<head>
    <title>Referenzen - Persönliches Tracking</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="general.css">
    <script type="text/javascript" src="general.js"></script>
</head>
<body>
    <div>
        <h1>Referenzen</h1>
        <a href="./"><button type="button">Startseite</button></a>
        <?= '<p style="font-weight: bold;color: orange">' . ((isset($_GET["message"])) ? $_GET["message"] : "") . '</p>' ?>
    </div>

    <div></div>

    <div>
        <div>
            <script>
                function confirm_delete(form){
                    if(form.command_pressed=="delete"){
                        return confirm("Möchtest du diesen Eintrag tatsächlich löschen?");
                    }else if(
                        form.command_pressed!="save" &&
                        (document.getElementById("old_calories").value!=document.getElementById("calories").value ||
                        document.getElementById("old_description").value!=document.getElementById("description").value)
                    ){
                        return confirm("Änderungen gehen beim Seitenwechsel verloren!\nTrotzdem fortfahren?");
                    }
                    return true;
                }
            </script>
            <form action="_references_change.php" method="post" onsubmit="return confirm_delete(this);">
                <?php if($_GET["id"]>0): ?>
                    <?php $edit_data = sql_query_reference_one($_GET["id"]); ?>
                    <input
                        type="hidden" id="old_calories"
                        value="<?= $edit_data["calories"] ?>"
                    >
                    <input
                        type="hidden" id="old_description"
                        value="<?= $edit_data["description"] ?>"
                    >
                <?php else: ?>
                    <?php $edit_data = null; ?>
                    <input type="hidden" id="old_calories" value="">
                    <input type="hidden" id="old_description" value="">
                <?php endif; ?>
                <?php if($_GET["id"]!=0): ?>
                    <input
                            type="hidden" id="id" name="id"
                            value="<?= $_GET["id"] ?>"
                    >
                <?php endif; ?>
                <input type="hidden" id="sort_copy" name="sort" value="<?= $_GET["sort"] ?>">
                <input type="hidden" id="search_copy" name="search" value="<?= (isset($_GET["search"])) ?  $_GET["search"] : "" ?>">
                <input type="hidden" id="search_dirty_copy" name="search_dirty" value="<?= (isset($_GET["search_dirty"])) ?  $_GET["search_dirty"] : "" ?>">

                <table>
                    <tr>
                        <td>ID</td>
                        <td><?= ($edit_data) ? $edit_data["id"] : "" ?></td>
                        <td><button onclick="this.form.command_pressed=this.value;" type="submit" name="command" value="new">Neu</button></td>
                    </tr>
                    <tr>
                        <td>Kalorien</td>
                        <td>
                            <input
                                type="number"
                                id="calories"
                                name="calories"
                                min="0" max="2000"
                                <?= ($_GET["id"]==0) ? "disabled": "" ?>
                                value="<?= ($edit_data) ? $edit_data["calories"] : "" ?>"
                            > kcal
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Name</td>
                        <td>
                            <script>
                                function clean_description(){
                                    document.getElementById("text_length").textContent = "(" + document.getElementById("description").value.length + "/255)";
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
                                <?= ($_GET["id"]==0) ? "disabled": "" ?>
                                value="<?= ($edit_data) ? $edit_data["description"] : "" ?>"
                            >
                        </td>
                        <td id="text_length">(0/255)</td>
                    </tr>
                    <tr>
                        <td>Name (System)</td>
                        <td>
                            <input
                                type="text"
                                id="description_clean"
                                name="description_clean"
                                minlength="0"
                                maxlength="255"
                                readonly
                                value="<?= ($edit_data) ? $edit_data["description_clean"] : "" ?>"
                            >
                        </td>
                        <td></td>
                    </tr>
                    <tr></tr>
                    <tr>
                        <td><button <?= ($_GET["id"]==0) ? "disabled":"" ?> onclick="this.form.command_pressed=this.value;" type="submit" name="command" value="cancel">Abbrechen</button></td>
                        <td><button <?= ($_GET["id"]>0) ? "":"disabled" ?> onclick="this.form.command_pressed=this.value;" type="submit" name="command" value="delete">Löschen</button></td>
                        <td><button <?= ($_GET["id"]==0) ? "disabled":"" ?> onclick="this.form.command_pressed=this.value;" type="submit" name="command" value="save">Speichern</button></td>
                    </tr>
                </table>
            </form>
        </div>

        <div></div>
        <div>
            <div style="width: 90%;height: 5px;background-color: black"></div>
        </div>
        <div></div>

        <div>
            <script>
                function confirm_change(){
                    if(document.getElementById("old_calories").value!=document.getElementById("calories").value ||
                            document.getElementById("old_description").value!=document.getElementById("description").value){
                            return confirm("Änderungen gehen beim Verlassen verloren!\nTrotzdem abbrechen?");
                        }
                    return true;
                }
            </script>
            <form action="references.php" method="get" onsubmit="return confirm_change();">
                <input type="hidden" id="id_copy" name="id" value="<?= $_GET["id"] ?>">
                <table>
                    <tr>
                        <td>Sortierung:</td>
                        <td colspan="2">
                            <select name="sort" id="sort" onchange="this.form.submit()">
                                <option value="id ASC"
                                    <?= ($_GET["sort"]=="id ASC") ? "selected=\"selected\"" : "" ?>
                                >ID Aufsteigend</option>
                                <option value="id DESC"
                                    <?= ($_GET["sort"]=="id DESC") ? "selected=\"selected\"" : "" ?>
                                >ID Absteigend</option>
                                <option value="calories ASC"
                                    <?= ($_GET["sort"]=="calories ASC") ? "selected=\"selected\"" : "" ?>
                                >Kalorien Aufsteigend</option>
                                <option value="calories DESC"
                                    <?= ($_GET["sort"]=="calories DESC") ? "selected=\"selected\"" : "" ?>
                                >Kalorien Absteigend</option>
                                <option value="description ASC"
                                    <?= ($_GET["sort"]==null || $_GET["sort"]=="description ASC") ? "selected=\"selected\"" : "" ?>
                                >Name Aufsteigend</option>
                                <option value="description DESC"
                                    <?= ($_GET["sort"]=="description DESC") ? "selected=\"selected\"" : "" ?>
                                >Name Absteigend</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Suche:</td>
                        <td>
                            <script>
                                function clean_search(){
                                    document.getElementById("search").value = clean_text(document.getElementById("search_dirty").value)
                                }
                            </script>
                            <input
                                    type="text"
                                    id="search_dirty"
                                    name="search_dirty"
                                    minlength="0"
                                    maxlength="255"
                                    size="20"
                                    onchange="clean_search();"
                                    onkeyup="clean_search();"
                                    onpaste="clean_search();"
                                    oninput="clean_search();"
                                    value="<?= (isset($_GET["search_dirty"])) ? $_GET["search_dirty"] : "" ?>"
                            >
                            <input type="hidden" id="search" name="search"
                                value="<?= (isset($_GET["search"])) ? $_GET["search"] : "" ?>"
                            >
                        </td>
                        <td>
                            <button type="submit" name="saction" value="search">Suchen</button>
                            <button type="submit" name="saction" value="reset">Zurücksetzen</button>
                        </td>
                    </tr>
                </table>

                <br><br>

                <table class="content">
                    <tr>
                        <th>Kalorien</th>
                        <th>Name</th>
                        <th></th>
                    </tr>
                    <script>
                        function change_id(new_id){
                            document.getElementById("id_copy").value = new_id;
                            return true;
                        }
                    </script>
                    <?php
                    $data = null;
                    if(isset($_GET["search"])){
                        $data = sql_query_reference_search($_GET["search"]);
                    }
                    else{
                        $data = sql_query_reference_all($_GET["sort"]);
                    }
                    if($data){
                        foreach ($data as $row){
                            echo "<tr>", "<td>", $row["calories"], " kcal</td>";
                            echo "<td>", $row["description"], "</td>";
                            echo "<td>";
                            echo '<button type="submit" onclick="change_id('.$row["id"].')">Bearbeiten</button>';
                            echo "</td>", "</tr>";
                        }
                    }
                    ?>
                </table>
            </form>
        </div>
    </div>
</body>

