<?php

namespace Tests\DamianPhp\Support\String;

use Tests\BaseTest;
use DamianPhp\Support\String\Media;

class MediaTest extends BaseTest
{
    public function testMedia(): void
    {
        $media = new Media();

        $fakeFileName = 'file-aaa.txt';
        $fileNameWithoutExt = $media->getFileNameWithoutExt($fakeFileName);
        $this->assertSame('file-aaa', $fileNameWithoutExt);

        $fakeImgName = 'logo-aaa';
        $fileNameImgWithExt = $media->getFileNameImgWithExt($fakeImgName);
        $this->assertSame('', $fileNameImgWithExt); // car ce fichier n'est pas présent

        $fakeImgName = 'logo-aaa';
        $srcImgWithExt = $media->getSrcImgWithExt($fakeImgName);
        $this->assertSame('', $srcImgWithExt); // car ce fichier n'est pas présent
    }
}
