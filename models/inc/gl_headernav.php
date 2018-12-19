<nav>
    <ul>
        <li class="<?php echo $navon["home"]; ?>"><a href="index.php?nav=home"><?php echo lang("NAV_HOME") ?></a></li>
        <li class="<?php echo $navon["reports"]; ?>"><a href="reports.php?nav=reports"><?php echo lang("NAV_REPORTS") ?></a></li>
        <li class="<?php echo $navon["resources"]; ?>"><a href="resources.php?nav=resources"><?php echo lang("NAV_RESOURCES") ?></a></li>
        <!-- <li class="<?php echo $navon["rewards"]; ?>"><a href="rewards.php?nav=rewards"><?php echo lang("NAV_REWARDS") ?></a></li> -->
        <li class="<?php echo $navon["activity"]; ?>"><a href="activity.php?nav=activity"><?php echo lang("NAV_ACTIVITY") ?></a></li>
        <!-- <li class="<?php echo $navon["game"]; ?>"><a href="game.php?nav=game"><?php echo lang("NAV_GAME") ?></a></li> -->

    </ul>
</nav>
<div class="header-container">
    <header class="wrapper clearfix">
        <h1 class="title"><?php echo lang("WELL_FOR_LIFE") ?></h1>
        <a id="account_drop" href="#"><span></span> <?php  echo $loggedInUser->firstname . " " . $loggedInUser->lastname?> <b class="caret"></b></a>
        <ul id="drop_menu">
            <li><a href="profile.php"><?php echo lang("MY_PROFILE") ?></a></li>
            <li><a href="index.php?logout=1"><?php echo lang("LOGOUT") ?></a></li>
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