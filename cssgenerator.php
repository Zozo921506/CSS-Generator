<?php

// Création image vide Sprite.png
$sprite = 'sprite.png';
$picture = imagecreate(700, 800);
$background = imagecolorallocatealpha($picture, 255, 255, 255, 127);
echo "L'image vide a été crée avec succès \n";
imagealphablending($picture, false);
imagesavealpha($picture, true);

$shortoptions = "i:r:s:"; // Stockage des options courte
$longoptions = ["output-image:", "recursive:", "output-style:"]; // Stockage des options longue
$options = getopt($shortoptions, $longoptions);

// Déterminer le nom du fichier de sortie en fonction des options
if (isset($options['i']) || isset($options["output-image"])) 
{
    $sprite = isset($options['i']) ? $options['i'] : $options["output-image"];
} 
else 
{
    $sprite = 'sprite.png';
}

$x = 0; // Position horizontale initiale
$y = 0; // Position verticale initiale

// Boucle qui permet l'avancement des arguments entrés dans la ligne de commande
for ($i = 1; $i < $argc; $i ++) 
{
    $arg = $argv[$i];
    if ($argc < 2)
    {
        die("J'ai besoin d'au moins 1 image pour fonctionner\n"); // Manque une image
    }

    // Vérification des arguments entrés
    switch($arg)
    {
        case '-i':
        case '--output-image':
            $i++;
            $sprite = pathinfo($sprite, PATHINFO_EXTENSION) === 'png' ? $sprite : $sprite . '.png';// Vérification si extension fini par png sinon le rajoute
            break;

        case '-r':
        case '--recursive':
            $i++;
            pngRecursif($argv[$i], $picture, $x, $y);
            break;

        case '-s':
        case '--output-style':
            $i++;
            $nameCss = isset($argv[$i]) ? $argv[$i] : 'style.css'; // Déterminer le nom du css sortie en fonction des options
            $nameCss = pathinfo($nameCss, PATHINFO_EXTENSION) === 'css' ? $nameCss : $nameCss . '.css'; // Vérification si extension fini par css sinon le rajoute
            cssGenerator($nameCss);
            break;
        default:
        {
            pngIteratif($arg, $picture, $x, $y); // Par défaut appelle de la fonction itérative

            // Ajuste les valeurs de décalage pour la prochaine image
            $x += 175; // Décalage horizontale des images (vers la droite)
            if ($x > 700)
            {
                $x = 0; // Réinilisation de la position horizontale
                $y += 175; // Ajuste le décalage verticale (vers le bas)
            }
            break;
        }
    }
}

// Vérification si un png existe de manière iterative
function pngIteratif($path, $picture, $x, $y)
{
    if (!file_exists($path)) // Vérifie si un fichier ou un dossier existe
    {
        return "Je n'existe pas\n"; // Fichier ou dossier inexistant
    }
    if (is_file($path)) // Vérifie si il s'agit d'un fichier
    {
        $img = imagecreatefrompng($path);
        $extension = substr(strrchr($path, '.'), 1); // Obtention de l'extension du fichier
        if ($extension == "png") 
        {
            $size = getimagesize($path);
            $width = $size[0];
            $height = $size[1];
            imagecopyresized($picture, $img, $x, $y, 0, 0, 175, 175, $width, $height);
        }
        else 
        {
            die("Format invalide\n"); // Pas le bon type de fichier
        }
    }
}

// Vérification si un png existe de manière récursive
function pngRecursif($path, $picture, &$x, &$y)
{
    if (!file_exists($path)) 
    {
        return "Je n'existe pas\n"; // Fichier ou dossier inexistant
    }

    if (is_file($path)) // Vérifie s'il s'agit d'un fichier
    {
        $img = imagecreatefrompng($path);
        $extension = substr(strrchr($path, '.'), 1);
        if ($extension == "png") 
        {
            $size = getimagesize($path);
            $width = $size[0];
            $height = $size[1];
            imagecopyresized($picture, $img, $x, $y, 0, 0, 175, 175, $width, $height);
            $x += 175;

            // Si l'image dépasse la largeur de l'image vierge, passe à la ligne suivante
            if ($x > 700) 
            {
                $x = 0;
                $y += 175;
            }
        } else 
        {
            die("Format invalide\n"); // Pas le bon type de fichier
        }
    }
    if (is_dir($path)) // Vérifie s'il s'agit d'un dossier
    {
        $dir = opendir($path);
        while ($file = readdir($dir)) 
        {
            if ($file != '.' && $file != '..') 
            {
                $filePath = $path . '/' . $file;
                pngRecursif($filePath, $picture, $x, $y);
            }
        }
    closedir($dir);
    }
}

// Génération du fichier CSS
function cssGenerator($style = "style")
{
    $name = fopen($style, "w");
    echo "Le fichier $style a été crée avec succès\n";
}

// Affichage de l'image
$picture_png = imagepng($picture, $sprite);
echo "Le spritesheet $sprite a été créé avec succès\n";
imagedestroy($picture); // Détruit l'image en mémoire de la RAM

// Pour changer la taille des images changer tous les 175 par valeurs souhaité
?>