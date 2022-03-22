<!-- BEGIN: main -->
<style>
	ul{
		list-style: none outside none;
		padding-left: 0;
		margin: 0;
	}
	.demo .item{
		margin-bottom: 60px;
	}
	.content-slider li{
		
		text-align: center;
		color: #FFF;
	}
	.content-slider h3 {
		margin: 0;
		padding: 70px 0;
	}
	.demo{
		width: 100%;
	}
</style>
<link rel="stylesheet"  href="{NV_BASE_SITEURL}themes/{TEMPLATE}/modules/{MODULEINFO}/plugins/uploadifive/css/lightslider.css"/>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="{NV_BASE_SITEURL}themes/{TEMPLATE}/modules/{MODULEINFO}/plugins/uploadifive/js/lightslider.js"></script> 
<script>
		$(document).ready(function() {
		$("#content-slider").lightSlider({
			auto:true,
			speed:{DATAGALLERY.speeditem},
			loop:true,
			keyPress:true,
			pauseOnHover: true,
			keyPress: false,
			controls: false,
			pager: false,
			item:{DATAGALLERY.items}
		});
	});
</script>
<div class="demo">
	<div class="item">
		<ul id="content-slider" class="content-slider">
		
		<!-- BEGIN: loop -->
			<!-- BEGIN: pro_img -->
				<li style="width:100%;background-size:cover;">
					<img src="{PRO_IMG.img_large}" alt="{ROW.title}" srcset=""  style="width:100%;">
				</li>
			<!-- END: pro_img -->
		<!-- END: loop -->
		</ul>
	</div>
</div>	

<!-- END: main -->
