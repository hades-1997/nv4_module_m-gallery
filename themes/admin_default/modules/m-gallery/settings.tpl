<!-- BEGIN: main -->
<form class="form-inline" role="form" action="{NV_BASE_ADMINURL}index.php" method="post">
	<input type="hidden" name ="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" />
	<input type="hidden" name ="{NV_OP_VARIABLE}" value="{OP}" />
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-hover">
			<caption><em class="fa fa-file-text-o">&nbsp;</em>{LANG.setting_view}</caption>
			<tbody>
			
				<tr>
					<th>{LANG.setting_homesite}</th>
					<td><input class= "form-control" type="text" value="{DATA.homewidth}" name="homewidth" /><span class="text-middle"> x </span><input class= "form-control" type="text" value="{DATA.homeheight}" name="homeheight" /></td>
				</tr>
				<tr>
					<th>{LANG.setting_thumbblock}</th>
					<td><input class= "form-control" type="text" value="{DATA.blockwidth}" name="blockwidth" />
					<span class="text-middle"> x </span><input class= "form-control" type="text" value="{DATA.blockheight}" 
					name="blockheight" /></td>
				</tr>
				<tr>
					<th>{LANG.speed}</th>
					<td><input class="form-control" name="speeditem"  type="text" value="{DATA.speeditem}" /> </td>
				</tr>
				<tr>
					<th>{LANG.items}</th>
					<td><input class="form-control" name="items"  type="text" value="{DATA.items}" /> </td>
				</tr>
				<tr>
					<th>{LANG.show_no_image}</th>
					<td><input class="form-control" name="show_no_image" id="show_no_image" value="{SHOW_NO_IMAGE}" style="width:340px;" type="text"/> <input value="{GLANG.browse_image}" name="selectimg" type="button" class="btn btn-info"/></td>
				</tr>
				
			
				<tr>
					<th>{LANG.structure_image_upload}</th>
					<td>
					<select class="form-control" name="structure_upload">
						<!-- BEGIN: structure_upload -->
						<option value="{STRUCTURE_UPLOAD.key}"{STRUCTURE_UPLOAD.selected}>{STRUCTURE_UPLOAD.title}</option>
						<!-- END: structure_upload -->
					</select></td>
				</tr>
				
			
			</tbody>
			<tfoot>
				<tr>
					<td class="text-center" colspan="2">
						<input class="btn btn-primary" type="submit" value="{LANG.save}" name="Submit1" />
						<input type="hidden" value="1" name="savesetting" />
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</form>
<script type="text/javascript">
	//<![CDATA[
	$("input[name=selectimg]").click(function() {
		var area = "show_no_image";
		var type = "image";
		var path = "{PATH}";
		var currentpath = "{CURRENTPATH}";
		nv_open_browse("{NV_BASE_ADMINURL}index.php?" + nv_name_variable + "=upload&popup=1&area=" + area + "&path=" + path + "&type=" + type + "&currentpath=" + currentpath, "NVImg", 850, 420, "resizable=no,scrollbars=no,toolbar=no,location=no,status=no");
		return false;
	});
	//]]>
</script>

<!-- END: main -->