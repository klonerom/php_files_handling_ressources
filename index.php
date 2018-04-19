<?php include('inc/head.php'); ?>

    <div id="content">
        <?php

        $files = searchFiles("files"); //on stocke dans un array tous les folders et files
        displayTree($files); //affichage des fichiers

        /**
         * @param $dir dossier de référence
         * @return array contenant tous les fichiers et folders du dossier $dir
         */
        function searchFiles($dir) {
            $result = array();

            if(is_dir($dir)) {//test si dossier non vide (sinon warning php)

                $scdir = scandir($dir);

                foreach ($scdir as $key => $value) {

                    if (!in_array($value, array(".", ".."))) { //les valeurs qu'on ne souhaite pas afficher
                        $chemin = $dir.DIRECTORY_SEPARATOR.$value;
                        //test si dossier alors on relance la fonction
                        if (is_dir($chemin)) {
                            //$chemin = $dir . DIRECTORY_SEPARATOR . $value;
                            //si Get alors suppression folder
                            if (isset($_POST)) {
                                foreach ($_POST as $valueGet) {
                                    if ($valueGet == $chemin) {
                                        shell_exec('rm -rf '.realpath($dir.DIRECTORY_SEPARATOR.$value)); // On efface
                                        header('Location:/');
                                        die;
                                    }
                                }
                            }

                            $result[$chemin] = searchFiles($chemin);
                        } else {
                            //si Get alors suppression file
                            if (isset($_POST)) {
                                foreach ($_POST as $valueGet) {
                                    if ($valueGet == $chemin) {
                                        unlink($dir.DIRECTORY_SEPARATOR.$value); // On efface
                                        header('Location:/');
                                        die;
                                    }
                                }
                            }
                            $result[] = $chemin;
                        }
                    }
                }
            } else {
                echo 'files folder doesn\'t exist - No X-files - The truth is out there !';
            }

            return $result;
        }


        /**
         * @param $tree liste des dossiers et fichiers contenu dans le dossier d'origine
         * @param int $i niveau dans l'arborescence (0 niveau de base, 1 pour suivant ...)
         */
        function displayTree($tree, $i = -1) { //tableau contient les chemins des éléments
            $i++;
            echo '<ul class="niv'.$i.'">';
            foreach ($tree as $key => $value) {

                $keyName = basename($key);//on ne recupere que le nom+format du chemin

                //si une valeur alors on a un sous fichier ou sous dossier
                if (is_array($value)) {
                    //echo '<li class="folder'.$i.'"><a href ="?dir=' . $key . '"><i class="fa fa-times fa-xs"></i></a>&nbsp;&nbsp;<i id="minus" class="far fa-minus-square"></i>&nbsp;&nbsp;<i class="fas fa-folder"></i>&nbsp;' . $keyName . '</li>';
                    echo '<li class="folder'.$i.'"><form action="" method="POST"><input type="hidden" name="cheminFolder" value="' . $key . '" /><button class="btn btn-delete btn-xs" type="submit" name="submit"><i class="fa fa-times fa-xs"></i></button></form>&nbsp;&nbsp;<i id="minus" class="far fa-minus-square"></i>&nbsp;&nbsp;<i class="fas fa-folder"></i>&nbsp;' . $keyName . '</li>';

                    //on relance la fonction pour définir les élements constituant les niveaux inférieurs
                    displayTree($value, $i);

                } else { //pas de niv inférieur, on affiche le resultat
                    $valueName = basename($value);
                    //echo '<li class="file'.$i.'"><a href ="?file=' . $value . '"><i class="fa fa-times fa-xs"></i></a>&nbsp;&nbsp;<i class="far fa-file-excel"></i>&nbsp; ' . $valueName . '</li>';
                    echo '<li class="file'.$i.' clear"><form action="" method="POST"><input type="hidden" name="cheminFile" value="' . $value . '" /><button class="btn btn-delete btn-xs" type="submit" name="submit"><i class="fa fa-times fa-xs"></i></button></form>&nbsp;&nbsp;<i class="far fa-file-excel"></i>&nbsp; ' . $valueName . '</li>';
                }
            }
            echo '</ul>';
        }

        ?>
    </div>

<script>

     $(document).ready(function(){
         //animation tree
        $('li.folder0').click(function() {
            $(this).next().slideToggle( 'slow' );
            $(this).find('#minus').toggleClass('fa-plus-square fa-minus-square');
        });
         //animation tree
         $('li.folder1').click(function() {
             $(this).next().slideToggle( 'slow' );
             $(this).find('#minus').toggleClass('fa-plus-square fa-minus-square');
         });

     });
</script>

<?php include('inc/foot.php'); ?>