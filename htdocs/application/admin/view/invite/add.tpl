<extend name="Public:Base" />

<block name="body">

<include file="Public/bread" menu="invite_index" section="会员" title="邀请码" />

<div id="page-wrapper">
	<div class="page-header">生成邀请码</div>
	<div id="page-content">
<form action="{:url('Invite/add')}" method="post">
	<div class="form-group">
		<label>绑定邀请人</label>
		<div class="input-group">
			<input class="form-control" type="text" name="member_id" placeholder="填写会员ID">
			<a class="btn btn-dark input-group-addon"><i class="ion-user"></i> 选择会员</a>
		</div>
	</div>
	<div class="form-group">
		<label>绑定会员组</label>
		<div class="input-group">
			<select name="level_id" class="form-control">
				<foreach name="levels" item="lv">
					<option value="{$lv['level_id']}" {$lv['is_default']==1?'selected':''}>{$lv['level_name']} ￥{$lv['level_price']}<if condition="$lv['is_default'] EQ 1">[默认]</if></option>
				</foreach>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label>有效期</label>
		<input class="form-control" type="text" name="valid_date" placeholder="不填写则不限制，格式 2016-09-18" />
	</div>
	<div class="form-group">
		<label>邀请码长度</label>
		<input class="form-control" type="number" name="length" value="16" placeholder="要生成邀请码的长度(8-16)">
	</div>
	<div class="form-group">
		<label>生成数量</label>
		<input class="form-control" type="number" max="1000" name="number" placeholder="要生成邀请码的数量,最大1000">
	</div>
	<div class="form-group">
		<button class="btn btn-primary" type="submit" >生成</button>
	</div>


</form>
		</div>
</div>

</block>
