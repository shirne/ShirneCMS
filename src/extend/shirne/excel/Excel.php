<?php
namespace shirne\excel;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use think\Exception;

define('OD_COLUMN_COUNT',26);

class Excel {

    /**
     * Xls,Xlsx
     */
    private $format;

    private $excel;
    private $sheet;
    private $rownum;

    public $rowcount;

    private $columntype;
    private $columnmap;

    /**
     * @param string $fmt Excel5,Excel2007
     */
    function __construct($fmt='Xls'){
        $this->format=$fmt;
        $this->excel=new Spreadsheet();
        $this->sheet=$this->excel->getActiveSheet();
        $this->rownum=1;
        $this->columnmap=[];
        $this->init_colum();
        $this->columntype=[];
    }

    public function load($file)
    {
        $reader = IOFactory::createReader($this->format); // 读取 excel 文档
        $this->excel = $reader->load($file); // 文档名称
        $this->sheet = $this->excel->getActiveSheet();
    }

    protected function init_colum()
    {
        $words='ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $l=strlen($words);
        for ($i=0; $i < $l; $i++) {
            $this->columnmap[]=substr($words, $i, 1);
        }
    }

    protected function extend_colum()
    {
        $l=OD_COLUMN_COUNT;
        if(count($this->columnmap)>$l)return;
        for ($i=0; $i < $l; $i++) {
            for ($j=0; $j < $l; $j++) {
                $this->columnmap[]=$this->columnmap[$i].$this->columnmap[$j];
            }
        }
    }

    public function read($file='',$start=1,$limit=100,$maxcolumn=255){
        if(!empty($file)){
            $this->load($file);
        }
        $highestRow = $this->sheet->getHighestRow(); // 取得总行数
        $highestColumn = $this->sheet->getHighestColumn(); // 取得总列数
        if(strlen($highestColumn)>1){
            $this->extend_colum();
        }
        if(strlen($highestColumn)>2){
            $maxcolumnnum=$maxcolumn;
        }else {
            $maxcolumnnum = array_search($highestColumn, $this->columnmap);
            if ($maxcolumnnum === false) throw new Exception('Excepted column: ' . $highestColumn);

            if ($maxcolumnnum > $maxcolumn) $maxcolumnnum = $maxcolumn;
        }
        $this->rowcount=$highestRow;

        if($highestRow>$start+$limit)$highestRow=$start+$limit;

        // 一次读取一行
        $data=array();
        for ($row = $start; $row <= $highestRow; $row++) {
            $datarow=array();
            for ($column = 0; $column<=$maxcolumnnum; $column++) {
                $datarow[$column] = $this->sheet->getCellByColumnAndRow($column, $row)->getValue();
            }
            $data[]=$datarow;
        }
        return $data;
    }

    public function getExcel(){
        return $this->excel;
    }

    public function getSheet(){
        return $this->sheet;
    }

    public function getSheets(){
        return $this->excel->getSheetNames();
    }
    public function setSheet($name){
        return $this->sheet = $this->excel->setActiveSheetIndexByName($name);
    }

    public function getCell($cell){
        return $this->sheet->getCell($cell);
    }

    public function getRownum(){
        return $this->rownum;
    }

    public function setInfo($info){
        $prop=$this->excel->getProperties();
        if(isset($info['creator']))$prop->setCreator($info['creator']);
        if(isset($info['modified_by']))$prop->setLastModifiedBy($info['modified_by']);
        if(isset($info['title']))$prop->setTitle($info['title']);
        if(isset($info['subject']))$prop->setSubject($info['subject']);
        if(isset($info['description']))$prop->setDescription($info['description']);
        if(isset($info['keywords']))$prop->setKeywords($info['keywords']);
        if(isset($info['category']))$prop->setCategory($info['category']);
    }

    /**
     * @param $column int|string
     * @param $type string DataType
     */
    public function setColumnType($column,$type){
        if(is_numeric($column)){
            if($column>OD_COLUMN_COUNT){
                $this->extend_colum();
            }
            $column=$this->columnmap[$column];
        }
        $this->columntype[$column]=$type;
    }

    public function getRangeStyle($range)
    {
        if(!$range instanceof Style){
            if(is_array($range)){
                if(count($range)==4) {
                    return $this->sheet->getStyleByColumnAndRow($range[0], $range[1], $range[2], $range[3]);
                }else{
                    return $this->sheet->getStyleByColumnAndRow($range[0], $range[1]);
                }
            }else {
                if(strpos($range, ':')>0){
                    list($rangeStart, $rangeEnd) = Coordinate::rangeBoundaries($range);

                    return $this->sheet->getStyleByColumnAndRow($rangeStart[0],$rangeStart[1],$rangeEnd[0],$rangeEnd[1]);
                }else{
                    return $this->sheet->getStyle($range);
                }
            }
        }
        return $range;
    }

    public function setRangeStyle($range, $setting)
    {
        $style = $this->getRangeStyle($range);
        foreach ($setting as $k=>$val){
            if($k == 'border'){

                $this->setRangeBorder($style, $val);

            }elseif($k == 'font'){

                $this->setRangeFont($style, $val);

            }elseif($k == 'fill'){

                $this->setRangeFill($style, $val);

            }elseif($k == 'align'){

                $this->setRangeAlign($style, $val);

            }
        }
    }

    public function setRangeFont($range, $value)
    {
        $style = $this->getRangeStyle($range);
        $font = $style->getFont();
        if(is_array($value)) {
            foreach ($value as $key => $v) {
                $method = "set".ucfirst($key);
                if(method_exists($font,$method)) {
                    $font->$method($v);
                }else{
                    //警告?
                }
            }
        }else{
            if(is_int($value)) {
                $font->setSize($value);
            }else{
                $font->setColor(new Color($value));
            }
        }
    }

    public function setRangeFill($range, $value)
    {
        $style = $this->getRangeStyle($range);
        $fill = $style->getFill();
        if(is_array($value)) {
            $fill->setFillType(Fill::FILL_GRADIENT_LINEAR);
            $fill->setEndColor(new Color($value[0]));
            $fill->setStartColor(new Color($value[1]));
            if (isset($value[2])) {
                $fill->setRotation($value[2]);
            }
        }elseif($value == 'none'){
            $fill->setFillType(Fill::FILL_NONE);
        }else{
            $fill->setFillType(Fill::FILL_SOLID);
            $color = new Color($value);
            $fill->setEndColor($color);
            $fill->setStartColor($color);
        }
    }

    public function setRangeAlign($range, $value)
    {
        $style = $this->getRangeStyle($range);
        $align = $style->getAlignment();
        if(is_array($value)){
            $keys = array_keys($value);
            if($keys == [0,1]){
                $align->setHorizontal($value[0]);
                $align->setVertical($value[1]);
            }else {
                foreach ($value as $key => $v) {
                    $method = "set" . ucfirst($key);
                    if (method_exists($align, $method)) {
                        $align->$method($v);
                    } else {
                        //警告?
                    }
                }
            }
        }else{
            //默认设置水平
            $align->setHorizontal($value);
        }
    }

    public function setRangeBorder($range, $value)
    {
        $style = $this->getRangeStyle($range);
        $border = $style->getBorders();
        $allborder = $border->getAllBorders();
        if(is_array($value) && is_assoc_array($value)) {

            foreach ($value as $key => $val) {
                if($key == 'style') {
                    $allborder->setBorderStyle($val);
                }elseif($key == 'color'){
                    $allborder->setColor(new Color($val));
                }elseif(in_array($key,['left','right','top','bottom','inside','diagonal','outline','horizontal','vertical'])){
                    $method = 'get'.ucfirst($key);
                    $subborder=$border->$method();
                    if($subborder instanceof Border) {
                        $this->setBorderStyle($subborder, $this->transBorderStyle($val));
                    }
                }
            }

        }else{
            $this->setBorderStyle($allborder, $this->transBorderStyle($value));
        }
    }

    private function transBorderStyle($style)
    {
        $styleData=['style'=>Border::BORDER_THIN, 'color'=>Color::COLOR_BLACK];
        if(is_array($style)) {

            if (is_assoc_array($style)) {
                foreach ($style as $key => $value) {
                    $styleData[$key] = $value;
                }
            } else {
                $styleData['style'] = $style[0];
                if (isset($style[1])) {
                    $styleData['color'] = $style[1];
                }
            }
        }elseif($style == 'none'){
            $styleData['style']=Border::BORDER_NONE;
        }else{
            $styleData['color']=$style;
        }
        return $styleData;
    }

    /**
     * @param $border Border
     * @param $style array
     */
    private function setBorderStyle($border, $style)
    {
        if(isset($style['style'])){
            $border->setBorderStyle($style['style']);
        }
        if(isset($style['color'])){
            $border->setColor(new Color($style['color']));
        }
    }

    public function setTitle($title){
        $this->sheet->setTitle($title);
    }

    public function setPageHeader($header='&C&B&TITLE'){
        $this->sheet->getHeaderFooter()->setOddHeader(str_replace('&TITLE',$this->excel->getProperties()->getTitle(),$header));

    }

    public function setPageFooter($footer='&L&TITLE &RPage &P of &N'){
        $this->sheet->getHeaderFooter()->setOddFooter(str_replace('&TITLE',$this->excel->getProperties()->getTitle(),$footer));
    }

    /**
     * 设置表头,其实与设置行一样，以后会增加其它功能
     */
    public function setHeader($header=array()){
        $i=0;
        if(count($header)>OD_COLUMN_COUNT){
            $this->extend_colum();
        }
        foreach ($header as $key => $value) {
            $this->sheet->setCellValueExplicit($this->columnmap[$i].$this->rownum,$value,DataType::TYPE_STRING);
            $i++;
        }
        $this->rownum++;
    }

    public function addRow($row){
        $i=0;
        if(count($row)>OD_COLUMN_COUNT){
            $this->extend_colum();
        }
        foreach ($row as $key => $value) {
            if(is_array($value)){
                $this->sheet->setCellValueExplicit($this->columnmap[$i] . $this->rownum, $value[0], $value[1]);
            }else {
                if (isset($this->columntype[$this->columnmap[$i]])) {
                    $this->sheet->setCellValueExplicit($this->columnmap[$i] . $this->rownum, $value, $this->columntype[$this->columnmap[$i]]);
                } else {
                    $this->sheet->setCellValue($this->columnmap[$i] . $this->rownum, $value);
                }
            }
            $i++;
        }
        $this->rownum++;
    }

    public function merge($cell1, $cell2){
        $this->sheet->mergeCells($cell1.':'.$cell2);
    }

    /**
     * 删除行,默认删除尾行
     */
    public function delRow($row=null, $num=1){
        if(is_null($row)){
            $row=$this->rownum;
        }
        $this->sheet->removeRow($row,$num);
        $this->rownum -= $num;
    }

    /**
     * 清空,是否清空表头
     */
    public function clear($wh=false){
        $this->sheet->removeRow($wh?1:2,$this->rownum);
        $this->rownum=$wh?1:2;
    }

    /**
     * 保存到文件
     * @param $path string
     * @return TRUE/FALSE
     */
    public function saveTo($path){
        $objWriter = IOFactory::createWriter($this->excel, $this->format);
        $objWriter->save($path);
        return true;
    }

    /**
     * 输出
     * @param $filename string 文件名
     */
    public function output($filename=''){
        $dir=app()->getRuntimePath().'/Data/excel';
        if(!is_dir($dir)){
            mkdir($dir,0777,TRUE);
        }
        $path=$dir.'/'.time().'-'.base_convert(microtime(), 10, 32).'.tmp';
        $this->saveTo($path);
        $file = fopen($path,"r"); // 打开文件
        $size=filesize($path);
        // 输入文件标签
        Header("Content-type: application/vnd.ms-excel");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length: ".$size);
        Header("Content-Disposition: attachment; filename=" . $filename.'.'.strtolower($this->format));
        // 输出文件内容
        echo fread($file,$size);
        fclose($file);
        unlink($path);
        exit();
    }
}