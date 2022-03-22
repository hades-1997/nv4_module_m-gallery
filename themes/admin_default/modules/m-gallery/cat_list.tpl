<!-- BEGIN: main -->
<!-- BEGIN: cat_title -->
<div style="background:#eee;padding:10px">
	{CAT_TITLE}
</div>
<!-- END: cat_title -->
<!-- BEGIN: data -->
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover">
		<col class="w100" />
		<col span="6" style="white-space: nowrap;" />
		<thead>
			<tr>
				<th class="text-center">{LANG.weight}</th>
				<th class="text-center">{LANG.name}</th>
				<th class="text-center">{LANG.inhome}</th>
			
			
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<!-- BEGIN: loop -->
			<tr>
				<td class="text-center">
				<!-- BEGIN: stt -->
				{STT}
				<!-- END: stt -->
				<!-- BEGIN: weight -->
				<select class="form-control" id="id_weight_{ROW.catid}" onchange="nv_chang_cat('{ROW.catid}','weight');">
					<!-- BEGIN: loop -->
					<option value="{WEIGHT.key}"{WEIGHT.selected}>{WEIGHT.title}</option>
					<!-- END: loop -->
				</select>
				<!-- END: weight -->
				</td>
				<td><a href="{ROW.link}"><strong>{ROW.title}</strong>
				<!-- BEGIN: numsubcat -->
				<span class="red">({NUMSUBCAT})</span>
				<!-- END: numsubcat -->
				</a></td>
				<td class="text-center">
				<!-- BEGIN: disabled_inhome -->
				{INHOME}
				<!-- END: disabled_inhome -->
				<!-- BEGIN: inhome -->
				<select class="form-control" id="id_inhome_{ROW.catid}" onchange="nv_chang_cat('{ROW.catid}','inhome');">
					<!-- BEGIN: loop -->
					<option value="{INHOME.key}"{INHOME.selected}>{INHOME.title}</option>
					<!-- END: loop -->
				</select>
				<!-- END: inhome -->
				</td>
			
				
				<td class="text-center">{ROW.adminfuncs}</td>
			</tr>
			<!-- END: loop -->
		</tbody>
	</table>
</div>
<!-- END: data -->
<!-- END: main -->