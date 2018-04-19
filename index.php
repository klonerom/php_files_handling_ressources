<?php include('inc/head.php');

        //save file content updated
        if (isset($_POST['content'])) {//save file updated
            $fileEdit = $_GET['file'];
            $fileOpen = fopen($fileEdit, "w");
            fwrite($fileOpen, $_POST['content']);
            fclose($fileOpen);

            header('Location: /');
            die;
        }

?>
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
                                foreach ($_POST as $valuePost) {
                                    if ($valuePost == $chemin) {
                                        shell_exec('rm -rf '.realpath($chemin)); // On efface
                                        header('Location:/');
                                        die;
                                    }
                                }
                            }

                            $result[$chemin] = searchFiles($chemin);
                        } else {
                            //si Get alors suppression file
                            if (isset($_POST)) {
                                foreach ($_POST as $valuePost) {
                                    if ($valuePost == $chemin) {
                                        unlink($chemin); // On efface
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
                    $extension = pathinfo(($valueName), PATHINFO_EXTENSION);
                    $extension_img = array('jpg', 'png', 'bmp');

                    if (isset($extension) && in_array($extension, $extension_img)) {
                        $iconView = '<i class="fas fa-eye fa-xs btn-edit' . $i . '"></i>';
                    } else {
                        $iconView = '<i class="fas fa-pencil-alt fa-xs btn-edit' . $i . '"></i>';
                    }
                    echo '<li class="file'.$i.' clear"><form action="" method="POST"><input type="hidden" name="cheminFile" value="' . $value . '" /><button class="btn btn-delete btn-xs" type="submit" name="submit"><i class="fa fa-times fa-xs"></i></button></form>&nbsp;&nbsp;<i class="far fa-file-excel"></i>&nbsp;<a href ="?file=' . $value . '">' . $iconView . '</a> ' . $valueName . '</li>';
                }
            }
            echo '</ul>';
        }


        //init
        $formAccess = 0;

        if (isset($_GET['file'])) {
            $filePath = $_GET['file'];
            $fileNameExtension = basename($filePath);

            $extension_fichier = pathinfo(($fileNameExtension), PATHINFO_EXTENSION);
            $extension_valides = array('txt', 'html');

            if (in_array($extension_fichier, $extension_valides)) {
                //$fileEdit = "files/roswell/col-duboses.txt";
                $contentFile = file_get_contents($filePath);
                $formAccess = 1; //affichage du formulaire de modif
            } else  {
                $extension_valides_img = array('jpg', 'png', 'bmp');
                if (in_array($extension_fichier, $extension_valides_img)) {
                    $imgDisplay = $filePath;
                    $imgAlt = $fileNameExtension;
                    $formAccess = 2; //affichage de l image et non le formulaire
                }
            }
        }

        ?>
    </div>

    <div id="content">
        <hr>
        <?php if($formAccess == 1) { ?>
        <strong>Modification du fichier</strong>
        <form action = "" method="POST">
            <textarea name="content" class="textareaContent"><?= $contentFile ?></textarea>
            <input type="submit" value="Valider" class="btn btn-default"/>
        </form>
        <?php } else if ($formAccess == 2) { ?>
            <strong>Image sélectionnée</strong>
            <img class="img-responsive" src="<?= $imgDisplay ?? null ?>" alt="<?= $imgAlt ?? null ?>" width="460" height="345">
        <?php } ?>
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