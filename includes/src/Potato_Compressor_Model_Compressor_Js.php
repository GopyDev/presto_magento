<?php
if (!@class_exists('JSMin')) {
    set_include_path( BP.DS.'lib'.DS.'Minify'. PS . get_include_path());
    require_once 'JSMin.php';
}
class Potato_Compressor_Model_Compressor_Js extends Potato_Compressor_Model_Compressor_Abstract
{
    const FILE_EXTENSION         = 'js';

    public function compression($content)
    {
        $jsCompressor = new JSMin('');
        $content = $jsCompressor->minify($content);
        return $content;
    }

    public function makeDefer($response)
    {
        $body = $response->getBody();

        //use defer attribute
        //$body = str_replace('<script ','<script defer ', $body);

        //remove cdata
        $body = preg_replace('#//<!\[CDATA\[(.+?)\//]\]>#s', '$1', $body);

        //getall script tags
        preg_match_all('/<script\b[^>]*>(.*?)<\/script>/is', $body, $matches);

        //inline scripts
        $nonSrcScriptLines = array();
        if (empty($matches)) {
            return $this;
        }
        foreach ($matches[0] as $line) {
            $scriptLine = $line;
            //check ignore
            preg_match('@po_cmp_ignore@', $scriptLine, $match);
            if (!empty($match)) {
                continue;
            }

            //check src attr
            preg_match('@src="([^"]+)"@', $scriptLine, $match);
            if (!empty($match)) {
                $scriptLine = str_replace('<script ','<script defer ', $scriptLine);
                $body = str_replace($line, $scriptLine, $body);
                continue;
            }
            //remove script tag
            $scriptLine = preg_replace('#<script(.*?)>#is', '', $scriptLine);
            $scriptLine = preg_replace('#</script>#is', '', $scriptLine);

            $nonSrcScriptLines[] = $scriptLine;
            //remove script from body
            $body = str_replace($line, '', $body);
        }
        if (!empty($nonSrcScriptLines)) {
            $content = '';
            //merge content
            foreach ($nonSrcScriptLines as $scriptLine) {
                $content .= rtrim($scriptLine, ';') . ';';
            }
            //compress content
            $content = $this->compression($content);

            //prepare inline js file
            $fileName = md5($content) . '.js';
            $filePath = Mage::helper('po_compressor')->getRootCachePath() . DS . Mage::app()->getStore()->getId() . DS . 'js';
            if (!file_exists($filePath . DS . $fileName)) {
                file_put_contents($filePath . DS . $fileName, $content);
            }
            $baseMediaUrl = Mage::helper('po_compressor')->getRootCacheUrl() . '/' . Mage::app()->getStore()->getId() . '/' . 'js' . '/';
            $body = str_replace('</body>', '<script defer src="' . $baseMediaUrl . $fileName . '"></script></body>', $body);
            $response->setBody($body);
        }
        return $this;
    }
}