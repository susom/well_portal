<!doctype html>
<div class = "sidenav"><!-- 
	<div class = "content-field">
		<img class = "sideImages" src = "assets/img/00-domain.jpg">
		<a href="resources.php?nav=resources-0">Exploration and Creativity</a>
	</div>
	<a href="resources.php?nav=resources-1">Lifestyle Behaviors</a>
	<a href="resources.php?nav=resources-2">Social Connectedness</a>
	<a href="resources.php?nav=resources-3">Stress and Resilience</a>
	<a href="resources.php?nav=resources-4">Experience of Emotions</a>
	<a href="resources.php?nav=resources-5">Sense of Self</a>
	<a href="resources.php?nav=resources-6">Physical Health</a>
	<a href="resources.php?nav=resources-7">Purpose and Meaning</a>
	<a href="resources.php?nav=resources-8">Financial Security</a>
	<a href="resources.php?nav=resources-9">Spirituality and Religion</a> -->
	<li>
		<ul>
			<img class = "sideImages" src = "assets/img/00-domain.jpg">
			<a href="resources.php?nav=resources-0">Exploration and Creativity</a>
		</ul>
		<ul>
			<img class = "sideImages" src = "assets/img/01-domain.jpg">
			<a href="resources.php?nav=resources-1">Lifestyle Behaviors</a></ul>
		<ul>
			<img class = "sideImages" src = "assets/img/02-domain.jpg">
			<a href="resources.php?nav=resources-2">Social Connectedness</a></ul>
		<ul>
			<img class = "sideImages" src = "assets/img/03-domain.jpg">
			<a href="resources.php?nav=resources-3">Stress and Resilience</a></ul>
		<ul>
			<img class = "sideImages" src = "assets/img/04-domain.jpg">
			<a href="resources.php?nav=resources-4">Experience of Emotions</a></ul>
		<ul>
			<img class = "sideImages" src = "assets/img/05-domain.jpg">
			<a href="resources.php?nav=resources-5">Sense of Self</a></ul>
		<ul>
			<img class = "sideImages" src = "assets/img/06-domain.jpg">
			<a href="resources.php?nav=resources-6">Physical Health</a></ul>
		<ul>
			<img class = "sideImages" src = "assets/img/07-domain.jpg">
			<a href="resources.php?nav=resources-7">Purpose and Meaning</a></ul>
		<ul>
			<img class = "sideImages" src = "assets/img/08-domain.jpg">
			<a href="resources.php?nav=resources-8">Financial Security</a></ul>
		<ul>
			<img class = "sideImages" src = "assets/img/09-domain.jpg">
			<a href="resources.php?nav=resources-9">Spirituality and Religion</a></ul>
	</li>
</div>

<div id = "main">
	<?php
	$empty_flag = True; 
	foreach($cats as $domainEntry){
		if($domainEntry["domain"]-1 == $domain_page){
			$empty_flag=False;
	?> 
			<div class = "resourceEntry">
				<figure>
					<?php echo $domainEntry["pic"];?>
				</figure>
				<p> <?php echo $domainEntry["content"]; ?></p>
				<a href="<?php echo $domainEntry["link"];?>">LINK</a>
			</div>
	<?php
		} //if
	}//for
	if($empty_flag){
		echo "No current resources";
	}
	?>
</div>

<style>
.resourceEntry{
	display:block;
	width:100%;
	height:70px;
	font-size: 13px;
}

ul,li{
	list-style: none;
	padding-left: none;
}
.sideImages{
	max-height: 40px;
	max-width: 40px;
	display:inline-block;
	float left;
}
.sidenav{
	width: 300px;;
	z-index: 1;
	left:0;
	overflow-x:hidden;
	padding-top:20px;
	position:fixed;
}

.sidenav a{
	font-size:13px;
	color:black;
	display:inline-block;

}
.sidenav a:hover{
	background-color: #f2f2f2;

}

#main{

	width:70%;
	float:right;
	text-align: center;
}

a{
	display:inline-block;
	padding:5px;

}

figure{
	float:left;
	display:inline-block;
}

</style>