<nav>
    <ul>
        <li class="<?php echo $navon["home"]; ?>"><a href="index.php?nav=home" class=""><?php echo lang("NAV_HOME") ?></a></li>
        <li class="<?php echo $navon["reports"]; ?>"><a href="reports.php?nav=reports" class=""><?php echo lang("NAV_REPORTS") ?></a></li>
        <li class="<?php echo $navon["resources"]; ?>"><a href="resources.php?nav=resources" class=""><?php echo lang("NAV_RESOURCES") ?></a></li>
        <li class="<?php echo $navon["rewards"]; ?>"><a href="rewards.php?nav=rewards" class=""><?php echo lang("NAV_REWARDS") ?></a></li>
    </ul>
</nav>

<div id="global_points">
    <h4>WELL Points!</h4>
    <a href="game.php" class=""><?php echo !empty($loggedInUser->portal_game_points) ? $loggedInUser->portal_game_points : 0 ?></a>
</div>

<style>
    #global_points {
        position:absolute;
        top:5px;
        width: 80px;
        height:70px;
        left:calc(50% + 380px);
        margin-left:-190px;
        background:url(assets/img/gamify_points_frame.png) 50% 5px no-repeat;
        background-size:contain;
        text-align:center;
        z-index:101;
s    }
    #global_points h4 {
        font-size:77%;
        margin:0; padding:0;
        font-weight:bold;
        color:gold;
        text-shadow: 0 1px 1px #000;
    }
    #global_points a {
        display:inline-block;
        height:60px;
        width:60px;
        font-size:130%;
        line-height:250%;
        color:gold;
        text-shadow: 0 1px 1px #000;
    }
    #global_points a:hover {
        background:none;
        text-decoration:none;
    }
</style>
<div class="header-container">
    <header class="wrapper clearfix">
        <h1 class="title"><?php echo lang("WELL_FOR_LIFE") ?></h1>
        <a id="account_drop" class="points_none" href="#"><span></span> <?php  echo $loggedInUser->firstname . " " . $loggedInUser->lastname?> <b class="caret"></b></a>
        <ul id="drop_menu">
            <li><a class="" href="profile.php"><?php echo lang("MY_PROFILE") ?></a></li>
            <li><a class="points_none" href="index.php?logout=1"><?php echo lang("LOGOUT") ?></a></li>
        </ul>
        <a href="#" class="hamburger"></a>
    </header>
</div>
<div class="splash-container">
    <div class="wrapper clearfix">
        <?php  
        if(isset($cats[1])){
            if(isset($cats[1]["subject"]) && isset($cats[1]["content"])){
        ?>
        <h2><?php echo $cats[1]["subject"]?></h2>
        <blockquote>
            <?php echo $cats[1]["content"]?>
        </blockquote>
        <style>
            .splash-container h2:before {
                background: url(<?php echo $cats[1]["pic"] ?>) 50% no-repeat;
                background-size:cover;
            }
        </style>
        <?php 
        }
    }
        ?>
    </div>
</div>