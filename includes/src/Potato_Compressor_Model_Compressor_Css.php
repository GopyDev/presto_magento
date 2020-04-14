<?php

set_include_path(BP . DS . 'lib' . DS . 'Minify' . PS . get_include_path());
require_once 'Minify' . DS . 'CSS.php';
class Potato_Compressor_Model_Compressor_Css extends Potato_Compressor_Model_Compressor_Abstract
{
    const FILE_EXTENSION         = 'css';

    public function compression($content)
    {
        $cssCompressor = new Minify_CSS;
        $content = $cssCompressor->minify($this->removeComments($content));
        $content = str_replace(" }", "}", $content);
        $content = str_replace("} ", "}", $content);
        $content = str_replace(": ", ":", $content);
        $content = str_replace(" :", ":", $content);
        $content = str_replace("  ", " ", $content);
        return $content;
    }

    public function removeComments($content)
    {
        //remove comments
        $content = preg_replace('!/\*.*?\*/!s', '', $content);
        $content = preg_replace('/\n\s*\n/', "\n", $content);
        return $content;
    }
}