</div>
</div>
</body>
</html>
<script>
// THIS IS FOR THE PRE PORTAL CODE login,setup,consent, cms etc
$(document).on('click', function(event) {
	if ($(event.target).closest('.alert').length) {
		$(".alert").fadeOut("fast",function(){
			$(".alert").remove();
		});
	}
  	
});
</script>
