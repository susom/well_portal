<!doctype html>
<article>
	<?php
	$empty_flag = True; 
	foreach($cats as $domainEntry){
		if($domainEntry["domain"]-1 == $domain_page){
			$empty_flag=False;
	?> 
			<div class="resourceEntry">
				<?php 
				if(isset($domainEntry["image-catagory"])){
					switch($domainEntry["image-catagory"]){
						case 1:
							echo "<img class='resourcetype' src='assets/img/01image-catagory.png' >";
							break;
						case 2:
							echo "<img class='resourcetype' src='assets/img/02image-catagory.png' >";
							break;
						case 3:
							echo "<img class='resourcetype' src='assets/img/03image-catagory.png' >";
							break;
						case 4:
							echo "<img class='resourcetype' src='assets/img/04image-catagory.png' >";
							break;
						case 5:
							echo "<img class='resourcetype' src='assets/img/05image-catagory.png' >";
							break;
						case 6:
							echo "<img class='resourcetype' src='assets/img/06image-catagory.png' >";
							break;
					}

				}


				echo $domainEntry["pic"];
				?>
				<h2><a href="<?php echo $domainEntry["link"];?>"> <?php echo $domainEntry["link-text"];?></a></h2>
				<p> <?php echo $domainEntry["content"]; ?></p>
				
			</div>
	<?php
		} //if
	}//for
	if($empty_flag){
		echo lang("RESOURCE_NONE");
	}
	?>
</article>

<aside class="sidenav">
	<h4>My Resources</h4>
	<ul>
		<li>
			<img class="sideImages" src="assets/img/00-domain.png">
			<a href="resources.php?nav=resources-0"><?php echo lang("RESOURCE_CREATIVITY") ?></a>
		</li>
		<li>
			<img class="sideImages" src="assets/img/01-domain.png">
			<a href="resources.php?nav=resources-1"><?php echo lang("RESOURCE_LIFESTYLE") ?></a></li>
		<li>
			<img class="sideImages" src="assets/img/02-domain.png">
			<a href="resources.php?nav=resources-2"><?php echo lang("RESOURCE_SOCIAL") ?></a></li>
		<li>
			<img class="sideImages" src="assets/img/03-domain.png">
			<a href="resources.php?nav=resources-3"><?php echo lang("RESOURCE_STRESS") ?></a></li>
		<li>
			<img class="sideImages" src="assets/img/04-domain.png">
			<a href="resources.php?nav=resources-4"><?php echo lang("RESOURCE_EMOTIONS") ?></a></li>
		<li>
			<img class="sideImages" src="assets/img/05-domain.png">
			<a href="resources.php?nav=resources-5"><?php echo lang("RESOURCE_SELF") ?></a></li>
		<li>
			<img class="sideImages" src="assets/img/06-domain.png">
			<a href="resources.php?nav=resources-6"><?php echo lang("RESOURCE_PHYSICAL") ?></a></li>
		<li>
			<img class="sideImages" src="assets/img/07-domain.png">
			<a href="resources.php?nav=resources-7"><?php echo lang("RESOURCE_PURPOSE") ?></a></li>
		<li>
			<img class="sideImages" src="assets/img/08-domain.png">
			<a href="resources.php?nav=resources-8"><?php echo lang("RESOURCE_FINANCIAL") ?></a></li>
		<li>
			<img class="sideImages" src="assets/img/09-domain.png">
			<a href="resources.php?nav=resources-9"><?php echo lang("RESOURCE_RELIGION") ?></a></li>
	</ul>
</aside>



<style>
.resourceEntry{
	display:block;
	width:100%;
	background-color: #f2f2f2;
	margin:10px;
	border-radius: 10px;
	overflow:hidden;
}

.resourceEntry h2 {
	margin:10px 0 0 120px;
}
.resourceEntry p{

}
ul,li{
	list-style: none;
	padding-left: 0;
}
.sideImages{
	max-height: 40px;
	max-width: 40px;
	display:inline-block;
}
.sidenav{
	width: 300px;;
	z-index: 1;
	left:0;
	overflow-x:hidden;
}

.sidenav a{
	font-size:16px;
	color:black;
	display:inline-block;

}
.sidenav a:hover{
	background-color: #f2f2f2;

}
.sidenav li {
	margin-bottom:15px; 
}


article{

	width:70%;
	float:right;
	text-align: center;
}

a{
	display:inline-block;
	padding:5px;

}

.event_img{
	float:left;
	display:inline-block;
	max-width:126px;
	max-height:126px;
	margin:10px;
}
h4{
	color:chocolate;
	text-align:left;
	font-size: 150%;
}
</style>