<?php

namespace shirne\paginator;


use think\Paginator;

/**
 * 基于Bootstrap4的分页样式
 * Class Bootstrap4
 * @package shirne\paginator
 */
class Bootstrap4 extends Paginator
{
    protected $extstyle='';
    protected $side=3;
    protected $jump=0;

    public function __construct($items, $listRows, $currentPage = null, $total = null, $simple = false, array $options = [])
    {
        $options = array_merge($options, config('paginate'));
        parent::__construct($items, $listRows, $currentPage, $total, $simple, $options);
        if(!isset($this->options['simple'])){
            $this->options['simple'] = false;
        }
        if(isset($this->options['justify'])) {
            switch ($this->options['justify']){
                case 'center':
                    $this->extstyle = ' justify-content-center';
                    break;
                case 'right':
                    $this->extstyle = ' justify-content-end';
                    break;
                default:
                    break;
            }
        }
        if(isset($this->options['size'])) {
            switch ($this->options['size']){
                case 'large':
                case 'lg':
                    $this->extstyle .= ' pagination-lg';
                    break;
                case 'small':
                case 'sm':
                    $this->extstyle .= ' pagination-sm';
                    break;
                default:
                    break;
            }
        }
        if(isset($this->options['side'])){
            $this->side=intval($this->options['side']);
        }
        if(isset($this->options['jump'])){
            $this->jump=intval($this->options['jump']);
        }
    }

    /**
     * 第一页按钮
     * @param string $text
     * @return string
     */
    protected function getFirstButton($text = "First")
    {

        if ($this->currentPage() <= 1) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->url(
            1
        );

        return $this->getPageLinkWrapper($url, $text);
    }

    /**
     * 上一页按钮
     * @param string $text
     * @return string
     */
    protected function getPreviousButton($text = "&laquo;")
    {

        if ($this->currentPage() <= 1) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->url(
            $this->currentPage() - 1
        );

        return $this->getPageLinkWrapper($url, $text);
    }

    /**
     * 下一页按钮
     * @param string $text
     * @return string
     */
    protected function getNextButton($text = '&raquo;')
    {
        if (!$this->hasMore) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->url($this->currentPage() + 1);

        return $this->getPageLinkWrapper($url, $text);
    }

    /**
     * 最后一页按钮
     * @param string $text
     * @return string
     */
    protected function getLastButton($text = 'Last')
    {
        if (!$this->hasMore) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->url($this->lastPage());

        return $this->getPageLinkWrapper($url, $text);
    }

    /**
     * 页码按钮
     * @return string
     */
    protected function getLinks()
    {
        if ($this->simple) {
            return '';
        }

        $block = [
            'first'  => null,
            'slider' => null,
            'last'   => null,
        ];

        $side   = $this->side;
        $window = $side * 2 + 1;

        if ($this->lastPage < $window + 4) {
            $block['first'] = $this->getUrlRange(1, $this->lastPage);
        } elseif ($this->currentPage <= $side + 1) {
            $block['first'] = $this->getUrlRange(1, $window + 1);
            $block['last']  = $this->getUrlRange($this->lastPage,$this->lastPage);
        } elseif ($this->currentPage > ($this->lastPage - $side - 1)) {
            $block['first'] = $this->getUrlRange(1,1);
            $block['last']  = $this->getUrlRange($this->lastPage - ($window + 1), $this->lastPage);
        } else {
            $block['first']  = $this->getUrlRange(1,1);
            $block['slider'] = $this->getUrlRange($this->currentPage - $side, $this->currentPage + $side);
            $block['last']   = $this->getUrlRange($this->lastPage,$this->lastPage);
        }

        $html = '';

        if (!empty($block['first'])) {
            $html .= $this->getUrlLinks($block['first']);
        }

        if (!empty($block['slider'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($block['slider']);
        }

        if (!empty($block['last'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($block['last']);
        }

        return $html;
    }

    /**
     * 渲染分页html
     * @return mixed
     */
    public function render()
    {
        if ($this->hasPages()) {
            if ($this->simple) {
                return sprintf(
                    '<nav aria-label="Page navigation"><ul class="pagination'.$this->extstyle.'">%s %s %s</ul></nav>',
                    $this->getPreviousButton(),
                    $this->getActivePageWrapper($this->currentPage()),
                    $this->getNextButton()
                );
            }elseif ($this->options['simple']) {
                return sprintf(
                    '<nav aria-label="Page navigation">%s<ul class="pagination'.$this->extstyle.'">%s %s %s %s %s</ul></nav>',
                    $this->jump?$this->getJumpComponent():'',
                    $this->getFirstButton(),
                    $this->getPreviousButton(),
                    $this->getActivePageWrapper($this->currentPage()),
                    $this->getNextButton(),
                    $this->getLastButton()
                );
            } else {
                return sprintf(
                    '<nav aria-label="Page navigation">%s<ul class="pagination'.$this->extstyle.'">%s %s %s</ul></nav>',
                    $this->jump?$this->getJumpComponent():'',
                    $this->getPreviousButton(),
                    $this->getLinks(),
                    $this->getNextButton()
                );
            }
        }
    }

    /**
     * 生成一个可点击的按钮
     *
     * @param  string $url
     * @param  int    $page
     * @return string
     */
    protected function getAvailablePageWrapper($url, $page)
    {
        return '<li class="page-item"><a class="page-link" href="' . htmlentities($url) . '">' . $page . '</a></li>';
    }

    /**
     * 生成一个禁用的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getDisabledTextWrapper($text)
    {
        return '<li class="page-item disabled"><span class="page-link">' . $text . '</span></li>';
    }

    /**
     * 生成一个激活的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getActivePageWrapper($text)
    {
        return '<li class="page-item active"><span class="page-link">' . $text . '</span></li>';
    }

    /**
     * 生成省略号按钮
     *
     * @return string
     */
    protected function getDots()
    {
        return $this->getDisabledTextWrapper('...');
    }

    /**
     * 批量生成页码按钮.
     *
     * @param  array $urls
     * @return string
     */
    protected function getUrlLinks(array $urls)
    {
        $html = '';

        foreach ($urls as $page => $url) {
            $html .= $this->getPageLinkWrapper($url, $page);
        }

        return $html;
    }

    /**
     * 生成普通页码按钮
     *
     * @param  string $url
     * @param  int    $page
     * @return string
     */
    protected function getPageLinkWrapper($url, $page)
    {
        if ($this->currentPage() == $page) {
            return $this->getActivePageWrapper($page);
        }

        return $this->getAvailablePageWrapper($url, $page);
    }

    /**
     * 生成跳转链接
     * @return string
     */
    protected function getJumpComponent($text='跳转到：'){
        $html=['<div class="fr jumppage">'.$text.'<select onchange="location.href=this.value;" >'];
        for($i=1; $i<=$this->lastPage; $i++){
            $html[]='<option value="'.$this->url($i).'" '.($i==$this->currentPage?'selected':'').'>'.$i.'</option>';
        }
        $html[]='</select></div>';
        return implode('',$html);
    }
}