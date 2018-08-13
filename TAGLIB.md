## article

> 本标签库中所有闭合标签均为变量生成标签，需要配合volist等标签来输出，<br />其行为类似内置assign标签，但目的是读取列表数据

* **list** 标签 [闭合标签]

> 读取文章列表，可用属性：
>> **var** 指定变量名。调用完此标签将会生成一个列表变量<br />
   **category** 指定分类ID或标识，另外指定 recursive属性可读取该分类及所有子分类的文章<br />
   **type** 指定文章类型属性<br />
   **order** 指定排序规则，可跟 [空格]ASC|DESC<br />
   **limit** 指定读取条数<br />
   **cover** 指定是否含有封面图的文章

* **relation** 标签 [闭合标签]

> 读取相关文章，可用属性：
>> **var** 指定变量名。<br />
   **category** 指定分类ID或标识<br />
   **id** 指定当前文章ID<br />
   **limit** 指定读取条数

* **prev** 标签 [闭合标签]

> 读取上一篇文章，可用属性：
>> **var** 指定变量名。<br />
   **category** 指定分类ID或标识<br />
   **id** 指定当前文章ID
   
* **next** 标签 [闭合标签]

> 读取下一篇文章，可用属性：
>> **var** 指定变量名。<br />
   **category** 指定分类ID或标识<br />
   **id** 指定当前文章ID

* **pages** 标签 [闭合标签]

> 读取页面列表，可用属性：
>> **var** 指定变量名<br />
    **group** 指定页面分组<br />
    **limit** 限制读取条数
    
* **page** 标签 [闭合标签]

> 读取页面，可用属性：
>> **var** 指定变量名<br />
    **name** 指定页面标识
    
* **cates** 标签 [闭合标签]

> 读取分类列表，可用属性：
>> **var** 指定变量名<br />
    **pid** 指定上级分类id ,默认为0 读取一级分类
    
* **cate** 标签 [闭合标签]

> 读取分类，可用属性：
>> **var** 指定变量名<br />
    **name** 指定分类id或标识
    
* **listwrap** 标签

> 嵌套volist等标签，切割数组，可用属性：
>> **name** 输入数组变量名<br />
    **step** 切割条数<br />
    **id** 切割后的变量名
    
> 示例代码(生成三个一列的列表)：
```
<article:listwrap name="artlist" step="3" id="wrapedlist">
    <div class="row">
    <volist name="wrapedlist" id="article">
        <div class="col">
            <a href="{:url('index/article/view',['id'=>$article['id']])}">{$article.title}</a>
        </div>
    </volist>
    </div>
</article:listwrap>
```

## extendtag

> 主要用于读取广告，链接，公告等数据的标签库

* **links** 标签
> **var** 指定变量名<br />
**limit** 限制读取条数

* **advs** 标签
> **var** 指定变量名<br />
**flag** 指定广告位<br />
**limit** 限制读取条数

* **notices** 标签
> **var** 指定变量名<br />
**limit** 限制读取条数

* **feedback** 标签
> **var** 指定变量名<br />
**limit** 限制读取条数<br />
**page** 是否分页 分页后可用 $var->render()生成页码