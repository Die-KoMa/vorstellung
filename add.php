<?php
require("config.php");

function error($str) {
    echo '<p class="text-danger">Es ist ein Fehler beim Speichern aufgetreten: ' . htmlspecialchars($str, ENT_NOQUOTES|ENT_HTML5) . '</p>';
}
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <title><?php echo $eventname; ?> Anfangsplenum</title>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="//www.gstatic.com/freebase/suggest/4_1/suggest.min.css">
    <style>label {display: block;} ul {list-style-type: none; padding: 0;}</style>
</head>
<body class="container">
    <h1><?php echo $eventname; ?> Anfangsplenum</h1>
    <h2>Fachschaft hinzufügen</h2>
<?php
if(!empty($_POST['university'])) {
    $data = array('facts' => array(), 'enums' => array());
    foreach($inputs['facts'] as $i => $fact) {
        if(isset($_POST['facts']) && isset($_POST['facts'][$i]))
            // we could do type sanitizing here, but what for?
            $data['facts'][$i] = $_POST['facts'][$i];
        else
            $data['facts'][$i] = null;
    }
    foreach($inputs['enums'] as $i => $enum) {
        $data['enums'][$i] = array();
        if(isset($_POST['enums']) && isset($_POST['enums'][$i]) && is_array($_POST['enums'][$i]))
            foreach($_POST['enums'][$i] as $item)
                if($item != "")
                    $data['enums'][$i][] = $item;
    }
    $fd = fopen($data_file, 'r+');
    if($fd === FALSE) {
        error($data_file . ' konnte nicht geöffnet werden.');
    } else {
        flock($fd, LOCK_EX);
        $universities = json_decode(stream_get_contents($fd), true);
        $universities[$_POST['university']] = $data;
        rewind($fd);
        ftruncate($fd, 0);
        fwrite($fd, json_encode($universities));
        flock($fd, LOCK_UN);
        fclose($fd);
?>
<p class="text-success">Die Daten wurden erfolgreich gespeichert!</p>
<?php
    }
?>
<?php
} else {
?>
    <form action="" method="post" role="form" class="form-horizontal">
        <fieldset>
            <legend>Zahlen, Daten, Fakten</legend>
            <label class="form-group">
                <span class="control-label col-sm-2">Hochschule</span>
                <span class="col-sm-10"><input type="text" id="university" class="form-control"></span>
                    <span class="col-sm-2"></span>
                    <span class="col-sm-10 help-block"></span>
                <input type="hidden" id="university_id" name="university" required>
            </label>
<?php foreach($inputs['facts'] as $i => $fact): ?>
            <label class="form-group">
                <span class="control-label col-sm-2"><?php echo $fact['name']; ?></span>
                <span class="col-sm-10"><input type="<?php echo $fact['type']; ?>" name="facts[<?php echo $i; ?>]" class="form-control"></span>
            </label>
<?php endforeach; ?>
        </fieldset>
<?php foreach($inputs['enums'] as $i => $enum): ?>
        <fieldset>
            <legend><?php echo $enum['name']; ?></legend>
            <span class="description">Gib bis zu 5 kurze Stichwörter an.</span>
            <ul>
            <?php for($j = 0; $j < 5; $j++): ?>
                <li class="form-group col-sm-12"><input type="text" maxlength="255" name="enums[<?php echo $i; ?>][]" class="form-control"></li>
            <?php endfor; ?>
        </ul>
        </fieldset>
<?php endforeach; ?>
        <p>Die hier gemachten Angaben werden automatisch ergänzt durch allgemeine Daten wie Bundesland und Anzahl der Studierenden.</p>
        <input type="submit" class="btn btn-primary">
    </form>
    <script src="//code.jquery.com/jquery-1.7.1.min.js"></script>
    <script src="//www.gstatic.com/freebase/suggest/4_1/suggest.min.js"></script>
    <script>
    $(function() {
        $("#university").suggest({
            key: '<?php echo $browser_api_key; ?>',
            filter:'(all type:/education/university)',
            lang:'de',
            status: ["Tippe um Vorschläge zu bekommen", "Suche…", "Wähle eine Hochschule aus der Liste:", "Irgendwas ist schiefgelaufen. Versuchs später nochmal!"]
        }).bind("fb-select", function(e, data) {
            $('#university_id').val(data.mid);
            $('#university').parents('.form-group')
                .removeClass('has-error')
                .children('.help-block').text('');
        });
        $('form').on('submit', function() {
            if($('#university_id').val() == "") {
                $('#university').parents('.form-group')
                    .addClass('has-error')
                    .children('.help-block').text('Es wurde keine Hochschule ausgewählt!');
                return false;
            }
            return true;
        })
    });
    </script>
<?php
}
?>
</body>
</html>
