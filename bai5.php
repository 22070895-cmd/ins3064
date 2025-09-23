<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        // http://ins3064.test/bai4.php?x=5&y=6
        $x = $_GET['x'];
        $y = $_GET['y'];
        echo "$x == $y: ". ($x == $y) . "<br/>";
        echo "$x != $y: ". ($x != $y) . "<br/>";
        echo "$x < $y: ". ($x < $y) . "<br/>";
        echo "$x > $y: ". ($x > $y) . "<br/>";
        echo "$x <= $y: ". ($x <= $y) . "<br/>";
        echo "$x >= $y: ". ($x >= $y) . "<br/>";

        echo "$x == $y: ". ($x == $y) . "<br/>";
        echo "$x != $y: ". ($x != $y) . "<br/>";
        echo "$x < $y: ". ($x < $y) . "<br/>";
        echo "$x > $y: ". ($x > $y) . "<br/>";
        echo "$x <= $y: ". ($x <= $y) . "<br/>";
        echo "$x >= $y: ". ($x >= $y) . "<br/>";
        ?>
    </body>
</html>