<?php
/**
 * Activo Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Activo Commercial License
 * that is available through the world-wide-web at this URL:
 * http://extensions.activo.com/license_professional
 *
 * @copyright   Copyright (c) 2013 Activo Extensions (http://extensions.activo.com)
 * @license     Commercial
 */

/**
 * Bulk Images import model
 *
 * @category    Activo
 * @package     Activo_BulkImages
 */
class Activo_BulkImages_Model_Import extends Mage_Core_Model_Abstract
{
    protected $_separator        = null;
    protected $_advanced_logging = false;
    protected $_products         = array();
    protected $_files            = array();
    protected $_image_extensions = array('jpg', 'png', 'gif');

    const PATH_LOG_FILE          = 'activo_bulkimages.log';

    const CPATH_SOURCE_FOLDER   = 'activo_bulkimages/global/sourcefolder';
    const CPATH_SUBFOLDERS      = 'activo_bulkimages/global/subfolders';
    const CPATH_SEPARATOR       = 'activo_bulkimages/global/separator';
    const CPATH_EXCLUDE_FIRST   = 'activo_bulkimages/global/excludefirst';
    const CPATH_DELETE_OLD      = 'activo_bulkimages/global/deleteold';
    const CPATH_FILTER_OPTIONS  = 'activo_bulkimages/global/filteroptions';
    const CPATH_ATTACH_SIMPLE   = 'activo_bulkimages/global/attachtosimple';
    const CPATH_LOGGING         = 'activo_bulkimages/global/logging';
    const CPATH_REGEX_PATTERN   = 'activo_bulkimages/regex/regexpattern';
    const CPATH_REGEX_REPLACE   = 'activo_bulkimages/regex/regexreplace';
    const CPATH_SEO_FILENAME    = 'activo_bulkimages/seo/newfilename';

    const FILTER_OPTIONS_ALL                    = 1;
    const FILTER_OPTIONS_VISIBLE_SEARCH_CATALOG = 2;
    const FILTER_OPTIONS_VISIBLE_SEARCH         = 3;
    const FILTER_OPTIONS_VISIBLE_CATALOG        = 4;
    const FILENAME_OPTIONS_NOCHANGE             = 1;
    const FILENAME_OPTIONS_NAME                 = 2;
    const FILENAME_OPTIONS_NAME_SKU             = 3;
    const FILENAME_OPTIONS_SKU_NAME             = 4;

    protected function _construct()
    {
        $this->_init('bulkimages/import');
        $this->_advanced_logging = Mage::getStoreConfig(self::CPATH_LOGGING);
        $this->_separator        = Mage::getStoreConfig(self::CPATH_SEPARATOR);
    }

    public function processImport($processAllFiles = true)
    {

        if ($this->_advanced_logging) {
            Mage::log('Started bulk images import process...', null, self::PATH_LOG_FILE);
        }

        // Get running parameters, last import date and recentImportId

        // Make sure we clean directory separators from the base media folder
        // and the import folder as users may or may not enter training slashes properly...
        $importFolder     = rtrim(Mage::getBaseDir('media'), DS)
            . DS . ltrim(Mage::getStoreConfig(self::CPATH_SOURCE_FOLDER), DS);
        $recentImportTime = self::_getRecentImportTime();
        $newImportIdNum   = (string)(self::_getRecentImportId() + 1);
        $newImportId      = '.' . $newImportIdNum;
        $helper           = Mage::helper('bulkimages');
        $numImages        = 0;
        $numSkuMatches    = 0;
        $numImageMatches  = 0;

        if ($this->_advanced_logging) {
            Mage::log('Import folder: ' . $importFolder, null, self::PATH_LOG_FILE);
            Mage::log('Last import time: ' . date('c', $recentImportTime), null, self::PATH_LOG_FILE);
            Mage::log('New import ID: ' . $newImportIdNum, null, self::PATH_LOG_FILE);
        }

        // Build an array of available image files and matching SKUs
        if (!is_dir($importFolder))
        {
            //show an error message: please correct dir path.
            Mage::getSingleton('adminhtml/session')->addError($helper->__('Error: the entered import folder is invalid.'));
            if ($this->_advanced_logging) {
                Mage::log('Error: the import folder does not exist: ' . $importFolder, null, self::PATH_LOG_FILE);
            }
        }
        else
        {
            if ($this->_advanced_logging) {
                Mage::log('Searching for available image files. Import folder: ' . $importFolder, null, self::PATH_LOG_FILE);
            }

            // Check if we need to search directory recursively or only one level directory
            $recursive = Mage::getStoreConfig(self::CPATH_SUBFOLDERS);
            if ($recursive) {
                $di = new RecursiveDirectoryIterator($importFolder);
                $iterator = new RecursiveIteratorIterator($di);
                if ($this->_advanced_logging) {
                    Mage::log('Also including subfolders...', null, self::PATH_LOG_FILE);
                }
            } else {
                $di = new DirectoryIterator($importFolder);
                $iterator = new IteratorIterator($di);
            }

            foreach ($iterator as $ff => $file) {
                $filename = $file->getPathname();

                // ignore file it is not one of the supported image extensions
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if (!in_array(strtolower($ext), $this->_image_extensions)) continue;

                $fname = $file->getFilename();
                $fbase = $file->getBasename('.' . $ext);

                $spos = stripos($fbase, $this->_separator);
                if (false === $spos) continue;  // ignore image file if it does not have a separator string

                $sku = substr($fbase, 0, $spos);
                $num = substr($fbase, $spos + strlen($this->_separator));

                if ($sku !== false)
                {
                    $numImages++;

                    $this->_files[$sku][$num]['file']    = $fname;
                    $this->_files[$sku][$num]['srcfile'] = $filename;

                    if ($processAllFiles || ($file->getMTime() > $recentImportTime) || ($file->getCTime() > $recentImportTime))
                    {
                        $this->_files[$sku]['process'] = true;
                        $proc = 'import';
                    } else {
                        $proc = 'old file, ignore';
                    }

                    if ($this->_advanced_logging) {
                        Mage::log(
                            sprintf('Found image file: %s -> sku: %s, sort: %s (%s)', $filename, $sku, $num, $proc),
                            null, self::PATH_LOG_FILE);
                    }

                }

            }

            // get array of visible products with: sku, runway_brand, gallery images
            $arrayAttrs = array('name', 'image', 'small_image', 'thumbnail');

            //Default: ALL Products
            // No need for addStoreFilter(...) since we will iterate through all stores
            // the product belongs to...
            $pCollection = Mage::getModel('catalog/product')->getCollection()
                    //->addStoreFilter(Mage_Core_Model_App::DISTRO_STORE_ID)
                    ->addAttributeToSelect('sku')
                    ->addAttributeToSelect('entity_type_id')
                    ->addAttributeToSelect($arrayAttrs, 'left');

            switch (Mage::getStoreConfig(self::CPATH_FILTER_OPTIONS)) {
               case self::FILTER_OPTIONS_VISIBLE_SEARCH_CATALOG:
                    $pCollection->addAttributeToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
                    break;
               case self::FILTER_OPTIONS_VISIBLE_SEARCH:
                    $pCollection->addAttributeToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH);
                    break;
               case self::FILTER_OPTIONS_VISIBLE_CATALOG:
                    $pCollection->addAttributeToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG);
                    break;
               case self::FILTER_OPTIONS_ALL:
               default:
                    break;
            }

            // Build $this->_products array by walking through the catalog and applying the RegEx if necessary
            if ($this->_advanced_logging) {
                Mage::log('Searching for available product SKUs... ', null, self::PATH_LOG_FILE);
            }
            Mage::getSingleton('core/resource_iterator')->walk($pCollection->getSelect(), array(array($this, 'productCallback')), array('arg1' => '===='));

            $productsArray = $this->_products;
            unset($pCollection);

            //io object
            $io = new Varien_Io_File();
            $io->setAllowCreateFolders(true);

            // loop through files, if file name matches a product, do:
            if ($this->_advanced_logging) {
                Mage::log('Checking files for SKU matches...', null, self::PATH_LOG_FILE);
            }
            foreach ($this->_files as $sku => $fileArray)
            {
                if (isset($fileArray['process']))
                {
                    unset($fileArray['process']);

                    // if filename matches product sku
                    if (isset($productsArray[$sku]))
                    {
                        $numSkuMatches++;
                        //The product object
                        $a = $productsArray[$sku];
                        if ($this->_advanced_logging) {
                            Mage::log(
                                sprintf('Matched image to SKU: %s', $sku),
                                null, self::PATH_LOG_FILE);
                        }

                        $p = Mage::getSingleton('catalog/product');
                        /* @var $p Mage_Catalog_Model_Product */

                        foreach($a as $pId)
                        {
                            $p->reset()->load($pId);

                            //The array of old image files for this sku
                            $imagesOld = self::_getImageFilesArray($p);

                            if (Mage::getStoreConfig(self::CPATH_DELETE_OLD)) {
                                if ($this->_advanced_logging) {
                                    Mage::log(' - removing old images for SKU: ' . $p->getSku(), null, self::PATH_LOG_FILE);
                                }

                                //remove old image files
                                foreach ($imagesOld as $image)
                                {
                                    $imagePath = $this->_getMediaConfig()->getMediaPath($image);
                                    $io->rm($imagePath);
                                    if ($this->_advanced_logging) {
                                        Mage::log(' - removed image: ' . $imagePath, null, self::PATH_LOG_FILE);
                                    }
                                }

                                //delete media gallery db records for product ID
                                $this->getResource()->deleteMediaGalleryByProductId($p->getId());

                                // Start new image import number from 1
                                $n = 1;
                            } else {
                                $n = count($imagesOld) + 1;
                            }

                            //sort filesArray
                            ksort($fileArray);
                            foreach ($fileArray as $f)
                            {
                                $numImageMatches++;

                                $newfile = $this->_getNewFileName($f['file'], $newImportId, $p);
                                $newpath = $this->_getNewFilePath(substr($newfile,5));

                                $mediapath = $this->_getMediaConfig()->getMediaPath($newfile);
                                if ($this->_advanced_logging) {
                                    Mage::log(' - importing image file: ' . $f['srcfile'] . '  ->  ' . $mediapath, null, self::PATH_LOG_FILE);
                                }

                                $io->open(array('path' => $newpath));
                                $io->cp($f['srcfile'], $mediapath);

                                //enter value in DB
                                $this->getResource()->addImage(
                                        $newfile,
                                        $p,
                                        $n,
                                        Mage::getStoreConfig(self::CPATH_EXCLUDE_FIRST));

                                //If option enabled in config AND isConfigurable AND first image
                                if (Mage::getStoreConfig(self::CPATH_ATTACH_SIMPLE) &&
                                    $p->isConfigurable() &&
                                    $n == 1)
                                {
                                    $assocProducts = $p->getTypeInstance()->getUsedProducts();
                                    foreach ($assocProducts as $aProduct)
                                    {
                                        //delete media gallery db records for product ID
                                        $this->getResource()->deleteMediaGalleryByProductId($p->getId());

                                        //associate the first image only
                                        $this->getResource()->addImage(
                                                $newfile,
                                                $aProduct,
                                                $n,
                                                Mage::getStoreConfig(self::CPATH_EXCLUDE_FIRST));
                                    }
                                }

                                //increment local counter of position
                                $n++;
                            }
                        }
                    }
                }
            }

            //Collect and save stats
            $import = Mage::getModel('bulkimages/import');
            $import->setNumImages($numImages)
                ->setNumImagesUnmatched($numImages - $numImageMatches)
                ->setNumSkus(count($this->_products))
                ->setNumSkusUnmatched(count($this->_products) - $numSkuMatches)
                ->setNumMatches($numSkuMatches)
                ->setCreatedAt(now())
                ->setSuccessfull(1)
                ->save();

            //Show success message
            Mage::getSingleton('adminhtml/session')->addSuccess($helper->__('Successfully imported '.$numImageMatches.' images.'));
            if ($this->_advanced_logging) {
                Mage::log('Successfully imported '.$numImageMatches.' images for ' . $numSkuMatches . ' product SKUs.', null, self::PATH_LOG_FILE);
            }
        }

        if ($this->_advanced_logging) {
            Mage::log('Finished bulk images import process...', null, self::PATH_LOG_FILE);
            Mage::log('-----' . PHP_EOL, null, self::PATH_LOG_FILE);
        }
    }

    function productCallback($args)
    {
//        $product = Mage::getModel('catalog/product');
//        $product->setData($args['row']);

        $sku = $args['row']['sku'];

        $regexPattern='';
        $regexReplace='';
        if (Mage::getStoreConfig(self::CPATH_REGEX_PATTERN)!='')
        {
            $regexPattern = Mage::getStoreConfig(self::CPATH_REGEX_PATTERN);
            $regexReplace = Mage::getStoreConfig(self::CPATH_REGEX_REPLACE);
        }

        if ($this->_advanced_logging) {
            Mage::log('Found product SKU: ' . $sku, null, self::PATH_LOG_FILE);
        }

        $sku_new = $sku;
        if (!empty($regexPattern))
        {
            $sku_new = preg_replace($regexPattern, $regexReplace, $sku);
            if ($this->_advanced_logging) {
                Mage::log('Applied RexEx pattern to SKU: ' . $sku . '  ->  ' . $sku_new, null, self::PATH_LOG_FILE);
            }
        }

        if (!isset($this->_products[$sku_new])) $this->_products[$sku_new] = array();

        $this->_products[$sku_new][] = $args['row']['entity_id'];
    }

    protected function _getNewFileName($file, $newImportId, $product)
    {
    	// Maximum image file length -- has to be lesser that 250 characters reserved for the record in DB
    	$maxsize   = 230;
    	$extension = substr($file, stripos($file, $this->_getSeparator())+strlen($this->_getSeparator()));
    	$maxsize  -= strlen($extension);

        switch (Mage::getStoreConfig(self::CPATH_SEO_FILENAME)) {
           case self::FILENAME_OPTIONS_NAME:
                $newfile = $product->getName() . '-';
                $newfile = substr($newfile, 0, $maxsize) . $extension;
                break;

           case self::FILENAME_OPTIONS_NAME_SKU:
                $newfile = $product->getName() . '-' . $product->getSku() . '-';
                $newfile = substr($newfile, 0, $maxsize) . $extension;
                break;

           case self::FILENAME_OPTIONS_SKU_NAME:
                $newfile = $product->getSku() . '-' . $product->getName() . '-';
                $newfile = substr($newfile, 0, $maxsize) . $extension;
                break;

           case self::FILENAME_OPTIONS_NOCHANGE:
           default:
                $newfile = $file;
                break;
        }

        $newfile = $this->sanitize_file_name($newfile);

        $endFilename = $this->_getEndFilenamePosition($newfile);
        $newfile = '/'.substr($newfile,0,1).'/'.substr($newfile,1,1).'/'.substr_replace($newfile, $newImportId, $endFilename, 0);
        $newfile = strtolower($newfile);

        return $newfile;
    }

    protected function _getNewFilePath($file)
    {
        $newpath = $this->_getMediaConfig()->getBaseMediaPath().'/'.substr($file,0,1).'/'.substr($file,1,1).'/';
        $newpath = strtolower($newpath);

        return $newpath;
    }

    protected function sanitize_file_name( $filename )
    {
        $filename_raw = $filename;
        $special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", "%");
        $filename = str_replace($special_chars, '', $filename);
        $filename = preg_replace('/[\s-]+/', '-', $filename);
        $filename = trim($filename, '.-_');
        return $filename;
    }


    protected function _getEndFilenamePosition($file)
    {
        $imageExtensions = array('.jpg', '.png', '.gif');
        $pos = false;

        foreach ($imageExtensions as $imageExtension)
        {
            $pos = stripos($file, $imageExtension);
            if ($pos !== false)
                break;
        }

        return $pos;
    }

    protected function _getMediaConfig()
    {
        return Mage::getSingleton('catalog/product_media_config');
    }


    protected function _getImageFilesArray($product)
    {
        $imageFilesArray = array();
        $heystack = '';

        if ($product->getImage() != '')
        {
            $imageFilesArray[] = $product->getImage();
            $heystack = $product->getImage();
        }
        if ($product->getSmallImage() != '' && stripos($heystack, $product->getSmallImage()) === FALSE )
        {
            $imageFilesArray[] = $product->getSmallImage();
            $heystack .= $product->getSmallImage();
        }
        if ($product->getThumbnail() != '' && stripos($heystack, $product->getThumbnail()) === FALSE )
        {
            $imageFilesArray[] = $product->getThumbnail();
            $heystack .= $product->getThumbnail();
        }
        //get all media gallery images
        if ($product->getMediaGalleryImages() != NULL)
        {
            foreach ($product->getMediaGalleryImages() as $image)
            {
                if (stripos($heystack, $image->getFile()) === FALSE)
                {
                    $imageFilesArray[] = $image->getFile();
                    $heystack .= $image->getFile();
                }
            }
        }

        return $imageFilesArray;
    }

    protected function _getSkuFromFilename( $filename )
    {
        $endPos = stripos( $filename, $this->_getSeparator() );

        if ($endPos === FALSE)
        {
            return false;
        }
        else
        {
            return substr($filename, 0, $endPos);
        }
    }

    protected function _getNumFromFilename( $filename )
    {
        $endPos = stripos( $filename, $this->_getSeparator() );

        if ($endPos === FALSE)
        {
            return false;
        }
        else
        {
            return substr($filename, $endPos + strlen($this->_getSeparator()), -4);
        }
    }

    protected function _getRecentImportTime()
    {
        $lastImport = Mage::getResourceModel('bulkimages/import_collection')->getFirstItem();

        if (is_null($lastImport))
        {
            return 0;
        }
        else
        {
            return strtotime($lastImport->getCreatedAt());
        }
    }

    protected function _getRecentImportId()
    {
        $lastImport = Mage::getResourceModel('bulkimages/import_collection')->getFirstItem();

        if (is_null($lastImport))
        {
            return 0;
        }
        else
        {
            return $lastImport->getId();
        }
    }

    protected function _getSeparator()
    {
        if ($this->_separator == null)
        {
            $this->_separator = Mage::getStoreConfig(self::CPATH_SEPARATOR);
        }

        return $this->_separator;
    }
}
