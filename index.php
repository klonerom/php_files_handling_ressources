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
                    $chemin = $dir . DIRECTORY_SEPARATOR . $value;
                    //test si dossier alors on relance la fonction
                    if (is_dir($chemin)) {
                        //$chemin = $dir . DIRECTORY_SEPARATOR . $value;
                        //si Get alors suppression folder
                        if (isset($_GET)) {
                            foreach ($_GET as $valueGet) {
                                if ($valueGet == $chemin) {
                                    shell_exec('rm -rf ' . realpath($dir . DIRECTORY_SEPARATOR . $value)); // On efface
                                    header('Location:/');
                                    die;
                                }
                            }
                        }

                        $result[$chemin] = searchFiles($chemin);
                    } else {
                        //si Get alors suppression file
                        if (isset($_GET)) {
                            foreach ($_GET as $valueGet) {
                                if ($valueGet == $chemin) {
                                    unlink($dir . DIRECTORY_SEPARATOR . $value); // On efface
                                    header('Location:/');
                                    die;
                                }
                            }
                        }


                        $result[] = $chemin;
                    }
                }
            }

            return $result;
        }



        function afficher_tablo($tableau, $i = -1) { //tableau contient les chemins des éléments
            $i++;
            echo '<ul class="niv'.$i.'">';
            foreach ($tableau as $key => $value) {

                $keyName = basename($key);//on ne recupere que le nom+format du chemin

                //si une valeur alors on a un sous fichier ou sous dossier
                if (is_array($value)) {

                    echo '<li class="folder'.$i.'"><a href ="?dir=' . $key . '" class="btn btn-default btn-xs">X</a> [' . $keyName . ']</li>';

                    //on relance la fonction pour définir les élements constituant les niveaux inférieurs
                    afficher_tablo($value, $i);

                } else { //pas de niv inférieur, on affiche le resultat
                    $valueName = basename($value);
                    echo '<li class="file'.$i.'"><a href ="?file=' . $value . '" class="btn btn-default btn-xs">X</a> ' . $valueName . '</li>';
                }
            }
            echo '</ul>';
        }

        ?>
    </div>

<script>

     $(document).ready(function(){

        // $('.niv0').click(function() {
        //    $('.niv1').slideToggle( 'slow' );
        //    $('.niv2').slideToggle( 'slow' );
        // });
        //
        // $('.niv2').click(function() {
        //     $('.niv2').slideToggle( 'slow' );
        // });

        // $('.niv0').click(function() {
        //     $('ul.niv1 > li').slideToggle( 'slow' );
        //     $('ul.niv2 > li').slideToggle( 'slow' );
        // });

        // $('.niv1').click(function() {
        //     $('li.file2, li.file3').slideToggle( 'slow' );
        // });

        //
        $('li.folder0').click(function() {
            //$('li.file2').slideUp( 'slow' );
            $(this).next().slideToggle( 'slow' );
        });
         $('li.folder1').click(function() {
             //$('li.file2').slideUp( 'slow' );
             $(this).next().slideToggle( 'slow' );
         });

         // $( 'file1 > li > a' ).on( 'click', function( event ) {
         //     var
         //         element = $( this ),
         //         ul = element.next( "ul" ),
         //         count = ul.length;
         //
         //     $( "file1 > li > ul" ).slideUp();
         //
         //     if( count > 0 && !ul.is( ":visible" ) ) {
         //         ul.slideDown();
         //
         //         event.preventDefault();
         //     } else {
         //         ul.slideUp();
         //     }
         // } );
     });
</script>

<?php include('inc/foot.php'); ?>