        <br/><br/>
        <div class="well">
        <footer>	
            <center>
                <p>
                        &copy; 2012-2013 University of Limerick &middot; <a href="http://www.therosettafoundation.org/">The Rosetta Foundation</a>
                </p>
                <p>
                    <a href = "mailto:%74%72%6F%6D%6D%6F%6E%73%40%74%68%65%72%6F%73%65%74%74%61%66%6F%75%6E%64%61%74%69%6F%6E%2E%6F%72%67">Contact Us</a> | <a href="#">Terms of Use</a> | <a href="#">Privacy Statement</a> | <a href="http://forum.solas.uni.me/" target="_blank">Community Forum</a>
                </p>
                <p>
                    Your <a href="https://docs.google.com/spreadsheet/viewform?fromEmail=true&formkey=dEdyWnJnbnF0TVJhTHFLb0IyWVdhWEE6MQ">feedback</a> is appreciated.
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

