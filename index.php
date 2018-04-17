<?php include('inc/head.php'); ?>

    <div id="content">
        <?php

        $files = searchFiles("files"); //on stocke dans un array tous les folders et files
        afficher_tablo($files); //affichage des fichiers

        function searchFiles($dir) {
            $result = array();

            $scdir = scandir($dir);

            foreach ($scdir as $key => $value) {

                if (!in_array($value,array(".",".."))) {

                    //test si dossier alors on relance la fonction
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {

                        //si Get alors suppression folder
                        if (isset($_GET)) {
                            foreach ($_GET as $valueGet) {
                                if ($valueGet == $value) {
                                    shell_exec('rm -rf ' . realpath($dir . DIRECTORY_SEPARATOR . $value)); // On efface
                                    header('Location:/');
                                    die;
                                }
                            }
                        }
                        $result[$value] = searchFiles($dir . DIRECTORY_SEPARATOR . $value);
                    } else {
                        //si Get alors suppression file
                        if (isset($_GET)) {
                            foreach ($_GET as $valueGet) {
                                if ($valueGet == $value) {
                                    unlink($dir . DIRECTORY_SEPARATOR . $value); // On efface
                                    header('Location:/');
                                    die;
                                }
                            }
                        }

                        $result[] = $value;
                    }
                }
            }

            return $result;
        }



        function afficher_tablo($tableau, $i = -1) {
            $i++;
            foreach ($tableau as $key => $value) {
                //mise en forme style |- (avec autant de - qu'il y a de profondeur de fichier $i)
                $txt ='|';
                for ($j=1; $j<=$i; $j++) {
                    $txt .='-';
                }
                $txt .='&nbsp';

                //si une valeur alors on a un sous fichier ou sous dossier
                if (is_array($value)) {

                    echo '<div class="niv'.$i.'"><a href ="?dir=' . $key . '" class="btn btn-default btn-xs">X</a>' . $txt . $key . '*</div>';

                    //on relance la fonction pour définir les élements constituant les niveaux inférieurs
                    afficher_tablo($value, $i);

                } else { //pas de niv inférieur, on affiche le resultat
                    echo '<div class="niv'.$i.'"><a href ="?file=' . $value . '" class="btn btn-default btn-xs">X</a>' . $txt . $value . '</div>';
                }
            }
        }

        ?>

    </div>


<?php include('inc/foot.php'); ?>