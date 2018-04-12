</div>
</div>
</body>
</html>
<script>
$(document).on('click', function(event) {
	if ($(event.target).closest('.alert').length) {
		$(".alert").fadeOut("fast",function(){
			$(".alert").remove();
		});
	}
  	
});
</script>
