<?php include('inc/head.php'); ?>

    <div id="content">
        <?php

        $files = searchFiles("files");
        afficher_tablo($files);

        function searchFiles($dir) {
            $result = array();

            $scdir = scandir($dir);

            foreach ($scdir as $key => $value) {

                if (!in_array($value,array(".",".."))) {

                    if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                        $result[$value] = searchFiles($dir . DIRECTORY_SEPARATOR . $value);
                    } else {
                        $result[] = $value;
                    }
                }
            }
            return $result;
        }

        function afficher_tablo($tableau, $i = -1) {
            $i++;
            foreach ($tableau as $key => $value) {
                $txt ='|';
                for ($j=1; $j<=$i; $j++) {
                    $txt .='-';
                }
                $txt .='&nbsp';

                if (is_array($value)) {

                    echo '<div class="niv'.$i.'">' . $txt . $key . '</div>';

                    afficher_tablo($value, $i);

                } else {
                    echo '<div class="niv'.$i.'">' . $txt . $value . '</div>';
                }
            }
        }

        ?>

    </div>


<?php include('inc/foot.php'); ?>