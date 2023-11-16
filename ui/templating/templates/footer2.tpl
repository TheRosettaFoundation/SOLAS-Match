            <br/><br/>
            
            <div>

                <footer>


                            <div>
                                {sprintf(Localisation::getTranslation('footer_maintained_by'), "https://translatorswithoutborders.org", "Translators without Borders")}
                                <br />
                                The platform is hosted by Azure through a donation from Microsoft<br />
                                <a href="https://microsoft.com" target="_blank">
                                    <img alt="Microsoft" style="border-width:0;" src="{urlFor name='home'}ui/img/Microsoft-logo_rgb_c-gray.png" />
                                </a>
                            
                            
                             </div>


                             <div>
                                    <a href="http://github.com/TheRosettaFoundation/SOLAS-Match" target="_blank">
                                    <img alt="Solas Logo" style="border-width:0;" src="{urlFor name='home'}ui/img/logo.png" height="48px" />
                                </a>
                                <br />
                                {sprintf(Localisation::getTranslation('footer_powered_by'), "https://github.com/TheRosettaFoundation/SOLAS-Match", "Solas")}

                             </div>

                            


                    </div>


                      <div class="bg-primary d-flex justify-content-between">

                        <div class="d-flex"> 
                           
                        
                        
                        </div>

                        <div>
                            Contact Us| Terms and Conditions | Privacy Policy | TWB Community

                        <div>


                        <div>

                            Subscribe To TWB Newsletter

                        </div>


                    <div>

                  

                 
                   
                    
                </footer>    
      

                    <script>


                    let light = true ;
                    let theme = document.getElementById("theme");
                    let imgL = document.getElementById('light');
                    let imgN = document.getElementById('night');
                    let navi = document.getElementById("nav") ;
                    let pages = document.querySelectorAll(".page");

                    console.log(pages);                  


                    pages.forEach(page => {
                        let hr = page.href;
                        console.log(hr)
                        page.addEventListener("click", (e)=>{
                         
                        e.preventDefault();
                        console.log("click");
                        requestPage(hr);                      

                        
                    })


                    } )

                    

                      const requestPage = (url) =>{
                       
                        const req = new XMLHttpRequest();
                        req.addEventListener("load", reqListner);
                         req.open("GET" , url , true ) ;
                         req.send();
                                             

                    }

                    function reqListner(){

                        let pages = this.responseText;
                       
                        let tasks = document.querySelectorAll('.tasks');

                        console.log( typeof pages);
                        console.log(pages);
                        test = json.parse(pages);
                        consol.log(test);

                      
                     

                    }
                    
                    

                
                  

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
