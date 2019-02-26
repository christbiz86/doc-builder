<?php

namespace App\Http\Handlers\DocumentBuilder;

use PhpOffice\PhpWord\TemplateProcessor;

class EssentialTemplateProcessor extends TemplateProcessor {

    protected $_rels;
    protected $_types;
    public $tempDocumentMainPart;
    
    public function __construct($documentTemplate)
    {
        parent::__construct($documentTemplate);
        $this->_countRels=100; 
    }

    public function setImg( $strKey, $img){
        $strKey = '${'.$strKey.'}';
        $relationTmpl = '<Relationship Id="RID" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/IMG"/>';
        $imgTmpl = '<w:pict><v:shape type="#_x0000_t75" style="width:WIDpx;height:HEIpx;position:absolute;margin-top:-20px;margin-left:MARLEFTpx;"><v:imagedata r:id="RID" o:title=""/></v:shape></w:pict>';
        $toAdd = $toAddImg = $toAddType = '';
        $aSearch = array('RID', 'IMG');
        $aSearchType = array('IMG', 'EXT');
        $countrels=$this->_countRels++;
        //I'm work for jpg files, if you are working with other images types -> Write conditions here
        $imgExt = 'jpg';
        $imgName = 'img' . $countrels . '.' . $imgExt;
        $this->zipClass->deleteName('word/media/' . $imgName);
        $this->zipClass->addFile($img['src'], 'word/media/' . $imgName);
        $typeTmpl = '<Override PartName="/word/media/'.$imgName.'" ContentType="image/EXT"/>';
        $rid = 'rId' . $countrels;
        $countrels++;
        if($img['src']!=''){
            list($w,$h) = getimagesize($img['src']);
            if(isset($img['swh'])) //Image proportionally larger side
            {
                if($w<=$h)
                {
                    $ht=(int)$img['swh'];
                    $ot=$w/$h;
                    $wh=(int)$img['swh']*$ot;
                    $wh=round($wh);
                }
                if($w>=$h)
                {
                    $wh=(int)$img['swh'];
                    $ot=$h/$w;
                    $ht=(int)$img['swh']*$ot;
                    $ht=round($ht);
                }
                $w=$wh;
                $h=$ht;
            }
            if(isset($img['size']))
            {
                $w = $img['size'][0];
                $h = $img['size'][1];           
                $marleft = $img['size'][2];
            }
            $toAddImg .= str_replace(array('RID', 'WID', 'HEI', 'MARLEFT'), array($rid, $w, $h, $marleft), $imgTmpl) ;
            if(isset($img['dataImg']))
            {
                $toAddImg.='<w:br/><w:t>'.$this->limpiarString($img['dataImg']).'</w:t><w:br/>';
            }
            $aReplace = array($imgName, $imgExt);
            $toAddType .= str_replace($aSearchType, $aReplace, $typeTmpl) ;
            $aReplace = array($rid, $imgName);
            $toAdd .= str_replace($aSearch, $aReplace, $relationTmpl);
            $this->tempDocumentMainPart=$this->str_replace2('<w:t>' . $strKey . '</w:t>', $toAddImg, $this->tempDocumentMainPart,1);
            if($this->_rels=="")
            {
                $this->_rels=$this->zipClass->getFromName('word/_rels/document.xml.rels');
                $this->_types=$this->zipClass->getFromName('[Content_Types].xml');
            }
            $this->_types = $this->str_replace2('</Types>', $toAddType, $this->_types,1) . '</Types>';
            $this->_rels = $this->str_replace2('</Relationships>', $toAdd, $this->_rels,1) . '</Relationships>';
        }
    }
    
    function limpiarString($str) {
        return str_replace(
            array('&', '<', '>', "\n"), 
            array('&amp;', '&lt;', '&gt;', "\n" . '<w:br/>'), 
            $str
        );
    }
    
    public function save()
    {
        foreach ($this->tempDocumentHeaders as $index => $xml) {
            $this->zipClass->addFromString($this->getHeaderName($index), $xml);
        }

        $this->zipClass->addFromString($this->getMainPartName(), $this->tempDocumentMainPart);
        
        if($this->_rels!=""){
            $this->zipClass->addFromString('word/_rels/document.xml.rels', $this->_rels);
        }
        if($this->_types!=""){
            $this->zipClass->addFromString('[Content_Types].xml', $this->_types);
        }

        foreach ($this->tempDocumentFooters as $index => $xml) {
            $this->zipClass->addFromString($this->getFooterName($index), $xml);
        }

        // Close zip file
        if (false === $this->zipClass->close()) {
            throw new Exception('Could not close zip file.');
        }

        return $this->tempDocumentFilename;
    }

    function str_replace2($find, $replacement, $subject, $limit = 0){
        if ($limit == 0)
          return str_replace($find, $replacement, $subject);
        $ptn = '/' . preg_quote($find,'/') . '/';
        return preg_replace($ptn, $replacement, $subject, $limit);
    }

    public function cloneBlockString($blockname, $clones = 1, $replace = true){
        $cloneXML = '';
        $replaceXML = null;
        // location of blockname open tag
        $startPosition = strpos($this->tempDocumentMainPart, '${' . $blockname . '}');
        if ($startPosition) {
            // start position of area to be replaced, this is from the start of the <w:p before the blockname
            $startReplacePosition = strrpos($this->tempDocumentMainPart, '<w:p',
                -(strlen($this->tempDocumentMainPart) - $startPosition));
            // start position of text we're going to clone, from after the </w:p> after the blockname
            $startClonePosition = strpos($this->tempDocumentMainPart, '</w:p>', $startPosition) + strlen('</w:p>');
            // location of the blockname close tag
            $endPosition = strpos($this->tempDocumentMainPart, '${/' . $blockname . '}');
            if ($endPosition) {
                // end position of the area to be replaced, to the end of the </w:p> after the close blockname
                $endReplace = strpos($this->tempDocumentMainPart, '</w:p>', $endPosition) + strlen('</w:p>');
                // end position of the text we're cloning, from the start of the <w:p before the close blockname
                $endClone = strrpos($this->tempDocumentMainPart, '<w:p',
                    -(strlen($this->tempDocumentMainPart) - $endPosition));
                $cloneLength = ($endClone - $startClonePosition);
                $replaceLength = ($endReplace - $startReplacePosition);
                $cloneXML = substr($this->tempDocumentMainPart, $startClonePosition, $cloneLength);
                $replaceXML = substr($this->tempDocumentMainPart, $startReplacePosition, $replaceLength);
            }
        }
        if ($replaceXML != null) {
            $cloned = array();
            for ($i = 1; $i <= $clones; $i++) {
                $cloned[] = $cloneXML;
            }
            if ($replace) {
                $this->tempDocumentMainPart = str_replace($replaceXML, implode('', $cloned),
                    $this->tempDocumentMainPart);
            }
        }
        return $cloneXML;
    }

}