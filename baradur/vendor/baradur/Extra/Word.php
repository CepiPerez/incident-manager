<?php 
/** 
 * Convert HTML to MS Word document 
 * @name HTML_TO_DOC 
 * @version 2.0 
 * @author CodexWorld 
 * @link https://www.codexworld.com 
 */ 
class Word 
{ 
    var $docFile  = ''; 
    var $title    = ''; 
    var $htmlHead = ''; 
    var $htmlBody = ''; 

    public static function download($filename, $view)
    {
        return self::generate($filename, $view);
    }


    private static function generate($filename, $view)
    {
        $folder = _DIR_ . env('WORD_PATH') . '/';
        $folder = str_replace('//', '/', $folder);
        
        @unlink($folder.$filename.'.html');
        file_put_contents($folder.$filename.'.html', $view);
        chmod($folder.$filename.'.html', 0777);

        header("Content-Type: application/vnd.ms-word");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("content-disposition: attachment;filename=$filename.doc");
        @readfile($folder.$filename.'.html');

        @unlink($folder.$filename.'.html');
        __exit();

    }

     
    /** 
     * Constructor 
     * 
     * @return void 
     */ 
    public function __construct()
    { 
        $this->title = ''; 
        $this->htmlHead = ''; 
        $this->htmlBody = ''; 
    } 
     
    /** 
     * Set the document file name 
     * 
     * @param string $docfile  
     */ 
    private function setDocFileName($docfile)
    { 
        $this->docFile = $docfile; 
        if(!preg_match("/\.doc$/i",$this->docFile) && !preg_match("/\.docx$/i",$this->docFile)){ 
            $this->docFile .= '.doc'; 
        } 
        return;  
    } 
     
    /** 
     * Set the document title 
     * 
     * @param string $title  
     */ 
    private function setTitle($title){ 
        $this->title = $title; 
    } 
     
    /** 
     * Return header of MS Doc 
     * 
     * @return string 
     */ 
    private function getHeader()
    { 
        $return = <<<EOH
<html xmlns:v="urn:schemas-microsoft-com:vml" 
xmlns:o="urn:schemas-microsoft-com:office:office" 
xmlns:w="urn:schemas-microsoft-com:office:word" 
xmlns="http://www.w3.org/TR/REC-html40"> 
    
<head> 
<meta http-equiv=Content-Type content="text/html; charset=utf-8"> 
<meta name=ProgId content=Word.Document> 
<meta name=Generator content="Microsoft Word 9"> 
<meta name=Originator content="Microsoft Word 9"> 
<!--[if !mso]> 
<style> 
v\:* {behavior:url(#default#VML);} 
o\:* {behavior:url(#default#VML);} 
w\:* {behavior:url(#default#VML);} 
.shape {behavior:url(#default#VML);} 
</style> 
<![endif]--> 
<title>$this->title</title> 
<!--[if gte mso 9]><xml> 
    <w:WordDocument> 
    <w:View>Print</w:View> 
    <w:DoNotHyphenateCaps/> 
    <w:PunctuationKerning/> 
    <w:DrawingGridHorizontalSpacing>9.35 pt</w:DrawingGridHorizontalSpacing> 
    <w:DrawingGridVerticalSpacing>9.35 pt</w:DrawingGridVerticalSpacing> 
    </w:WordDocument> 
</xml><![endif]--> 
<style> 
<!-- 
    /* Font Definitions */ 
@font-face 
    {font-family:Verdana; 
    panose-1:2 11 6 4 3 5 4 4 2 4; 
    mso-font-charset:0; 
    mso-generic-font-family:swiss; 
    mso-font-pitch:variable; 
    mso-font-signature:536871559 0 0 0 415 0;} 
    /* Style Definitions */ 
p.MsoNormal, li.MsoNormal, div.MsoNormal 
    {mso-style-parent:""; 
    margin:0in; 
    margin-bottom:.0001pt; 
    mso-pagination:widow-orphan; 
    font-size:7.5pt; 
        mso-bidi-font-size:8.0pt; 
    font-family:"Verdana"; 
    mso-fareast-font-family:"Verdana";} 
p.small 
    {mso-style-parent:""; 
    margin:0in; 
    margin-bottom:.0001pt; 
    mso-pagination:widow-orphan; 
    font-size:1.0pt; 
        mso-bidi-font-size:1.0pt; 
    font-family:"Verdana"; 
    mso-fareast-font-family:"Verdana";} 
@page Section1 
    {size:8.5in 11.0in; 
    margin:1.0in 1.25in 1.0in 1.25in; 
    mso-header-margin:.5in; 
    mso-footer-margin:.5in; 
    mso-paper-source:0;} 
div.Section1 
    {page:Section1;} 
--> 
</style> 
<!--[if gte mso 9]><xml> 
    <o:shapedefaults v:ext="edit" spidmax="1032"> 
    <o:colormenu v:ext="edit" strokecolor="none"/> 
    </o:shapedefaults></xml><![endif]--><!--[if gte mso 9]><xml> 
    <o:shapelayout v:ext="edit"> 
    <o:idmap v:ext="edit" data="1"/> 
    </o:shapelayout></xml><![endif]--> 
    $this->htmlHead 
</head> 
<body>
EOH;

        return $return; 
    } 
     
    /** 
     * Return Document footer 
     * 
     * @return string 
     */ 
    private function getFotter()
    { 
        return "</body></html>"; 
    } 
 
    private function createDoc($html, $file, $download = false)
    { 
        if(is_file($html)){ 
            $html = @file_get_contents($html); 
        } 
         
        $this->_parseHtml($html); 
        //$this->setDocFileName($file); 
        $doc = $this->getHeader(); 
        $doc .= $this->htmlBody; 
        $doc .= $this->getFotter(); 
                         
        if($download) { 
            @header("Cache-Control: ");// leave blank to avoid IE errors 
            @header("Pragma: ");// leave blank to avoid IE errors 
            @header("Content-type: application/octet-stream"); 
            @header("Content-Disposition: attachment; filename=\"$file\""); 
            echo $doc; 
            return true; 
        } else { 
            return $this->write_file($this->docFile, $doc); 
        } 
    } 
    
    private function _parseHtml($html)
    { 
        $html = preg_replace("/<!DOCTYPE((.|\n)*?)>/ims", "", $html); 
        $html = preg_replace("/<script((.|\n)*?)>((.|\n)*?)<\/script>/ims", "", $html); 
        preg_match("/<head>((.|\n)*?)<\/head>/ims", $html, $matches); 
        $head = !empty($matches[1])?$matches[1]:''; 
        preg_match("/<title>((.|\n)*?)<\/title>/ims", $head, $matches); 
        $this->title = !empty($matches[1])?$matches[1]:''; 
        $html = preg_replace("/<head>((.|\n)*?)<\/head>/ims", "", $html); 
        $head = preg_replace("/<title>((.|\n)*?)<\/title>/ims", "", $head); 
        $head = preg_replace("/<\/?head>/ims", "", $head); 
        $html = preg_replace("/<\/?body((.|\n)*?)>/ims", "", $html); 
        $this->htmlHead = $head; 
        $this->htmlBody = $html; 
        return; 
    } 
     
    private function write_file($file, $content, $mode = "w")
    { 
        $fp = @fopen($file, $mode); 
        if(!is_resource($fp)){ 
            return false; 
        } 
        fwrite($fp, $content); 
        fclose($fp); 
        return true; 
    }
}
