        <br/><br/>
        <div class="well">
        <footer>	
            <center>
                <p>
                        &copy; 2012-2013 University of Limerick &middot; <a href="http://www.therosettafoundation.org/">The Rosetta Foundation</a>
                </p>
                <p>
                    Your <a href="https://docs.google.com/a/ul.ie/spreadsheet/viewform?formkey=dER4VFJZQVpNY0g2anpLb2dJSGJEbFE6MQ#gid=0">feedback</a> is appreciated.
                </p>
            </center>
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

