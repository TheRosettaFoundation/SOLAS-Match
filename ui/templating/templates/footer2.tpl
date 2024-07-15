    
    </main>
                <footer class="mt-4 relative">


                            <div class="container d-flex flex-wrap mb-4 justify-content-center">

                                
                                <div class="d-flex justify-content-between p-4  ">
                                    <div class="d-flex flex-column align-items-center  ">
                                     <div class="mb-4 ">Maintained by <a href="https://translatorswithoutborders.org/" class="custom-link" target="_blank">Translators without Borders</a></div>
                                    <a href="http://github.com/TheRosettaFoundation/SOLAS-Match" class="text-decoration-none text-body" target="_blank">
                                        <img alt="Solas Logo" style="border-width:0" src="{urlFor name='home'}ui/img/logo.png" height="48px" />
                                        <h1 class="fs-5 text-decoration-none text-secondary"> TWB Platform is powered by <a href="https://github.com/TheRosettaFoundation/SOLAS-Match" class="custom-link">Solas </h1></a>
                                         <a href="https://creativecommons.org/licenses/by-nc-sa/3.0/us/" target="_blank"><img class="wp-image-2357" src="https://translatorswithoutborders.org/wp-content/uploads/2016/04/image001-150x150.png" alt="image001" width="50" height="50"></a>
                                    </div>
                                    <div>

                                

                                    </div>

                                </div>
                              
                            
                            
                             </div>


                                                




                      <div class="bg-primary d-flex justify-content-between  flex-wrap text-white  mt-4 ">

                        <div class="mx-5  mt-2 d-flex align-items-center flex-wrap py-2 py-md-5"> 
                           <div class =" text-white"> Follow Us :</div>
                           <div class ="  mx-2  mt-md-0"> <a href="https://facebook.com/translatorswithoutborders" target ="_blank"> <img alt="" src="{urlFor name='home'}ui/img/facebook.svg" /> </a></div>
                           <div class =" mx-2  mt-md-0">  <a href="https://twitter.com/CLEARGlobalOrg" target ="_blank"> <img alt="" src="{urlFor name='home'}ui/img/x.svg" /> </a></div>
                            <div class =" mx-2  mt-md-0">  <a href="https://www.youtube.com/user/TranslatorsWB" target ="_blank"> <img alt="" src="{urlFor name='home'}ui/img/youtube.svg" /> </a> </div>
                            <div class =" mx-2  mt-md-0">  <a href="https://linkedin.com/company/translators-without-borders" target ="_blank"> <img alt="" src="{urlFor name='home'}ui/img/linkdin.svg" /> </a></div>
                           <div class =" mx-2  mt-md-0">   <a href="https://www.instagram.com/translatorswb/?hl=en" target="_blank" > <img alt="" src="{urlFor name='home'}ui/img/instagram.svg" />  </a></div>
       
                        
                        </div>

                        <div class= "mx-5  mt-2 py-2 py-md-5">
                            <a href="mailto:%69%6e%66%6f@%74%72%61%6e%73%6c%61%74%6f%72%73%77%69%74%68%6f%75%74%62%6f%72%64%65%72%73.%6f%72%67" class="text-decoration-none text-white" target="_blank">Contact Us</a>
                             | 
                            <a href="/static/terms/" class="text-decoration-none text-white" target="_blank" >
                            Terms and Conditions
                            </a> 
                            | <a href="https://twbplatform.org/static/privacy/" class="text-decoration-none text-white" target="_blank"> Privacy Policy </a> 
                            | <a href="https://community.translatorswb.org/" target="_blank" class="text-decoration-none text-white">
                                TWB Community                           
                            </a>

                        </div>

                       

                        <div class="d-flex justify-content-end mt-2  mx-5  py-2 py-md-5">
                             <div  class ="text-end"> 
                             <a href="https://share.hsforms.com/1ctab13J6RHWkhWHLjzk3wQ4fck2?__hstc=84675846.0317038ad40c7930bed861f0514d9b6b.1634021927382.1634021927382.1634309162966.2&amp;__hssc=84675846.1.1634309162966&amp;__hsfp=2187346942" target="_blank" class="text-decoration-none text-white">
                              <img alt="" class="me-2" src="{urlFor name='home'}ui/img/sub.svg" /> Subscribe To TWB Newsletter  </a>  
                              </div>
                            
                        </div>


                    </div>
 
                </footer>  
                </div>
                 <script>

                    // Initializing tooltips

                    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
                    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

                    // Variables for  the theme of the site

                    let navi = document.getElementById("nav");

                    let imgL = document.getElementById("light");
                    let imgN = document.getElementById("night");

                    let light = true;
                    
                    let theme = document.getElementById("theme");

                    let logo = document.querySelector('.logo')

                    let savedTheme = localStorage.getItem('theme');

                    let next= document.querySelector("#next") ;
                    let next1 = document.querySelector("#next2");

                    const downloadFile = document.querySelector("#download-file");


                    let print  = document.querySelector("#print");
                    let downimg = document.querySelector("#downing");

                    if (savedTheme == 'dark') {

                        imgL.classList.add("d-none");
                          imgN.classList.remove("d-none");
                          document.documentElement.setAttribute('data-bs-theme', 'dark')
                          navi.setAttribute('data-bs-theme', 'dark')
                          logo.src = "/ui/img/TWB_Logo1.svg" ;
                          localStorage.setItem('theme', 'dark');
                          if(next && next1){
                                 next.src = "/ui/img/bread.svg" 
                                next1.src = "/ui/img/bread.svg"

                          }
                          if(print){
                               print.src="/ui/img/print.svg" 
                            downing.src="/ui/img/download.svg"


                          }
                        
                        
                    } 
       
                    if(theme){
                    theme.addEventListener("click" , function(e) {
                       
                       light = !light ;       
   
                       if(light){
                        imgL.classList.remove("d-none");
                        imgN.classList.add("d-none");
                        document.documentElement.setAttribute('data-bs-theme', 'light')
                        navi.setAttribute('data-bs-theme', 'light')
                        logo.src = "/ui/img/TWB_Logo.svg" ;
                        localStorage.setItem('theme', 'light');
                        if(next && next1){

                              next.src = "/ui/img/bread.svg"
                            next1.src = "/ui/img/bread.svg"

                        }
                      
                    
                        
                       }
                       else{
                          imgL.classList.add("d-none");
                          imgN.classList.remove("d-none");
                          document.documentElement.setAttribute('data-bs-theme', 'dark')
                          navi.setAttribute('data-bs-theme', 'dark')
                          logo.src = "/ui/img/TWB_Logo1.svg" ;
                          
                            if(next && next1){

                                   next.src = "/ui/img/next-white.svg"
                                next1.src = "/ui/img/next-white.svg"
                                
                            }
  
                           localStorage.setItem('theme', 'dark');
                       }

                       
                    })
                    }
                    
                                      


                    if(print){
                                            
                            const iframe = document.querySelector("#iframe");
               
                            const iframesrc = iframe.src;


                          downing.addEventListener("click", function(){

                            window.open(iframesrc);  

                          })
                           print.addEventListener("click", function(){
                                if(confirm("Are you sure you want to print the document")){
                                    let wind = window.open(iframesrc);  
                                    wind.print();

                             }

                    })

                    }

                    const linkCopy= document.querySelector('#linkcopy') ;

                     const badgeCopy= document.querySelector('#badgecopy') ;

                     const badgeCopy_2 = document.querySelector("#badgecopy_2")


                  
                    const buttonCopy = document.querySelector('#copy-button') ;
                    const buttonBadge = document.querySelector('#badge-button') ;
                    const buttonBagde_2 = document.querySelector('#badge-button_2')


                    if(buttonCopy){
                        buttonCopy.addEventListener("click" , async()=>{
                          let linkText = linkCopy.href ;
                        await navigator.clipboard.writeText(linkText).then(()=>{
                            
                            buttonCopy.textContent = "Copied"
                        })
                    })

                    }

                      
                    if(buttonBagde_2){
                        buttonBagde_2.addEventListener("click" , async()=>{
                          let linkText = badgeCopy_2.href ;
                        await navigator.clipboard.writeText(linkText).then(()=>{
                          
                            buttonBagde_2.textContent = "Copied"
                        })
                    })

                    }
                    
                    if(buttonBadge){

                    buttonBadge.addEventListener("click" , async()=>{
                          let linkText = badgeCopy.href ;
                        await navigator.clipboard.writeText(linkText).then(()=>{
                          
                            buttonBadge.textContent = "Copied"
                        })
                    })



                    }

                
                    
                    </script>

                  
                
                   

                      <script>

                            // Script for setting active navLink 

                            const currentPath = window.location.pathname;
                            const navLinks = document.querySelectorAll(".nav-link");
                            const currentOrigin = window.location.origin;
 
                            navLinks.forEach(link => {

                                let href = link.href ;
                                
                                if(link.pathname === currentPath && href.includes(currentOrigin)){
                                    link.classList.add("activeLink");
                                   
                                }else{
                                    link.classList.remove("activeLink");
                                }
                            })

                            
                     </script>

                     <script>

                        let menu = document.querySelector(".menu_open");
                        let menuList = document.querySelector(".menu_list");
                        if(menu&&menuList){
                            menu.addEventListener("click", function () {
                            menuList.classList.toggle("d-none");
                        });

                        }

                        

                        const areAnyTaskSelected = document.querySelectorAll('input[name="select_task"]') ;
                        

                            
               
                                   
                                    let error = document.querySelector('.error_task');
                        
    

                        const myModalEl_1 = document.getElementById('exampleModalToggle')
                        // let savedPreferences = document.getElementById("saveTaskP");
                        let savedPreferences = document.querySelector("#saveTaskP");
                        console.log(error);
                         
                        if(savedPreferences){
                            console.log(savedPreferences);
                        }

                        console.log(myModalEl_1);
                                myModalEl_1.addEventListener('shown.bs.modal', event => {

                                // do something...
                                    console.log(this) ;
                                   
                                

                                    savedPreferences.addEventListener('click' , event =>{
                                        e.preventDefault();
                                        console.log("clicked on button");
                                        if(areAnyTaskSelected.length){

                                            error.classList.toggle("d-none");

                                        }
                                    })

                                    console.log(areAnyTaskSelected.length);

                                })

                        
                        // const myModalEl_2 = document.getElementById('modal')
                        // myModalEl_2.addEventListener('show.bs.modal', event => {

                        // // do something...
                        //     console.log('showing second modal') ;
                            

                        // })
                                            

                     </script>


                     

   </div>               
 </body>                
                    
                    
</html> 
