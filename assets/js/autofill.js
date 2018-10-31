var required_fields = $("#customform section.active .required");

function makeRandomString(strlength) {
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789 ";

    for (var i = 0; i < strlength; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}


required_fields.each(function(){
    if( $(this).is(":visible") ){
      var inputs     = $(this).find(":input");
      var input_type = inputs.attr("type");
      var inp_select = inputs[0].nodeName.toLowerCase();

      if (inp_select == "select" && (inputs.val() == "-" || inputs.val() == "")) {
         var options = inputs.find("option");

         var randomNumber = Math.floor(Math.random()* options.length);
         console.log(options[randomNumber].value);
         inputs.val(options[randomNumber].value).trigger("change");
         return;
      }

      if(inputs.is(':radio') && $(this).find(":input:checked").length == 0 ){
          //pick random radio
          var randomNumber = Math.floor(Math.random()* inputs.length);
          $(inputs[randomNumber]).attr("checked",true).trigger("change");
          return;
      }

      if(inputs.is(':text') && inputs.val().length == 0 ){
          var randomNumber = Math.floor(Math.random()*30);
         inputs.val(makeRandomString(randomNumber)).trigger("change");

      }
    }
});