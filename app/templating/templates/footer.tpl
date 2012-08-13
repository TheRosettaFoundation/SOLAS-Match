<hr>
<footer>	
	<p>
		&copy; 2012 University of Limerick &middot; <a href="http://www.therosettafoundation.org/">The Rosetta Foundation</a>
	</p>
</footer>
</div><!-- /container -->
{if isset($openid)&& ($openid==='y'||$openid==='h' )}
    <script type="text/javascript">
        $(window).load(function() {
                openid.init('openid_identifier');
                openid.setDemoMode(false); //Stops form submission for client javascript-only test purposes
        });
    </script>
{/if}
</body>  
</html> 
