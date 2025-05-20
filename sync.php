<?php
if(session_status()!=PHP_SESSION_ACTIVE) session_start();
$ch = curl_init("https://markusmaal.ee/channel_db_lite/web/video/report?ord=ASC&frmt=json");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, 0);
$remote_data = curl_exec($ch);
curl_close($ch);

include($_SERVER["DOCUMENT_ROOT"] . "/connect.php");

function tstr($string, $connection) {
    return "\"" . mysqli_real_escape_string($connection, $string) . "\"";
}


foreach (json_decode($remote_data) as $record) {
    $url = $record->URL;
    $arr = mysqli_fetch_array(mysqli_query($connection, "SELECT COUNT(*), ID FROM channel_db WHERE URL = '$url'"));
    $result = $arr[0];
    $id = $arr[1];
    $values = array(
        tstr($record->Kanal, $connection),
        tstr($record->Video, $connection),
        $record->Kustutatud,
        tstr($record->Kuupäev, $connection),
        tstr($record->Kirjeldus, $connection),
        $record->Subtiitrid,
        $record->Avalik,
        $record->Ülekanne,
        $record->HD,
        tstr($record->URL, $connection),
        tstr($record->TitleMUI_en, $connection),
        tstr($record->TitleMUI_et, $connection),
        tstr($record->KirjeldusMUI_en, $connection),
        tstr($record->KirjeldusMUI_et, $connection),
        tstr($record->Filename, $connection),
        tstr($record->Category, $connection),
        tstr($record->CategoryMUI_en, $connection),
        tstr($record->Tags, $connection),
        tstr($record->OdyseeURL, $connection),
        tstr($record->KanalMUI_en, $connection),
        tstr($record->KanalMUI_et, $connection),
        1
    );
    if ($result == 0) {
        $query = "INSERT INTO channel_db (Kanal, Video, Kustutatud, Kuupäev, Kirjeldus, Subtiitrid, Avalik, Ülekanne, HD, URL, TitleMUI_en, TitleMUI_et, KirjeldusMUI_en, KirjeldusMUI_et, Filename, Category, CategoryMUI_en, Tags, OdyseeURL, KanalMUI_en, KanalMUI_et, Translated) VALUES (" . join(", ", $values) . ")";
        mysqli_query($connection, $query);
    } else {
        $variables = explode(", ", "Kanal, Video, Kustutatud, Kuupäev, Kirjeldus, Subtiitrid, Avalik, Ülekanne, HD, URL, TitleMUI_en, TitleMUI_et, KirjeldusMUI_en, KirjeldusMUI_et, Filename, Category, CategoryMUI_en, Tags, OdyseeURL, KanalMUI_en, KanalMUI_et, Translated");
        $setstr = "SET ";
        for ($i = 0; $i < count($variables); $i++) {
            $setstr .= $variables[$i] . " = " . $values[$i];
            if ($i < count($variables) - 1) {
                $setstr .= ", ";
            }
        }
        $query = "UPDATE channel_db " . $setstr . " WHERE ID = " . $id;
        mysqli_query($connection, $query);
    }
}

echo "Sünkroniseerimine OK";