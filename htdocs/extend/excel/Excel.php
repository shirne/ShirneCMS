<?php
namespace shirne\excel;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Excel {

    /**
     * Excel5,Excel2007
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
        $this->columnmap=array();
        $words='ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $l=strlen($words);
        for ($i=0; $i < $l; $i++) {
            $this->columnmap[]=substr($words, $i, 1);
        }
        for ($i=0; $i < $l; $i++) {
            for ($j=0; $j < $l; $j++) {
                $this->columnmap[]=$this->columnmap[$i].$this->columnmap[$j];
            }
        }
        $this->columntype=array();
    }

    public function load($file)
    {
        $reader = IOFactory::createReader($this->format); // 读取 excel 文档
        $this->excel = $reader->load($file); // 文档名称
        $this->sheet = $this->excel->getActiveSheet();
    }
    public function read($file='',$start=1,$limit=100,$maxcolumn=255){
        if(!empty($file)){
            $this->load($file);
        }
        $highestRow = $this->sheet->getHighestRow(); // 取得总行数
        $highestColumn = $this->sheet->getHighestColumn(); // 取得总列数

        $hc=0;
        foreach ($this->columnmap as $k=>$cm){
            if($highestColumn==$cm){
                $hc=$k;
                break;
            }
        }
        if($hc>$maxcolumn)$hc=$maxcolumn;
        $this->rowcount=$highestRow;

        if($highestRow>$start+$limit)$highestRow=$start+$limit;

        // 一次读取一行
        $data=array();
        for ($row = $start; $row <= $highestRow; $row++) {
            $datarow=array();
            for ($column = 0; $column<=$hc; $column++) {
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
            $column=$this->columnmap[$column];
        }
        $this->columntype[$column]=$type;
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
        foreach ($header as $key => $value) {
            $this->sheet->setCellValueExplicit($this->columnmap[$i].$this->rownum,$value,DataType::TYPE_STRING);
            $i++;
        }
        $this->rownum++;
    }

    public function addRow($row){
        $i=0;
        foreach ($row as $key => $value) {
            if(isset($this->columntype[$this->columnmap[$i]])){
                $this->sheet->setCellValueExplicit($this->columnmap[$i].$this->rownum,$value,$this->columntype[$this->columnmap[$i]]);
            }else{
                $this->sheet->setCellValue($this->columnmap[$i].$this->rownum,$value);
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
        Header("Content-Disposition: attachment; filename=" . $filename.'.'.($this->format=='Excel2007'?'xlsx':'xls'));
        // 输出文件内容
        echo fread($file,$size);
        fclose($file);
        unlink($path);
        exit();
    }
}