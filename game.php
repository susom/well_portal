<?php 
require_once("models/config.php"); 
include("models/inc/checklogin.php");
if(!$loggedInUser->portal_wof_unlocked){

    header("location:index.php");
    exit;
}

$navon          = array("home" => "", "reports" => "", "game" => "on", "resources" => "", "rewards" => "", "activity" => "");
$API_URL        = SurveysConfig::$projects["ADMIN_CMS"]["URL"];
$API_TOKEN      = SurveysConfig::$projects["ADMIN_CMS"]["TOKEN"];
$extra_params   = array(
    'content'   => 'record',
    'format'    => 'json',
    "records"   => 9999,
    "fields"    => "wof_quotes"
);
$results        = RC::callApi($extra_params, true, $API_URL, $API_TOKEN);
$quotes         = !empty($results) ? current($results) : array();
$full_json      = isset($quotes["wof_quotes"]) && !is_null($quotes["wof_quotes"]) ? $quotes["wof_quotes"] : "[]";
$quotes_full    = json_decode($full_json,1);
$quotes_pool    = array_column($quotes_full,"quote");

$API_URL        = SurveysConfig::$projects["REDCAP_PORTAL"]["URL"];
$API_TOKEN      = SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"];
$extra_params   = array(
    'content'   => 'record',
    'format'    => 'json',
    "records"   => $loggedInUser->id,
    "fields"    => "portal_wof_solved",
    "events"    => "enrollment_arm_1"
);
$results        = RC::callApi($extra_params, true,  $API_URL, $API_TOKEN);
$quotes         = !empty($results) ? current($results) : array();
$quotes_solved  = !empty($quotes["portal_wof_solved"]) ? json_decode($quotes["portal_wof_solved"],1) : array();
$quotes_mine    = array_column($quotes_solved,"quote");
$available_quotes = array_values(array_diff($quotes_pool,$quotes_mine));

//UPDATE THE SOLVED PUZZLES
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "solved_puzzle"){
    $API_URL    = SurveysConfig::$projects["REDCAP_PORTAL"]["URL"];
    $API_TOKEN  = SurveysConfig::$projects["REDCAP_PORTAL"]["TOKEN"];

    $quote_cite = $quotes_full[array_search($_REQUEST["value"],$quotes_pool)];
    print_rr( $quotes_solved ) ;

    array_push($quotes_solved, $quote_cite);
    $data   = array();
    $data[] = array(
        "record"            => $loggedInUser->id,
        "field_name"        => "portal_wof_solved",
        "value"             => json_encode($quotes_solved),
        "redcap_event_name" => "enrollment_arm_1"
    );
    $result = RC::writeToApi($data, array("overwriteBehavior" => "overwrite", "type" => "eav"), $API_URL, $API_TOKEN);

    print_rr($quote_cite);
    print_rr( $quotes_solved ) ;
    exit;
}

$pageTitle = "Well v2 Game";
$bodyClass = "game";
include_once("models/inc/gl_head.php");
?>
    <div class="main-container">
        <div class="main wrapper clearfix">
            <article>
                <div id="wof_game">
                  <h1 class="title" title="Well Being Paradise!">
                    <span></span>
                    <div class="stats">
                      <div id="guesscount">
                        <h4>Points Leader</h4>
                          <b>?</b>
                      </div>
                      <div id="totalpoints">
                        <h4>Total Prize</h4>
                        <b><?php echo $loggedInUser->portal_game_points; ?></b>
                      </div>
                      <div id="solved">
                        <h4>Puzzles Solved</h4>
                        <b><?php echo count($quotes_solved) ?></b>
                      </div>
                    </div>
                  </h1>
                  
                  <?php
                  if( 1){
                  ?>
                  <div id="gameboard" class="col-sm-6">
                    <div id="board">
                      <a href="#" id="howtoplay" class="points_none">How to Play?</a>
                      <a href="#" id="solveit" class="btn btn-info points_none">I'd like to solve the puzzle!</a>
                      <a href="#" id="newgame" class="btn btn-info points_none">Start New Game</a>
                    </div>
                    <div id="bigwheel" class="centered">
                      <hgroup>
                        <h2>Step 1</h2><h3>Spin the Wheel!</h3>
                      </hgroup>
                      <canvas id="drawing_canvas"></canvas>
                      <div id="status_label">loading...</div>
                    </div>
                    <form id="letterpicker">
                        <hgroup>
                            <h2>Step 2</h2><h3>Pick a letter or Buy a vowel</h3>
                        </hgroup>
                    </form>
                  </div>
                  <?php
                  }
                  ?>
                </div>
            </article>
        </div> <!-- #main -->
    </div> <!-- #main-container -->
<?php 
include_once("models/inc/gl_foot.php");
?>
<script src="//cdnjs.cloudflare.com/ajax/libs/p2.js/0.6.0/p2.min.js"></script>
<script>
  window.activepuzzle;
  window.activepuzzle_raw;

  var spincost  = -500;
  var vowelcost = -250;

  function updateWOFPoints(newpoints){
      $.ajax({
            url : "ajax_handler.php",
            type: 'POST',
            data: "action=persist_points&value="+newpoints,
            datatype: 'JSON',
            success:function(result){
                console.log(result);
                //update the points box
                var result = JSON.parse(result);
                if(result.hasOwnProperty("points_added")){
                    var curpoints = parseInt($("#global_points a").text());
                    curpoints = curpoints + parseInt(result.points_added);
                    $("#totalpoints b,#global_points a").text(curpoints);
                }
            }
      });
      return;
  }

  function makeGameBoard(secretmessage){
    window.activepuzzle_raw = secretmessage;
    secretmessage           = secretmessage.replace(/\./g,'');
    secretmessage           = secretmessage.replace(/\;/g,'');
    secretmessage           = secretmessage.replace(/\,/g,'');
    window.activepuzzle     = secretmessage.replace(/\-/g,' ');
    var words               = window.activepuzzle.split(" ");

    var letters_per_row = 10;
    var tile_width      = 40;

    var msglen          = window.activepuzzle.length;

    //remove old gameboard if there
    $("#flipboard").remove();
    var gameboard = $("<div id='flipboard'></div>");
    $("#board").prepend(gameboard);
    var board_width     = gameboard.width();
    var max_tiles_row   = Math.floor(board_width/tile_width);

    var row_words = {};
    var n         = 0;
    var rowlength = 0;
    var row_chars = {};
    for(var i in words){
        var word        = words[i];
        var wordlen     = word.length;
        rowlength       += wordlen;
        var remainder   = max_tiles_row - rowlength;

        if(remainder < 1){
            rowlength = 0;
            rowlength       += wordlen;
            n++;
        }

        if(!row_words.hasOwnProperty(n)){
            row_words[n] = [];
            row_chars[n] = 0;
        }
        rowlength += 1;
        row_chars[n] += wordlen;
        row_words[n].push(word);
    }

    if(1 == 1) {
        for(var n in row_words){
            var words           = row_words[n];
            var row_chars_cnt   = row_chars[n];
            var filler_cnt      = max_tiles_row - row_chars_cnt - (words.length-1);
            var front_filler    = Math.ceil(filler_cnt/2);
            var back_filler     = Math.floor(filler_cnt/2);

            var row_char_count  = 0;
            for (m in words){
                var word    = words[m];
                var wordlen = word.length;
                for (var i = 0; i < wordlen; i++) {

                    if(m == 0 && i == 0){
                        //first letter of first word
                        for (var x = 0; x < front_filler; x++){
                            row_char_count++;
                            var fc = makeLetterTile("");
                            gameboard.append(fc);
                        }
                    }

                    var fc = makeLetterTile(word[i]);
                    gameboard.append(fc);
                    row_char_count++;

                    if(i == (wordlen - 1)){
                        //last letter
                        var remaining_spaces = back_filler;
                        if(m != (words.length - 1)){
                            //last word
                            remaining_spaces = 1;
                        }

                        if(remaining_spaces > 0){
                            for (var x = 0; x < remaining_spaces; x++){
                                row_char_count++;

                                //add space character if < than max tile count
                                var fc = makeLetterTile("");
                                gameboard.append(fc);
                            }
                        }
                    }
                }
            }
        }
    }

    return;
  }

  function makeLetterTile(theletter){
      var fc = $("<div class='flip-container'></div>");
      var fp = $("<div class='flipper'></div>");
      var fr = $("<div class='front'></div>");
      var ba = $("<div class='back'></div>");

      var letter = theletter.toUpperCase();
      fc.addClass(letter);
      ba.text(letter);
      fp.append(fr).append(ba);
      fc.append(fp);
      if(theletter == "") {
          fr.addClass("space");
      }

      return fc;
  }

  function makeLetterTray(){
    var runes   = ["B","C","D","F","G","H","J","K","L","M","N","P","Q","R","S","T","V","W","X","Y","Z"];
    var vowels  = ["A","E","I","O","U"];
    
    //remove existing old trays.
    $("#lettertray,#voweltray").remove();

    var tray    = $("<div id='lettertray'><h5 id='guessvalue'>Each matching letter will be worth <b>(SPIN!)</b> points</h5></div>");
    for(var i in runes){
      var label   = $("<label>"+runes[i]+"</label>");
      var letter  = $("<input type='checkbox' name='letters' value='"+runes[i]+"'>");
      
      var btn     = $("button[clicked=true]").attr("id");
     
      //MAKING A GUESS
      letter.change(function(){
        if($(this).is(":checked")){
          //REMOVE "SELECTED" CLASS FROM ANY SIBLINGS (NOT ALREADY BURNED)
          
          // if(btn == "solveit"){
          //   if(!$(this).hasClass("picked")){
          //     $(this).find(":input").attr("checked",false);
          //   }
          // }else{
            $(this).parent().siblings().removeClass(function() {
              if(!$(this).hasClass("picked")){
                $(this).find(":input").attr("checked",false);
                return $( this ).attr( "class" );
              }
            });
          // }
          
          $(this).parent().addClass("selected");
        }else{
          $(this).parent().removeClass("selected");
        }  
        return false;
      });

      label.append(letter);
      tray.append(label);
    }
    $("#letterpicker").append(tray);
    tray.append($("<button id='pickit' class='btn btn-info'>Guess Letter</button>"));
    $("#letterpicker").append($("<h6>OR</h6>"));

    var tray    = $("<div id='voweltray'><h5>Reveal all matching vowels for a flat cost of -250 points</h5></div>");
    for(var i in vowels){
      var label   = $("<label>"+vowels[i]+"</label>");
      var letter  = $("<input type='checkbox' name='vowels' value='"+vowels[i]+"'>");
      
      //MAKING A GUESS
      letter.change(function(){
        if($(this).is(":checked")){
          //REMOVE "SELECTED" CLASS FROM ANY SIBLINGS (NOT ALREADY BURNED)
          $(this).parent().siblings().removeClass(function() {
            if(!$(this).hasClass("picked")){
              $(this).find(":input").attr("checked",false);
              return $( this ).attr( "class" );
            }
          });
          $(this).parent().addClass("selected");
        }else{
          $(this).parent().removeClass("selected");
        }  
        return false;
      });

      label.append(letter);
      tray.append(label);
    }
    $("#letterpicker").append(tray);
    tray.append($("<button id='buyit' class='btn btn-info'>Buy a Vowel</button>"));
    return;
  }

  function resetLetters(){
    $(".picked").each(function(){
      var el      = $(this);
      var letter  = el.text();
      el.append($("<input type='checkbox' name='letters' value='"+letter+"'/>"));
      el.removeClass("picked");
    });

    return;
  }

  function revealPuzzle(_cb){
    var elems = count = $(".flip-container").length;

    $(".flip-container").each(function(){
      var el = $(this);
      setTimeout(function(){
        el.addClass("rotate");
      },500);

      if (!--count){
        setTimeout(_cb,3000);
      };
    });
    
  }

  function newGame(phrase){
    if(!phrase){
        alert("Sorry there are no new puzzles at the moment.  Check back soon!");
        return;
    }
    makeGameBoard(phrase);
    makeLetterTray();
  }

  $(document).ready(function(){
    //SET UP THE FIRST PUZZLE AND LETTER TRAY
    var phrasing    = <?php echo json_encode($available_quotes); ?>;
    var phrase      = phrasing.length ?  phrasing.shift() : false;
    newGame(phrase);

    $("#letterpicker button.btn").click(function() {
        $("#letterpicker button.btn").removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

    window.wheelSpun = false;

    //RESOLVING A GUESS
    $("#letterpicker").submit(function(event){
      var btn = $("button[clicked=true]").attr("id"); 

      //CHECK IF GUESSES AVAILABLE
      var curpoints = parseInt($("#totalpoints b").text());
      if(curpoints >= 0){
        //GUESS SUBMITTED, SO LOCK IN GUESS, DISABLE THE INPUT
        if($(this).find(".selected").length){
          $(this).find(".selected").each(function(el){

            var el = $(this);

            //CHECK WHAT CURRENT POINT MULTIPLIER IS
            var pointmult     = parseInt($("#guessvalue b").text());

            var letter_guess  = el.find("input").val();
            el.removeClass("selected");
            el.find("input").attr("checked",false);

            //NOW RESOLVE THE GUESS
            var letters_matched = $(".flip-container."+letter_guess).length;
            var points_earned   = ["A","E","I","O","U"].indexOf(letter_guess) > -1 ? letters_matched : letters_matched * pointmult;

            if(["A","E","I","O","U"].indexOf(letter_guess) > -1){
              if(curpoints < (vowelcost*-1)){
                alert("You don't have enough to buy a vowel");
                return false;
              }
              //vowels should cost
              var points_earned = vowelcost;
            }else{
              if(!window.wheelSpun){
                alert("Spin the Wheel First!");
                return false;
              }
              if(letters_matched){
                PlaySound("assets/sounds/Ding.mp3");
              }else{
                PlaySound("assets/sounds/Buzzer.mp3");
              }
              var points_earned = letters_matched * pointmult;
            }

            el.find("input").remove();
            el.addClass("picked");
            //DECREMENT GUESSES

            
            $("#guessvalue b").text(pointmult);
            $("#lettertray h5 b").text(pointmult);

            updateWOFPoints(points_earned);

            $(".flip-container."+letter_guess).addClass("rotate");
            window.wheelSpun = false;
          });
        }else{
          alert("Pick a Letter First");
        }
      }else{
        alert("No guesses available, answer survey questions to get more guesses!");
      }
      return false;
    });

    $("#solveit").click(function(){
      var solve = prompt("Solve the Puzzle!");
      if (solve.toLowerCase() == window.activepuzzle.toLowerCase()) {
          PlaySound("assets/sounds/solved.mp3");
          spawnPartices();
          statusLabel.innerHTML = 'You\'ve Solved the Puzzle!';

          var solved = parseInt($("#solved b").text() );
          solved++;
          $("#solved b").text(solved);

          revealPuzzle(function(){
            resetLetters();
          });

          $.ajax({
              url : "game.php",
              type: 'POST',
              data: "action=solved_puzzle&value="+window.activepuzzle_raw,
              datatype: 'JSON',
              success:function(result){
                  console.log(result);
              }
          });
      }else{
          PlaySound("assets/sounds/Buzzer.mp3");
          statusLabel.innerHTML = 'Sorry, that is not the right answer.';
      }
      return false;
    });

    $("#newgame").click(function(){
      var phrase = phrasing.length ?  phrasing.shift() : false;
      newGame(phrase);
      return false;
    });

    $("#howtoplay").click(function(){
          $("#dialog .dialog_inner").empty();
          var title   = $("<h3>").html("How to play WELL of Fortune!");
          var p1      = $("<li>").html("First Spin the Wheel! Each spin costs 500 WELL points");
          var p2      = $("<li>").text("Then pick a letter or buy a vowel for 250 WELL points.");
          var p3      = $("<li>").text("Each letter matched will be worth the points spun.");
          var p4      = $("<li>").text("Be careful not to land on Bankrupt!");

          var cancel  = $("<button>").addClass("btn btn-danger").addClass("btn btn-danger").html("Close");
          $("#dialog .dialog_inner").append(title);
          $("#dialog .dialog_inner").append(p1);
          $("#dialog .dialog_inner").append(p2);
          $("#dialog .dialog_inner").append(p3);
          $("#dialog .dialog_inner").append(p4);
          $("#dialog .dialog_inner").append(cancel);
          $("#dialog").addClass("show").addClass("apple");
          return false;
      });
  });


</script>
<link rel="stylesheet" type="text/css" href="assets/css/wheel_of_fortune.css">
<script src="assets/js/wheel_of_fortune.js"></script>
<script>
const TWO_PI  = Math.PI * 2;
const HALF_PI = Math.PI * 0.5;

var colors    = [ "#000000" 
                , "#C080FF"
                , "#FFFF00"
                , "#00C0FF"
                , "#ffffff"
                , "#FF0000"
                , "#C080FF"
                , "#FF80E0"
                , "#00FF00"
                , "#FFC000"
                , "#DFDFDF"
                , "#C080FF"
                , "#00FF00"
                , "#FFFF00"
                , "#FF0000"
                , "#00C0FF"
                , "#FFC000"
                , "#C080FF"
                , "#FFFF00"
                , "#FF80E0"
                , "#FF0000"
                , "#00C0FF"
                , "#00FF00"
                , "#FF80E0"
                ];

var points    = [ "Bankrupt"
                , 100
                , 200
                , 300
                , 400
                , 500
                , 600
                , 700
                , 800
                , 900
                , 1000
                // , 1100
                // , 1200
                // , 1300
                // , 1400
                // , 1500
                // , 170
                // , 18
                // , 19
                // , 20
                // , 21
                // , 22
                // , 23
                ];

// canvas settings
var viewWidth     = 430,
    viewHeight    = 430,
    viewCenterX   = viewWidth * 0.5,
    viewCenterY   = viewHeight * 0.5,
    drawingCanvas = document.getElementById("drawing_canvas"),
    ctx,
    timeStep      = (1/60),
    time          = 0;

var ppm             = 24, // pixels per meter
    physicsWidth    = viewWidth  / ppm,
    physicsHeight   = viewHeight / ppm,
    physicsCenterX  = physicsWidth  * 0.5,
    physicsCenterY  = physicsHeight * 0.5;

var world;

var wheel,
    arrow,
    mouseBody,
    mouseConstraint;

var arrowMaterial,
    pinMaterial,
    contactMaterial;

var wheelSpinning = false,
    wheelStopped  = true;

var particles     = [];

var statusLabel   = document.getElementById('status_label');

window.onload = function() {
    initDrawingCanvas();
    initPhysics();
    requestAnimationFrame(loop);
    statusLabel.innerHTML = '<h4>Click or Drag to give it a good spin!</h4>';
};
</script>