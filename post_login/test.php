  <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
	<script type="text/javascript">
jQuery(function(){
		function deleteItem() {
		    if (confirm("Дали сте сигурни?")) {
		        // your deletion code
		        <?php ?>
		    }
		    return false;
		}
		
			var checkstr =  confirm('Потврди го бришењето');
			if(checkstr == true){
			  var id = $(this).attr("id");
    			//alert(id);
    			
    			$.post('http://kokino.ugd.edu.mk/registracija/post_login/brisi.php', {pateka: "id"}, function(data){
			    	$('#rez').html(data);
			    	//alert(id);
			    });
			    
    			//location.reload();

			}else{
			return false;
			}
			

});
	</script>

	<div id="rez">sdsdsds</div>