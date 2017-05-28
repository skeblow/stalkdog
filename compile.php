<?php
/**
 * Created by PhpStorm.
 * User: skeblow
 * Date: 28/02/17
 * Time: 15:45
 */

/*if (!file_exists('build/')) {
    echo 'creating build/';
    mkdir('build/');
}*/

unlink($filename =__DIR__ . '/app.phar');
$phar = new Phar($filename);


phar_add_dir($phar, __DIR__ . '/vendor/');

function phar_add_dir($phar, $dir) {

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir)
    );

    /** @var SplFileInfo[] $iterator */
    foreach ($iterator as $i) {
        if ($i->getPathname() == 'LICENCE' || $i->getExtension() == 'php') {
            $filename = str_replace(__DIR__ . '/', '', $i->getPathname());
            $lower = strtolower($filename);
            //if (strpos($lower, 'test') === false /*&& strpos($lower,'phpunit') === false*/) {
                echo $filename, "\n";
                $phar->addFile($filename);
                $phar[$filename]->compress(Phar::GZ);
           // }
        }
    }
}

$phar->setStub($phar->createDefaultStub( 'vendor/app.php'));

echo "done! ALL!\n\n";
