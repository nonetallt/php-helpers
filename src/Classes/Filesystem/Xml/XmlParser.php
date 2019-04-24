<?php

namespace Nonetallt\Helpers\Filesystem\Xml;

use Nonetallt\Helpers\Filesystem\Xml\Exceptions\XmlParsingException;
use Nonetallt\Helpers\Filesystem\Exceptions\FilesystemException;
use Nonetallt\Helpers\Filesystem\Exceptions\FileNotFoundException;
use Nonetallt\Helpers\Filesystem\Exceptions\TargetNotFileException;
use Nonetallt\Helpers\Filesystem\Exceptions\TargetNotDirectoryException;

/**
 * Simple wrapper class for json_decode and json_encode
 */
class XmlParser
{
    public function __construct()
    {

    }

    /**
     * @throws Nonetallt\Helpers\Filesystem\Xml\Exceptions\XmlParsingException
     */
    public function decodeFile(string $filepath, bool $assoc = false)
    {
        try {
            if(! file_exists($filepath)) throw new FileNotFoundException($filepath);
            if(! is_file($filepath)) throw new TargetNotFileException($filepath);

            $result = file_get_contents($filepath);
            if($result === false) throw new FilesystemException("Error reading file", $filepath);

            return $this->decode($result, $assoc, $depth, $options);
        }
        catch(FilesystemException $e) {
            throw new XmlParsingException("Specified file could not be used for parsing", 0, $e);
        }
    }

    /**
     * @throws Nonetallt\Helpers\Filesystem\Xml\Exceptions\XmlParsingException
     */
    public function encodeIntoFile(\DOMNode $value, string $filepath)
    {
        try {
            if(! file_exists(dirname($filepath))) throw new FileNotFoundException($filepath, 'Parent directory does not exist');
            if(file_exists($filepath) && $overwrite === false) throw new FilesystemException('File already exists', $filepath);
            if(is_dir($filepath)) throw new TargetNotDirectoryException($filepath);

            $simpleXml = $this->encode($value);
            $result = file_put_contents($filepath, $simpleXml->asXml());
            if($result === false) throw new FilesystemException("Error writing to file", $filepath);
        }
        catch(FilesystemException $e) {
            throw new XmlParsingException("Specified file could not be used for parsing", 0, $e);
        }
    }

    /**
     * @throws Nonetallt\Helpers\Filesystem\Xml\Exceptions\XmlParsingException
     */
    public function decode(string $xml, bool $assoc = false)
    {
        libxml_use_internal_errors(true);
        $result = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);

        if($result === false) {
            throw new XmlParsingException();
        }

        if($assoc) {
            $result = dom_import_simplexml($result);
            if($result === false) {
                $msg = "Could not convert SimpleXML element to DOM node";
                throw new XmlParsingException($msg);
            } 
            return $this->domnodeToArray($result);
        }

        libxml_use_internal_errors(false);
        return $result;
    }

    /**
     * @throws Nonetallt\Helpers\Filesystem\Xml\Exceptions\XmlParsingException
     */
    public function encode(\DOMNode $node)
    {
        libxml_use_internal_errors(true);
        $result = simplexml_import_dom($node);

        if($result === false) {
            throw new XmlParsingException();
        }

        libxml_use_internal_errors(false);
        return $result;
    }

    public function domnodeToArray(\DOMNode $node)
    {
        $output = [];
        switch ($node->nodeType) {
        case XML_CDATA_SECTION_NODE:
        case XML_TEXT_NODE:
            $output = trim($node->textContent);
            break;
        case XML_ELEMENT_NODE:
            for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
                $child = $node->childNodes->item($i);
                $v = $this->domnodeToArray($child);
                if(isset($child->tagName)) {
                    $t = $child->tagName;
                    if(!isset($output[$t])) {
                        if(! is_array($output)) continue;
                        $output[$t] = [];
                    }
                    $output[$t][] = $v;
                }
                elseif($v || $v === '0') {
                    $output = (string) $v;
                }
            }
            if($node->attributes->length && !is_array($output)) { //Has attributes but isn't an array
                $output = array('@content'=>$output); //Change output into an array.
            }
            if(is_array($output)) {
                if($node->attributes->length) {
                    $a = array();
                    foreach($node->attributes as $attrName => $attrNode) {
                        $a[$attrName] = (string) $attrNode->value;
                    }
                    $output['@attributes'] = $a;
                }
                foreach ($output as $t => $v) {
                    if(is_array($v) && count($v)==1 && $t!='@attributes') {
                        $output[$t] = $v[0];
                    }
                }
            }
            break;
        }
        return $output;

    }
}
