<?php

namespace App\Enums\Catalog;

enum ItemImageType: string
{
    case COVER = 'cover';
    case GALLERY = 'gallery';
    case QRCODE = 'qrcode';
}
