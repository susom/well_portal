<nav>
    <ul>
        <li class="<?php echo $navon["home"]; ?>"><a href="index.php?nav=home" class=""><?php echo lang("NAV_HOME") ?></a></li>
        <li class="<?php echo $navon["reports"]; ?>"><a href="reports.php?nav=reports" class=""><?php echo lang("NAV_REPORTS") ?></a></li>
        <li class="<?php echo $navon["resources"]; ?>"><a href="resources.php?nav=resources" class=""><?php echo lang("NAV_RESOURCES") ?></a></li>
        <li class="<?php echo $navon["activity"]; ?>"><a href="activity.php?nav=activity" class=""><?php echo lang("NAV_ACTIVITY") ?></a></li>
        <li class="<?php echo $navon["rewards"]; ?>"><a href="rewards.php?nav=rewards" class=""><?php echo lang("NAV_REWARDS") ?></a></li>
    </ul>

    <div id="global_points">
        <h4>WELL Points!</h4>
        <a href="game.php" class=""><?php echo !empty($loggedInUser->portal_game_points) ? $loggedInUser->portal_game_points : 0 ?></a>
    </div>
</nav>

<header class="row">
    <a href="#" class="hamburger points_none"></a>
    <a class="title logo"><?php echo lang("WELL_FOR_LIFE") ?></a>

    <a id="account_drop" class="points_none" href="#"><span></span> <?php  echo $loggedInUser->firstname . " " . $loggedInUser->lastname?> <b class="caret"></b></a>
    <ul id="drop_menu">
        <li><a class="" href="profile.php"><?php echo lang("MY_PROFILE") ?></a></li>
        <li><a class="points_none" href="index.php?logout=1"><?php echo lang("LOGOUT") ?></a></li>
    </ul>
</header>

<div class="splash-container">
    <div class="wrapper row">
    <?php
    if(isset($cats[1])){
        if(isset($cats[1]["subject"]) && isset($cats[1]["content"])){
    ?>
    <h2 class="col-sm-12" ><?php echo $cats[1]["subject"]?></h2>
    <blockquote class="col-sm-10 col-sm-offset-1 col-lg-offset-4 col-lg-4">
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


<div class="row" id="gridref">
    <div class="hidden-sm hidden-md col-lg-1">col-lg-1</div>
    <div class="hidden-sm hidden-md col-lg-1">col-lg-2</div>
    <div class="hidden-sm hidden-md col-lg-1">col-lg-3</div>
    <div class="hidden-sm hidden-md col-lg-1">col-lg-4</div>
    <div class="hidden-sm hidden-md col-lg-1">col-lg-5</div>
    <div class="hidden-sm hidden-md col-lg-1">col-lg-6</div>
    <div class="hidden-sm hidden-md col-lg-1">col-lg-7</div>
    <div class="hidden-sm hidden-md col-lg-1">col-lg-8</div>
    <div class="hidden-sm hidden-md col-lg-1">col-lg-9</div>
    <div class="hidden-sm hidden-md col-lg-1">col-lg-10</div>
    <div class="hidden-sm hidden-md col-lg-1">col-lg-11</div>
    <div class="hidden-sm hidden-md col-lg-1">col-lg-12</div>

    <div class="hidden-lg hidden-sm col-md-1">col-md-1</div>
    <div class="hidden-lg hidden-sm col-md-1">col-md-2</div>
    <div class="hidden-lg hidden-sm col-md-1">col-md-3</div>
    <div class="hidden-lg hidden-sm col-md-1">col-md-4</div>
    <div class="hidden-lg hidden-sm col-md-1">col-md-5</div>
    <div class="hidden-lg hidden-sm col-md-1">col-md-6</div>
    <div class="hidden-lg hidden-sm col-md-1">col-md-7</div>
    <div class="hidden-lg hidden-sm col-md-1">col-md-8</div>
    <div class="hidden-lg hidden-sm col-md-1">col-md-9</div>
    <div class="hidden-lg hidden-sm col-md-1">col-md-10</div>
    <div class="hidden-lg hidden-sm col-md-1">col-md-11</div>
    <div class="hidden-lg hidden-sm col-md-1">col-md-12</div>

    <div class="hidden-md hidden-lg col-sm-1">col-sm-1</div>
    <div class="hidden-md hidden-lg col-sm-1">col-sm-2</div>
    <div class="hidden-md hidden-lg col-sm-1">col-sm-3</div>
    <div class="hidden-md hidden-lg col-sm-1">col-sm-4</div>
    <div class="hidden-md hidden-lg col-sm-1">col-sm-5</div>
    <div class="hidden-md hidden-lg col-sm-1">col-sm-6</div>
    <div class="hidden-md hidden-lg col-sm-1">col-sm-7</div>
    <div class="hidden-md hidden-lg col-sm-1">col-sm-8</div>
    <div class="hidden-md hidden-lg col-sm-1">col-sm-9</div>
    <div class="hidden-md hidden-lg col-sm-1">col-sm-10</div>
    <div class="hidden-md hidden-lg col-sm-1">col-sm-11</div>
    <div class="hidden-md hidden-lg col-sm-1">col-sm-12</div>
</div>
<style>
    #gridref {
        display:none;
    }
    #gridref .col-sm-1{
        background:pink;
    }
    #gridref .col-md-1{
        background:gray;
    }
    #gridref div {
        background:yellow;
        min-height:100px;
        position:relative;
        text-align:center;
    }
    #gridref div:after{
        position:absolute;
        content:"";
        top:0px;
        left:0px;
        width:calc(100% - 4px);
        height:96px;
        border:2px solid black;
        z-index:1
    }
</style>