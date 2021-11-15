<?php
namespace MQS\lib;

if(!defined('DS')) { define('DS',DIRECTORY_SEPARATOR); }
if(!defined('ROOT')) { define('ROOT',dirname(__FILE__)); }

use \Fpdf\Fpdf;

/**
 * Classe que implementa a montagem de documentos em PDF
 *
 * @author Eduardo Andrade
 **/
class PdfCreator extends Fpdf{

    private $tipo;
    private $entidade;
    private $widths;
    private $aligns;
    
    //variables of html parser
    protected $B;
    protected $I;
    protected $U;
    protected $HREF;
    protected $fontList;
    protected $issetfont;
    protected $issetcolor;

    function __construct($tipo = 'padrao', $entidade){
        $this->tipo = $tipo;
        $this->entidade = $entidade;

        if($tipo == "padrao"){
            //parent::__construct('P', 'mm', 'A4', false, 'UTF-8', false); 
            parent::__construct('P', 'mm', 'A4'); 
        }else{
        	parent::__construct('L', 'mm', 'A4');            		
        }
    }
    
    /**
     * Overwrites the default header
     *
     * @return void
     */
    function Header(){
        switch($this->tipo){
            case "relatorio":
                $this->headerRelatorio();
                break;
            default:
                $this->headerPadrao();
        }
    }

    /**
     * Header padrão 
     *
     * @return void
     */
    function headerPadrao(){
        $this->SetCreator("Grupo MQS. Todos os direitos reservados.");
        $this->SetAuthor("Grupo MQS");
        $this->SetTitle(utf8_decode("Impressão de Inspeção"));
        $this->SetSubject("Brazil Lifting");
        
        // Image(file , abcissa , ordenate, width, height, type)
        $this->Image(ROOT . DS. 'views/images/template_topo.jpg', 0, 0, 210, 50);
        $this->SetY(22);
        $this->SetFont('Times', '', 11);
    }
    
    /**
     * Header para relatório
     *
     * @return void
     */
    function headerRelatorio(){
        $this->SetCreator("Grupo MQS. Todos os direitos reservados.");
        $this->SetAuthor("Grupo MQS");
        $this->SetTitle(utf8_decode("Impressão de Inspeção"));
        $this->SetSubject("Brazil Lifting");
        // Image(file , abcissa , ordenate, width, height, type)
    }
    
    /**
     * Overwrites the default footer
     *
     * @return void
     */
    function Footer(){
        switch($this->tipo){
            case "relatorio":
                $this->footerRelatorio();
                break;
            default:
                $this->footerPadrao();
        }
    }
    

    /**
     * Footer padrão
     *
     * @return void
     */
    function footerPadrao(){
        $this->Image(ROOT . DS. 'views/images/template_base.jpg', 0, 265, 210, 30);
        $this->SetY(-20);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Times', '', 7);
        $texto = utf8_decode('Informações atualizadas até ').date('d/m/Y H:i:s');
        $this->Cell(0,8, $texto." ".utf8_decode('Página ').' de {nb}','T',1,'C');
    }
    
    
    /**
     * Footer padrão paisagem
     *
     * @return void
     */
    function footerRelatorio(){
        $this->SetY(-20);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 7);
        $this->Cell(0,8, utf8_decode('Informações atualizadas até ').date('d/m/Y H:i:s'),'T',1,'C');
        $this->Cell(0, 13, utf8_decode('Página ').$this->PageNo().' de {nb}', 0, 0, 'R');
    }
    

    function SetWidths($w){
        //Set the array of column widths
        $this->widths=$w;
    }
    
    function SetAligns($a){
        //Set the array of column alignments
        $this->aligns=$a;
    }
    
    function Row($data){
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
            $h=5*$nb;
            //Issue a page break first if needed
            $this->CheckPageBreak($h);
            //Draw the cells of the row
            for($i=0;$i<count($data);$i++){
                $w=$this->widths[$i];
                $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                //Save the current position
                $x=$this->GetX();
                $y=$this->GetY();
                //Draw the border
                $this->Rect($x,$y,$w,$h);
                //Print the text
                $this->MultiCell($w,5,$data[$i],0,$a);
                //Put the position to the right of the cell
                $this->SetXY($x+$w,$y);
            }
            //Go to the next line
            $this->Ln($h);
    }
    
    function CheckPageBreak($h){
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }
    
    function NbLines($w,$txt){
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
            $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
            $s=str_replace("\r",'',$txt);
            $nb=strlen($s);
            if($nb>0 and $s[$nb-1]=="\n")
                $nb--;
                $sep=-1;
                $i=0;
                $j=0;
                $l=0;
                $nl=1;
                while($i<$nb){
                    $c=$s[$i];
                    if($c=="\n"){
                        $i++;
                        $sep=-1;
                        $j=$i;
                        $l=0;
                        $nl++;
                        continue;
                    }
                    if($c==' ')
                        $sep=$i;
                        $l+=$cw[$c];
                        if($l>$wmax){
                            if($sep==-1){
                                if($i==$j)
                                    $i++;
                            }else
                                $i=$sep+1;
                                $sep=-1;
                                $j=$i;
                                $l=0;
                                $nl++;
                        }else
                            $i++;
                }
                return $nl;
    }
    
    
    ## Escrever HTML no PDF (não aprovou)
    function WriteHTML($html)
    {
        //HTML parser
        $html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote>"); //supprime tous les tags sauf ceux reconnus
        $html=str_replace("\n",' ',$html); //remplace retour à la ligne par un espace
        $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //éclate la chaîne avec les balises
        foreach($a as $i=>$e)
        {
            if($i%2==0)
            {
                //Text
                if($this->HREF)
                    $this->PutLink($this->HREF,$e);
                    else
                        $this->Write(5,stripslashes($this->txtentities($e)));
            }
            else
            {
                //Tag
                if($e[0]=='/')
                    $this->CloseTag(strtoupper(substr($e,1)));
                    else
                    {
                        //Extract attributes
                        $a2=explode(' ',$e);
                        $tag=strtoupper(array_shift($a2));
                        $attr=array();
                        foreach($a2 as $v)
                        {
                            $a3 = "";
                            if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                                $attr[strtoupper($a3[1])]=$a3[2];
                        }
                        $this->OpenTag($tag,$attr);
                    }
            }
        }
    }
    
    function OpenTag($tag, $attr)
    {
        //Opening tag
        switch($tag){
            case 'STRONG':
                $this->SetStyle('B',true);
                break;
            case 'EM':
                $this->SetStyle('I',true);
                break;
            case 'B':
            case 'I':
            case 'U':
                $this->SetStyle($tag,true);
                break;
            case 'A':
                $this->HREF=$attr['HREF'];
                break;
            case 'IMG':
                if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
                    if(!isset($attr['WIDTH']))
                        $attr['WIDTH'] = 0;
                        if(!isset($attr['HEIGHT']))
                            $attr['HEIGHT'] = 0;
                            $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), $this->px2mm($attr['WIDTH']), $this->px2mm($attr['HEIGHT']));
                }
                break;
            case 'TR':
            case 'BLOCKQUOTE':
            case 'BR':
                $this->Ln(5);
                break;
            case 'P':
                $this->Ln(10);
                break;
            case 'FONT':
                if (isset($attr['COLOR']) && $attr['COLOR']!='') {
                    $coul=$this->hex2dec($attr['COLOR']);
                    $this->SetTextColor($coul['R'],$coul['V'],$coul['B']);
                    $this->issetcolor=true;
                }
                if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {
                    $this->SetFont(strtolower($attr['FACE']));
                    $this->issetfont=true;
                }
                break;
        }
    }
    
    function CloseTag($tag)
    {
        //Closing tag
        if($tag=='STRONG')
            $tag='B';
            if($tag=='EM')
                $tag='I';
                if($tag=='B' || $tag=='I' || $tag=='U')
                    $this->SetStyle($tag,false);
                    if($tag=='A')
                        $this->HREF='';
                        if($tag=='FONT'){
                            if ($this->issetcolor==true) {
                                $this->SetTextColor(0);
                            }
                            if ($this->issetfont) {
                                $this->SetFont('arial');
                                $this->issetfont=false;
                            }
                        }
    }
    
    function SetStyle($tag, $enable)
    {
        //Modify style and select corresponding font
        $this->$tag+=($enable ? 1 : -1);
        $style='';
        foreach(array('B','I','U') as $s)
        {
            if($this->$s>0)
                $style.=$s;
        }
        $this->SetFont('',$style);
    }
    
    function PutLink($URL, $txt)
    {
        //Put a hyperlink
        $this->SetTextColor(0,0,255);
        $this->SetStyle('U',true);
        $this->Write(5,$txt,$URL);
        $this->SetStyle('U',false);
        $this->SetTextColor(0);
    }
    
    ////////////////////////////////////
    //function hex2dec
    //returns an associative array (keys: R,G,B) from
    //a hex html code (e.g. #3FE5AA)
    function hex2dec($couleur = "#000000"){
        $R = substr($couleur, 1, 2);
        $rouge = hexdec($R);
        $V = substr($couleur, 3, 2);
        $vert = hexdec($V);
        $B = substr($couleur, 5, 2);
        $bleu = hexdec($B);
        $tbl_couleur = array();
        $tbl_couleur['R']=$rouge;
        $tbl_couleur['V']=$vert;
        $tbl_couleur['B']=$bleu;
        return $tbl_couleur;
    }
    
    //conversion pixel -> millimeter at 72 dpi
    function px2mm($px){
        return $px*25.4/72;
    }
    
    function txtentities($html){
        $trans = get_html_translation_table(HTML_ENTITIES);
        $trans = array_flip($trans);
        return strtr($html, $trans);
    }
    ////////////////////////////////////
    
    ## conteúdo da classe Sector
    function Sector($xc, $yc, $r, $a, $b, $style='FD', $cw=true, $o=90)
    {
        $d0 = $a - $b;
        if($cw){
            $d = $b;
            $b = $o - $a;
            $a = $o - $d;
        }else{
            $b += $o;
            $a += $o;
        }
        while($a<0)
            $a += 360;
            while($a>360)
                $a -= 360;
                while($b<0)
                    $b += 360;
                    while($b>360)
                        $b -= 360;
                        if ($a > $b)
                            $b += 360;
                            $b = $b/360*2*M_PI;
                            $a = $a/360*2*M_PI;
                            $d = $b - $a;
                            if ($d == 0 && $d0 != 0)
                                $d = 2*M_PI;
                                $k = $this->k;
                                $hp = $this->h;
                                if (sin($d/2))
                                    $MyArc = 4/3*(1-cos($d/2))/sin($d/2)*$r;
                                    else
                                        $MyArc = 0;
                                        //first put the center
                                        $this->_out(sprintf('%.2F %.2F m',($xc)*$k,($hp-$yc)*$k));
                                        //put the first point
                                        $this->_out(sprintf('%.2F %.2F l',($xc+$r*cos($a))*$k,(($hp-($yc-$r*sin($a)))*$k)));
                                        //draw the arc
                                        if ($d < M_PI/2){
                                            $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                                                $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                                                $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                                                $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                                                $xc+$r*cos($b),
                                                $yc-$r*sin($b)
                                                );
                                        }else{
                                            $b = $a + $d/4;
                                            $MyArc = 4/3*(1-cos($d/8))/sin($d/8)*$r;
                                            $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                                                $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                                                $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                                                $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                                                $xc+$r*cos($b),
                                                $yc-$r*sin($b)
                                                );
                                            $a = $b;
                                            $b = $a + $d/4;
                                            $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                                                $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                                                $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                                                $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                                                $xc+$r*cos($b),
                                                $yc-$r*sin($b)
                                                );
                                            $a = $b;
                                            $b = $a + $d/4;
                                            $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                                                $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                                                $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                                                $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                                                $xc+$r*cos($b),
                                                $yc-$r*sin($b)
                                                );
                                            $a = $b;
                                            $b = $a + $d/4;
                                            $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                                                $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                                                $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                                                $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                                                $xc+$r*cos($b),
                                                $yc-$r*sin($b)
                                                );
                                        }
                                        //terminate drawing
                                        if($style=='F')
                                            $op='f';
                                            elseif($style=='FD' || $style=='DF')
                                            $op='b';
                                            else
                                                $op='s';
                                                $this->_out($op);
    }
    
    function _Arc($x1, $y1, $x2, $y2, $x3, $y3 )
    {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
            $x1*$this->k,
            ($h-$y1)*$this->k,
            $x2*$this->k,
            ($h-$y2)*$this->k,
            $x3*$this->k,
            ($h-$y3)*$this->k));
    }
    
    ## conteúdo da classe Diag
    var $legends;
    var $wLegend;
    var $sum;
    var $NbVal;
    
    function PieChart($w, $h, $data, $format, $colors=null)
    {
        $this->SetFont('Courier', '', 10);
        $this->SetLegends($data,$format);
        
        $XPage = $this->GetX();
        $YPage = $this->GetY();
        $margin = 2;
        $hLegend = 5;
        $radius = min($w - $margin * 4 - $hLegend - $this->wLegend, $h - $margin * 2);
        $radius = floor($radius / 2);
        $XDiag = $XPage + $margin + $radius;
        $YDiag = $YPage + $margin + $radius;
        if($colors == null) {
            for($i = 0; $i < $this->NbVal; $i++) {
                $gray = $i * intval(255 / $this->NbVal);
                $colors[$i] = array($gray,$gray,$gray);
            }
        }
        
        //Sectors
        $this->SetLineWidth(0.2);
        $angleStart = 0;
        $angleEnd = 0;
        $i = 0;
        foreach($data as $val) {
            if($val > 0){
                $angle = ($val * 360) / doubleval($this->sum);
            }else{
                $angle = 1;                
            }
            if ($angle != 0) {
                $angleEnd = $angleStart + $angle;
                $this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
                $this->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
                $angleStart += $angle;
            }
            $i++;
        }
        
        //Legends
        $this->SetFont('Courier', '', 10);
        $x1 = $XPage + 2 * $radius + 4 * $margin;
        $x2 = $x1 + $hLegend + $margin;
        $y1 = $YDiag - $radius + (2 * $radius - $this->NbVal*($hLegend + $margin)) / 2;
        for($i=0; $i<$this->NbVal; $i++) {
            $this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
            $this->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
            $this->SetXY($x2,$y1);
            $this->Cell(0,$hLegend,$this->legends[$i]);
            $y1+=$hLegend + $margin;
        }
    }
    
    function BarDiagram($w, $h, $data, $format, $color=null, $maxVal=0, $nbDiv=4)
    {
        $this->SetFont('Courier', '', 10);
        $this->SetLegends($data,$format);
        
        $XPage = $this->GetX();
        $YPage = $this->GetY();
        $margin = 2;
        $YDiag = $YPage + $margin;
        $hDiag = floor($h - $margin * 2);
        $XDiag = $XPage + $margin * 2 + $this->wLegend;
        $lDiag = floor($w - $margin * 3 - $this->wLegend);
        if($color == null)
            $color=array(155,155,155);
            if ($maxVal == 0) {
                $maxVal = max($data);
            }
            $valIndRepere = ceil($maxVal / $nbDiv);
            $maxVal = $valIndRepere * $nbDiv;
            $lRepere = floor($lDiag / $nbDiv);
            $lDiag = $lRepere * $nbDiv;
            $unit = $lDiag / $maxVal;
            $hBar = floor($hDiag / ($this->NbVal + 1));
            $hDiag = $hBar * ($this->NbVal + 1);
            $eBaton = floor($hBar * 80 / 100);
            
            $this->SetLineWidth(0.2);
            $this->Rect($XDiag, $YDiag, $lDiag, $hDiag);
            
            $this->SetFont('Courier', '', 10);
            $this->SetFillColor($color[0],$color[1],$color[2]);
            $i=0;
            foreach($data as $val) {
                //Bar
                $xval = $XDiag;
                $lval = (int)($val * $unit);
                $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
                $hval = $eBaton;
                $this->Rect($xval, $yval, $lval, $hval, 'DF');
                //Legend
                $this->SetXY(0, $yval);
                $this->Cell($xval - $margin, $hval, $this->legends[$i],0,0,'R');
                $i++;
            }
            
            //Scales
            for ($i = 0; $i <= $nbDiv; $i++) {
                $xpos = $XDiag + $lRepere * $i;
                $this->Line($xpos, $YDiag, $xpos, $YDiag + $hDiag);
                $val = $i * $valIndRepere;
                $xpos = $XDiag + $lRepere * $i - $this->GetStringWidth($val) / 2;
                $ypos = $YDiag + $hDiag - $margin;
                $this->Text($xpos, $ypos, $val);
            }
    }
    
    function SetLegends($data, $format)
    {
        $this->legends=array();
        $this->wLegend=0;
        $this->sum=array_sum($data);
        $this->NbVal=count($data);
        foreach($data as $l=>$val)
        {
            if($val >0 ){ 
                $p=sprintf('%.2f',$val/$this->sum*100).'%';
            }else{
                $p=sprintf('%.2f',0).'%';                
            }
            $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
            $this->legends[]=$legend;
            $this->wLegend=max($this->GetStringWidth($legend),$this->wLegend);
        }
    }
    
    //Private properties
    var $tmpFiles = array();
    
    /*******************************************************************************
     *                                                                              *
     *                               Public methods                                 *
     *                                                                              *
     *******************************************************************************/
    function Image($file, $x=null, $y=null, $w=0, $h=0, $type='', $link='', $isMask=false, $maskImg=0)
    {
        //Put an image on the page
        if(!isset($this->images[$file]))
        {
            //First use of this image, get info
            if($type=='')
            {
                $pos=strrpos($file,'.');
                if(!$pos)
                    $this->Error('Image file has no extension and no type was specified: '.$file);
                    $type=substr($file,$pos+1);
            }
            $type=strtolower($type);
            if($type=='png'){
                $info=$this->_parsepng($file);
                if($info=='alpha')
                    return $this->ImagePngWithAlpha($file,$x,$y,$w,$h,$link);
            }
            else
            {
                if($type=='jpeg')
                    $type='jpg';
                    $mtd='_parse'.$type;
                    if(!method_exists($this,$mtd))
                        $this->Error('Unsupported image type: '.$type);
                        $info=$this->$mtd($file);
            }
            if($isMask){
                if(in_array($file,$this->tmpFiles))
                    $info['cs']='DeviceGray'; //hack necessary as GD can't produce gray scale images
                    if($info['cs']!='DeviceGray')
                        $this->Error('Mask must be a gray scale image');
                        if($this->PDFVersion<'1.4')
                            $this->PDFVersion='1.4';
            }
            $info['i']=count($this->images)+1;
            if($maskImg>0)
                $info['masked'] = $maskImg;
                $this->images[$file]=$info;
        }
        else
            $info=$this->images[$file];
            //Automatic width and height calculation if needed
            if($w==0 && $h==0)
            {
                //Put image at 72 dpi
                $w=$info['w']/$this->k;
                $h=$info['h']/$this->k;
            }
            elseif($w==0)
            $w=$h*$info['w']/$info['h'];
            elseif($h==0)
            $h=$w*$info['h']/$info['w'];
            //Flowing mode
            if($y===null)
            {
                if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
                {
                    //Automatic page break
                    $x2=$this->x;
                    $this->AddPage($this->CurOrientation,$this->CurPageFormat);
                    $this->x=$x2;
                }
                $y=$this->y;
                $this->y+=$h;
            }
            if($x===null)
                $x=$this->x;
                if(!$isMask)
                    $this->_out(sprintf('q %.2F 0 0 %.2F %.2F %.2F cm /I%d Do Q',$w*$this->k,$h*$this->k,$x*$this->k,($this->h-($y+$h))*$this->k,$info['i']));
                    if($link)
                        $this->Link($x,$y,$w,$h,$link);
                        return $info['i'];
    }
    
    // needs GD 2.x extension
    // pixel-wise operation, not very fast
    function ImagePngWithAlpha($file,$x,$y,$w=0,$h=0,$link='')
    {
        $tmp_alpha = tempnam('.', 'mska');
        $this->tmpFiles[] = $tmp_alpha;
        $tmp_plain = tempnam('.', 'mskp');
        $this->tmpFiles[] = $tmp_plain;
        
        list($wpx, $hpx) = getimagesize($file);
        $img = imagecreatefrompng($file);
        $alpha_img = imagecreate( $wpx, $hpx );
        
        // generate gray scale pallete
        for($c=0;$c<256;$c++)
            ImageColorAllocate($alpha_img, $c, $c, $c);
            
            // extract alpha channel
            $xpx=0;
            while ($xpx<$wpx){
                $ypx = 0;
                while ($ypx<$hpx){
                    $color_index = imagecolorat($img, $xpx, $ypx);
                    $col = imagecolorsforindex($img, $color_index);
                    imagesetpixel($alpha_img, $xpx, $ypx, $this->_gamma( (127-$col['alpha'])*255/127) );
                    ++$ypx;
                }
                ++$xpx;
            }
            
            imagepng($alpha_img, $tmp_alpha);
            imagedestroy($alpha_img);
            
            // extract image without alpha channel
            $plain_img = imagecreatetruecolor ( $wpx, $hpx );
            imagecopy($plain_img, $img, 0, 0, 0, 0, $wpx, $hpx );
            imagepng($plain_img, $tmp_plain);
            imagedestroy($plain_img);
            
            //first embed mask image (w, h, x, will be ignored)
            $maskImg = $this->Image($tmp_alpha, 0,0,0,0, 'PNG', '', true);
            
            //embed image, masked with previously embedded mask
            $this->Image($tmp_plain,$x,$y,$w,$h,'PNG',$link, false, $maskImg);
    }
    
    function Close()
    {
        parent::Close();
        // clean up tmp files
        foreach($this->tmpFiles as $tmp)
            @unlink($tmp);
    }
    
    /*******************************************************************************
     *                                                                              *
     *                               Private methods                                *
     *                                                                              *
     *******************************************************************************/
    function _putimages()
    {
        $filter=($this->compress) ? '/Filter /FlateDecode ' : '';
        reset($this->images);
        while(list($file,$info)=@each($this->images))
        {
            $this->_newobj();
            $this->images[$file]['n']=$this->n;
            $this->_out('<</Type /XObject');
            $this->_out('/Subtype /Image');
            $this->_out('/Width '.$info['w']);
            $this->_out('/Height '.$info['h']);
            
            if(isset($info['masked']))
                $this->_out('/SMask '.($this->n-1).' 0 R');
                
                if($info['cs']=='Indexed')
                    $this->_out('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal'])/3-1).' '.($this->n+1).' 0 R]');
                    else
                    {
                        $this->_out('/ColorSpace /'.$info['cs']);
                        if($info['cs']=='DeviceCMYK')
                            $this->_out('/Decode [1 0 1 0 1 0 1 0]');
                    }
                    $this->_out('/BitsPerComponent '.$info['bpc']);
                    if(isset($info['f']))
                        $this->_out('/Filter /'.$info['f']);
                        if(isset($info['parms']))
                            $this->_out($info['parms']);
                            if(isset($info['trns']) && is_array($info['trns']))
                            {
                                $trns='';
                                for($i=0;$i<count($info['trns']);$i++)
                                    $trns.=$info['trns'][$i].' '.$info['trns'][$i].' ';
                                    $this->_out('/Mask ['.$trns.']');
                            }
                            $this->_out('/Length '.strlen($info['data']).'>>');
                            $this->_putstream($info['data']);
                            unset($this->images[$file]['data']);
                            $this->_out('endobj');
                            //Palette
                            if($info['cs']=='Indexed')
                            {
                                $this->_newobj();
                                $pal=($this->compress) ? gzcompress($info['pal']) : $info['pal'];
                                $this->_out('<<'.$filter.'/Length '.strlen($pal).'>>');
                                $this->_putstream($pal);
                                $this->_out('endobj');
                            }
        }
    }
    
    // GD seems to use a different gamma, this method is used to correct it again
    function _gamma($v){
        return pow ($v/255, 2.2) * 255;
    }
    
    // this method overriding the original version is only needed to make the Image method support PNGs with alpha channels.
    // if you only use the ImagePngWithAlpha method for such PNGs, you can remove it from this script.
    function _parsepng($file)
    {
        //Extract info from a PNG file
        $f=fopen($file,'rb');
        if(!$f)
            $this->Error('Can\'t open image file: '.$file);
            //Check signature
            if($this->_readstream($f,8)!=chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10))
                $this->Error('Not a PNG file: '.$file);
                //Read header chunk
                $this->_readstream($f,4);
                if($this->_readstream($f,4)!='IHDR')
                    $this->Error('Incorrect PNG file: '.$file);
                    $w=$this->_readint($f);
                    $h=$this->_readint($f);
                    $bpc=ord($this->_readstream($f,1));
                    if($bpc>8)
                        $this->Error('16-bit depth not supported: '.$file);
                        $ct=ord($this->_readstream($f,1));
                        if($ct==0)
                            $colspace='DeviceGray';
                            elseif($ct==2)
                            $colspace='DeviceRGB';
                            elseif($ct==3)
                            $colspace='Indexed';
                            else {
                                fclose($f);      // the only changes are
                                return 'alpha';  // made in those 2 lines
                            }
                            if(ord($this->_readstream($f,1))!=0)
                                $this->Error('Unknown compression method: '.$file);
                                if(ord($this->_readstream($f,1))!=0)
                                    $this->Error('Unknown filter method: '.$file);
                                    if(ord($this->_readstream($f,1))!=0)
                                        $this->Error('Interlacing not supported: '.$file);
                                        $this->_readstream($f,4);
                                        $parms='/DecodeParms <</Predictor 15 /Colors '.($ct==2 ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w.'>>';
                                        //Scan chunks looking for palette, transparency and image data
                                        $pal='';
                                        $trns='';
                                        $data='';
                                        do
                                        {
                                            $n=$this->_readint($f);
                                            $type=$this->_readstream($f,4);
                                            if($type=='PLTE')
                                            {
                                                //Read palette
                                                $pal=$this->_readstream($f,$n);
                                                $this->_readstream($f,4);
                                            }
                                            elseif($type=='tRNS')
                                            {
                                                //Read transparency info
                                                $t=$this->_readstream($f,$n);
                                                if($ct==0)
                                                    $trns=array(ord(substr($t,1,1)));
                                                    elseif($ct==2)
                                                    $trns=array(ord(substr($t,1,1)), ord(substr($t,3,1)), ord(substr($t,5,1)));
                                                    else
                                                    {
                                                        $pos=strpos($t,chr(0));
                                                        if($pos!==false)
                                                            $trns=array($pos);
                                                    }
                                                    $this->_readstream($f,4);
                                            }
                                            elseif($type=='IDAT')
                                            {
                                                //Read image data block
                                                $data.=$this->_readstream($f,$n);
                                                $this->_readstream($f,4);
                                            }
                                            elseif($type=='IEND')
                                            break;
                                            else
                                                $this->_readstream($f,$n+4);
                                        }
                                        while($n);
                                        if($colspace=='Indexed' && empty($pal))
                                            $this->Error('Missing palette in '.$file);
                                            fclose($f);
                                            return array('w'=>$w, 'h'=>$h, 'cs'=>$colspace, 'bpc'=>$bpc, 'f'=>'FlateDecode', 'parms'=>$parms, 'pal'=>$pal, 'trns'=>$trns, 'data'=>$data);
    }
    
}// class

?>