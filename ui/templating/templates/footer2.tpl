            <br/><br/>
            
            <div class="well pull-left" style="display:none;">
                <footer>
                    <table>
                        <tr>
                            <td width="31%" style="text-align: center">
                                <a rel="license" href="http://creativecommons.org/licenses/by/3.0/ie/">
                                    <img alt="Creative Commons Licence" style="border-width:0" src="{urlFor name='home'}ui/img/88x31.png" />
                                </a>
                                <br />
                                {sprintf(Localisation::getTranslation('footer_0'), "http://creativecommons.org/licenses/by/3.0/ie/", {urlFor name='terms'})}
                            </td>
                            <td width="38%" style="text-align: center">
                                {sprintf(Localisation::getTranslation('footer_maintained_by'), "https://translatorswithoutborders.org", "Translators without Borders")}
                            </td>
                            <td width="31%" style="text-align: center">
                            	<a href="http://github.com/TheRosettaFoundation/SOLAS-Match" target="_blank">
                                    <img alt="Solas Logo" style="border-width:0" src="{urlFor name='home'}ui/img/logo.png" height="48px" />
                                </a>
                                <br />
                                {sprintf(Localisation::getTranslation('footer_powered_by'), "https://github.com/TheRosettaFoundation/SOLAS-Match", "Solas")}
                            </td>
                        </tr>
                    </table>
                    <div id="footer-menu">
                        {mailto address={Settings::get("site.system_email_address")} encode='hex' text={Localisation::getTranslation("footer_contact_us")}} |
                        <a href="{urlFor name='terms'}">
                            {Localisation::getTranslation('footer_terms_and_conditons')}
                        </a> |
                        <a href="https://twbplatform.org/static/privacy/">
                            {Localisation::getTranslation('footer_privacy_policy')}
                        </a>
                        {if Settings::get('site.forum_enabled') == 'y'}
                            | <a href="{Settings::get('site.forum_link')}" target="_blank">
                                {Localisation::getTranslation('common_forum')}
                            </a>
                        {/if}
                    </div>
                    
                </footer>    
            </div>
        </div>
         <!-- Container -->
        <div class="row-fluid">
        <div class="span4" style="text-align: center;margin-left:-880px;">
         <a href="https://facebook.com/translatorswithoutborders" target="_blank" class="fa fa-facebook"></a>
         <a href="https://www.instagram.com/translatorswb/?hl=en" target="_blank" class="fa fa-instagram"></a>
         <a href="https://linkedin.com/company/translators-without-borders" target="_blank" class="fa fa-linkedin"></a>
         <a href="https://twitter.com/TranslatorsWB" target="_blank" class="fa fa-twitter" style="background: #55ACEE;color: white;"></a>
         <a href="https://www.youtube.com/user/TranslatorsWB" target="_blank" class="fa fa-youtube"></a>
         <br/>
         <a href="https://share.hsforms.com/1ctab13J6RHWkhWHLjzk3wQ4fck2?__hstc=84675846.0317038ad40c7930bed861f0514d9b6b.1634021927382.1634021927382.1634309162966.2&__hssc=84675846.1.1634309162966&__hsfp=2187346942" target="_blank" class="btn btn-primary">Subscribe to TWB Newsletter</a>

           
        </div>
        <div class="span4" style="text-align: center;margin-left:-430px;margin-top:15px;">
        <br />
        <br />
        {sprintf(Localisation::getTranslation('footer_maintained_by'), "https://translatorswithoutborders.org", "Translators without Borders")}
        <br />
        The platform is hosted by Azure through a donation from Microsoft<br />
        <a href="https://microsoft.com" target="_blank">
            <img alt="Microsoft" style="border-width:0;" src="{urlFor name='home'}ui/img/Microsoft-logo_rgb_c-gray.png" />
        </a>
        </div>
        <div class="span4" style="text-align: center;margin-left:895px;margin-top:-25px;">
                                	<a href="http://github.com/TheRosettaFoundation/SOLAS-Match" target="_blank">
                                    <img alt="Solas Logo" style="border-width:0;" src="{urlFor name='home'}ui/img/logo.png" height="48px" />
                                </a>
                                <br />
                                {sprintf(Localisation::getTranslation('footer_powered_by'), "https://github.com/TheRosettaFoundation/SOLAS-Match", "Solas")}
        </div>
        </div>
    
<br/>
             <div id="footer-menu">
                        {mailto address={Settings::get("site.system_email_address")} encode='hex' text={Localisation::getTranslation("footer_contact_us")}} |
                        <a href="{urlFor name='terms'}">
                            {Localisation::getTranslation('footer_terms_and_conditons')}
                        </a> |
                        <a href="https://twbplatform.org/static/privacy/">
                            {Localisation::getTranslation('footer_privacy_policy')}
                        </a>
                        {if Settings::get('site.forum_enabled') == 'y'}
                            | <a href="{Settings::get('site.forum_link')}" target="_blank">
                                {Localisation::getTranslation('common_forum')}
                            </a>
                        {/if}
                        <a href="https://creativecommons.org/licenses/by-nc-sa/3.0/us/" target="_blank"><img style="margin-top: -30px;" class="wp-image-2357 pull-right" src="https://translatorswithoutborders.org/wp-content/uploads/2016/04/image001-150x150.png" alt="image001" width="50" height="50"></a>
             </div>

                
                    <br/>
                    <br/>
                    <script>

                    
                    localStorage.setItem("tasks" , {$all_tasks});
                    console.log ({$all_tasks});
                
                    

                
                    
                    </script>
                    <script>


                    let light = true ;
                    let theme = document.getElementById("theme");
                    let imgL = document.getElementById('light');
                    let imgN = document.getElementById('night');
                    let navi = document.getElementById("nav") ;
                    let pages = document.querySelectorAll(".page");

                    console.log(pages);

                    const requestPage = (url) =>{

                        const req = new XMLHttpRequest();
                         
                         req.onreadystatechange = receivedTasks ;
                         req.open("GET" , url , true ) ;
                         req.send();
                                             

                    }

                    const receivedTasks = ()=>{
                        if(this.readyState == 4){
                            if(this.status === 200) {
                                console.log("response succeed")
                            }
                            else{
                                console.log("Response failed");
                            }
                        }
                    }

                    pages.forEach(page =>{
                        page.addEventListener("click" , (e)=>{
                            e.preventDefault();
                            let url  = page.href;
                           requestPage(url);
                            
                        })
                    })

                    
                    

                
                  

                    theme.addEventListener("click" , function(e) {
                       
                       light = !light ;
                       console.log(light);

                       if(light){
                        imgL.classList.remove("d-none");
                        imgN.classList.add("d-none");
                        document.documentElement.setAttribute('data-bs-theme', 'light')
                        navi.setAttribute('data-bs-theme', 'light')
                        
                       }
                       else{
                          imgL.classList.add("d-none");
                          imgN.classList.remove("d-none");
                           document.documentElement.setAttribute('data-bs-theme', 'dark')
                            navi.setAttribute('data-bs-theme', 'dark')
                       }

                       
                    })
                   
                    
                    </script>

                 
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
                    <script src="https://unpkg.com/htmx.org@1.9.6" integrity="sha384-FhXw7b6AlE/jyjlZH5iHa/tTe9EpJ1Y55RjcgPbjeWMskSxZt1v9qkxLJWNJaGni" crossorigin="anonymous"></script>
    </body>  
</html> 
