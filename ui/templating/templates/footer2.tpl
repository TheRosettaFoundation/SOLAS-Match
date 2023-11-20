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
                    let tasksContainer = document.querySelector('.taskPagination');
                    console.log(tasksContainer)
               


                    pages.forEach(page => {
                        let hr = page.href;
                    
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

                        let pages = this.response;

                        let newData = document.createElement("div");

                        
                        try {
                                 parsed = JSON.parse(pages);

                                 for (const item of parsed) {
                                    console.log(item);
                                  
                                    const itemElement = document.createElement('div');
                                    itemElement.classList.add('d-flex justify-content-between mb-4 bg-body-tertiary p-3 rounded-3');

                                    const cardString  = 
                                    '
                                        <div class=" w-100">
                      
                                        div class="d-flex justify-content-between">
                                        <div class="">
                                        <div class="fw-bold fs-4 align-middle ">
                                        <div id="" href="" class="text-primary d-inline-block">
                                        <span class="fs-5 bg-primary border-2 border-primary opacity-75 rounded-circle d-inline-block px-2 text-white align-self-start">  {title}</span> </div>
                                        </div>

                                        <div class="d-flex mt-2 mb-2 ">
                                            <button class="rounded-5 bg-greenish border border-0  ">  <span class="fs-6 p-1 text-white fw-bold align-middle">{ } </span> </button>
                                                {if $task->getWordCount()}
                                                <button type="button" class=" ms-1 rounded-5  bg-quartenary border border-0 "><div class="fs-6 p-1 text-white fw-bold align-middle"> {}</strong> </div> </button>
                                            

                                        </div>

                                        
                                            <div class="mb-1  text-muted">
                                                <span class=" ">
                                                    Languages: <strong>{} - </strong>
                                                </span>
                               
                                            <span>
                                            <strong>{}</strong>
                                            </span>
                                            <div class="process_deadline_utc" style="visibility: hidden">{}</div>
                                        
                                            
                                            </div>
                                       

                                

                                </div>
                           

                                <div>
                                        <div id="img_{$task_id}"  >
                                            <img src="" style ="width:100px ; height:100px">
                                        </div>
                                   
                                            <div id="" class="" ></div>
                                 

                                </div>
                          

                            
                            </div>
                           
                           


                            
                            <div class ="d-flex justify-content-between align-items-center flex-wrap ">
                                    <div> Translation Project for  <span class="text-primary">Translations without Borders </span></div>
                                     <div class="d-flex justify-content-end">
                                        <a class="btn btn-secondary fs-5 px-3"  href="" target="_blank">View Task</a>
                                     </div>
                            
                            </div>
                            
                           
                           
                        </div>

                        </div> ';

                        console.log(cardString);
                                                         '

                                    const itemNameElement = document.createElement('div');
                                    itemNameElement.textContent = "test";
                                    it
                                    itemElement.appendChild(itemNameElement);
                                    newData.appendChild(itemElement) ;
                                                            
                        
                                    }
                                    console.log(newData)
                                    newDataString = newData.outerHTML;
                                    console.log(newDataString);
                                    tasksContainer.innerHTML = newDataString ;

                            } catch (error) {
                                console.log(error);
                             
                            }

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
