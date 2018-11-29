## Dialog

> 基于BootStrap4.x的弹出框库 

### 参数表

> 所有回调函数可以接收两个参数，一个弹框body的jQuery对象，一个弹框体的jQuery对象，并且函数绑定弹框对象本身
<table>
    <thead>
        <tr>
            <th>参数</th><th>类型</th><th>说明</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td> size </td><td> sm/md/lg </td><td> 弹出框尺寸 </td>
        </tr>
        <tr>
            <td>btns </td><td> 数组 </td><td> 按钮设置 </td>
        </tr>
        <tr>
            <td>onsure</td><td> callback </td><td> 确认操作 </td>
        </tr>
        <tr>
            <td>onshow</td><td> callback </td><td> 显示回调 </td>
        </tr>
        <tr>
            <td>onshown</td><td> callback </td><td> 显示完成回调 </td>
        </tr>
        <tr>
            <td>onhide</td><td> callback </td><td> 关闭回调<br />return false 会阻止弹框关闭 </td>
        </tr>
        <tr>
            <td>onhidden</td><td> callback </td><td> 关闭完成回调 </td>
        </tr>
    </tbody>
</table>

### 方法表

| 方法名   | 参数  | 说明   |
|:-----:|:-----:|:-----:|
| show | content,title | 显示弹框,content可以为html,title可省略 |
| hide | 无 | 隐藏弹框 |

### 快捷调用

> 快捷调用绑定在全局 dialog 对象上

| 方法名   | 参数  | 说明   |
|:-----:|:-----:|:-----:|
| alert | message\[,callback\[,title\]\] | 提示框 |
| confirm | message\[,confirm\[,cancel\]\] | 确认框 |
| prompt | message,callback\[,cancel\] | 输入框 |
| action | list,callback\[,title\] | 操作选项 |
| pickList | config,callback\[,filter\] | 异步拉取列表数据选择 |
| pickUser | callback\[,filter\] | 检索并选择用户弹框 |
| pickArticle | callback\[,filter\] | 检索并选择文章弹框 |
| pickProduct | callback\[,filter\] | 检索并选择产品弹框 |
| pickLocate | type(baidu/google/tencent/daode), callback, locate | 地图位置选择 |