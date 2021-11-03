<?php
if (isset($_POST["contenu"])) {
    $fichier = $_POST['file'];
    $fichierEnCoursModif = fopen($fichier, "w");
    fwrite($fichierEnCoursModif, $_POST["contenu"]);
    fclose($fichierEnCoursModif);
}


if (isset($_POST['delete'])) {
    if (is_dir($_POST['file'])) {
        deleteDirectory($_POST['file']);
    } else {
        unlink($_POST['file']);
    }
    header('Location: index.php');
}

include('inc/head.php'); ?>


    <html>
    <head>
        <meta charset="utf-8"/>
        <title>Application</title>
    </head>
    </html>

    <body>
    <div class="container-fluid">


        <h1>Liste des fichiers et dossiers</h1>

        <?php


        // Parcourt un dossier et affiche sous forme hiérarchisée
        function parcourtDossier($chemin, $level)
        {

            if (is_dir($chemin)) {
                // c'est un dossier

                // ouvre le dossier
                if ($handle = opendir($chemin)) {

                    // liste de tout ce qu'on va trouver
                    $dirFiles = array();

                    // récupère la liste des fichiers/dossiers
                    while (false !== ($entry = readdir($handle))) {
                        $dirFiles[] = $entry;
                    }

                    // affiche la liste triée
                    foreach ($dirFiles as $entry) {

                        if ($entry != "." && $entry != "..") {

                            if (is_dir($chemin . '/' . $entry)) {

                                // affiche le nom du dossier
                                echo '<b>' . $entry . '</b>';

                                // affiche le contenu du dossier
                                echo '<div style="margin-left:60px; margin-top:4px;">';
                                parcourtDossier($chemin . '/' . $entry, $level + 1);
                                echo '</div>';
                            } else {
                                // $chemin est un un fichier
                                echo '• <a href="?file='
                                    . $chemin . '/' . $entry . ' " >' . $entry . '</a><br/>';
                            }

                        }
                    }
                    closedir($handle);
                }

            } else {
                // $chemin est un fichier
                // utilisé seulement dans le cas où on le paramètre est un nom de fichier
                if ($chemin != "." && $chemin != "..") {
                    echo '<a href="' . $chemin . '">' . $chemin . '</a><br/>';
                }
            }
        }

        parcourtDossier("./files", 0);


        // Fonction effacer :
        function deleteDirectory($chemin)
        {
            if (is_dir($chemin))
                $handle = opendir($chemin);
            if (!$handle) {
                return false;
            }
            while ($entry = readdir($handle)) {
                if ($entry != '.' && $entry != '..') {
                    if (!is_dir($chemin . '/' . $entry)) {
                        unlink($chemin . '/' . $entry);
                    } else {
                        deleteDirectory($chemin . '/' . $entry);
                    }
                }
            }
            closedir($handle);
            rmdir($chemin);
            return true;
        }


        // Formulaire :
        if (isset($_GET['file'])) {
            echo $_GET['file'];
            $lien = $_GET['file'];
            $contenu = file_get_contents($lien);

            ?>

            <form method="POST" action="index.php">

            <textarea name="contenu" style="width:100%; height:200px;">
                <?php echo $contenu; ?>
            </textarea>
                <input type="hidden" name="file" value="<?php echo $_GET['file'] ?>"/>
                <input type="submit" value="Modifier"/>

                <input type="submit" name="delete" value="supprimer"/>
            </form>

        <?php }

        ?>


    </div>
    </body>


<?php include('inc/foot.php'); ?>