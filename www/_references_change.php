<?php
include "_sql.php";

if(!isset($_POST["command"])){
    header('Location: references.php');
    exit();
}
if($_POST["command"]=="new"){
    header(
        'Location: references.php?id=-1'
        .'&sort='.urlencode($_POST["sort"])
        .'&search_dirty='.((isset($_POST["search_dirty"])) ? $_POST["search_dirty"] : "")
        .'&search='.((isset($_POST["search"])) ? $_POST["search"] : "")
        .'&message='.urlencode("Bitte neuen Eintrag eingeben und dann speichern:")
    );
    exit();
}elseif ($_POST["command"]=="cancel"){
    header(
        'Location: references.php?'
        .'sort='.urlencode($_POST["sort"])
        .'&search_dirty='.((isset($_POST["search_dirty"])) ? $_POST["search_dirty"] : "")
        .'&search='.((isset($_POST["search"])) ? $_POST["search"] : "")
    );
    exit();
}elseif ($_POST["command"]=="delete"){
    if($_POST["id"]<=0){
        header('Location: references.php');
        exit();
    }else{
        if(sql_delete_reference($_POST["id"])){
            $msg = "Eintrag erfolgreich gelöscht!";
        }else{
            $msg = "ERR: Eintrag konnte nicht gelöscht werden!";
        }
        header(
            'Location: references.php?'
            .'sort='.urlencode($_POST["sort"])
            .'&search_dirty='.((isset($_POST["search_dirty"])) ? $_POST["search_dirty"] : "")
            .'&search='.((isset($_POST["search"])) ? $_POST["search"] : "")
            .'&message='.urlencode($msg)
        );
        exit();
    }
}elseif ($_POST["command"]=="save"){
    if(((int)$_POST["id"])==0){
        header('Location: references.php');
        exit();
    }elseif (((int)$_POST["id"])==-1){
        if(!(isset($_POST["calories"]) && isset($_POST["description"]) && isset($_POST["description_clean"]))){
            header('Location: references.php?message='.urlencode("Werte fehlen, speichern abgebrochen!"));
            exit();
        }
        if(!((filter_var($_POST["calories"], FILTER_VALIDATE_INT) || $_POST["calories"]=="0") && strlen($_POST["description"])>0)){
            header('Location: references.php?message='.urlencode("Werte inkorrekt, speichern abgebrochen!"));
            exit();
        }

        $ret = sql_insert_reference((int)$_POST["calories"], $_POST["description"], $_POST["description_clean"]);
        if($ret){
            $msg = "Eintrag erfolgreich hinzugefügt!";
        }else{
            $msg = "Fehler beim einfügen!";
        }

        header(
            'Location: references.php?'
            .'id='.(($ret) ?: 0)
            .'&sort='.urlencode($_POST["sort"])
            .'&search_dirty='.((isset($_POST["search_dirty"])) ? $_POST["search_dirty"] : "")
            .'&search='.((isset($_POST["search"])) ? $_POST["search"] : "")
            .'&message='.urlencode($msg)
        );
        exit();
    }else{
        if(!(isset($_POST["calories"]) && isset($_POST["description"]) && isset($_POST["description_clean"]))){
            header(
                'Location: references.php?'
                .'id='.$_POST["id"]
                .'&sort='.urlencode($_POST["sort"])
                .'&search_dirty='.((isset($_POST["search_dirty"])) ? $_POST["search_dirty"] : "")
                .'&search='.((isset($_POST["search"])) ? $_POST["search"] : "")
                .'&message='.urlencode("Werte fehlen! Speichern abgebrochen!")
            );
            exit();
        }
        if(!((filter_var($_POST["calories"], FILTER_VALIDATE_INT) || $_POST["calories"]=="0") && strlen($_POST["description"])>0)){
            header(
                'Location: references.php?'
                .'id='.$_POST["id"]
                .'&sort='.urlencode($_POST["sort"])
                .'&search_dirty='.((isset($_POST["search_dirty"])) ? $_POST["search_dirty"] : "")
                .'&search='.((isset($_POST["search"])) ? $_POST["search"] : "")
                .'&message='.urlencode("Werte inkorrekt! Speichern abgebrochen!")
            );
            exit();
        }

        $ret = sql_update_reference((int)$_POST["id"], (int)$_POST["calories"], $_POST["description"], $_POST["description_clean"]);
        if($ret){
            $msg = "Eintrag erfolgreich gespeichert!";
        }else{
            $msg = "Fehler beim speichern!";
        }

        header(
            'Location: references.php?'
            .'id='.$_POST["id"]
            .'&sort='.urlencode($_POST["sort"])
            .'&search_dirty='.((isset($_POST["search_dirty"])) ? $_POST["search_dirty"] : "")
            .'&search='.((isset($_POST["search"])) ? $_POST["search"] : "")
            .'&message='.urlencode($msg)
        );
        exit();
    }
}

header('Location: references.php');
exit();
