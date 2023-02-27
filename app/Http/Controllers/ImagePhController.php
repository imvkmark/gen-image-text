<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Classes\PlainGenerator;
use Illuminate\Http\Response;
use Request;

/**
 * 占位符
 */
class ImagePhController extends Controller
{

    /**
     * @param string $text
     * @return Response
     */
    public function generate($text = '')
    {

        $fc = '#' . Request::input('_fc', 'EAE0D0');
        return (new PlainGenerator())->gen($text, $fc)->response('png');
    }
}
