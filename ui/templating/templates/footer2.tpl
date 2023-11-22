            <br/><br/>
            
            <div>

                <footer>


                            <div class="text-center">
                                {sprintf(Localisation::getTranslation('footer_maintained_by'), "https://translatorswithoutborders.org", "Translators without Borders")}
                                <br />
                                The platform is hosted by Azure through a donation from Microsoft<br />
                                <a href="https://microsoft.com" target="_blank">
                                    <img alt="Microsoft" style="border-width:0;" src="{urlFor name='home'}ui/img/Microsoft-logo_rgb_c-gray.png" />
                                </a>
                            
                            
                             </div>


                                                


                    </div>


                      <div class="bg-primary d-flex justify-content-between p-2 flex-wrap text-white">

                        <div class="d-flex"> 
                           <div> Facebook</div>
                           <div> Twitter</div>
                           <div> Youtube</div>
                           
                        
                        
                        </div>

                        <div>
                            Contact Us| Terms and Conditions | Privacy Policy | TWB Community

                        </div>


                        <div class="text-end">

                            Subscribe To TWB Newsletter

                        </div>


                    <div>

 
                </footer>    

                    <script>

                    // Variables on the theme of the site
                    let light = true ;
                    let theme = document.getElementById("theme");

                    let imgL = document.getElementById('light');
                    let imgN = document.getElementById('night');
                    let navi = document.getElementById("nav") ;
                    let pages = document.querySelectorAll(".page");
                    let tasksContainer = document.querySelector('.taskPagination');
                    let previous = document.querySelector('#previous');
                    let previousUrl = previous.href ;
                    let navPage = previousUrl.split('//') ;

                    navPage.shift();
 
                    let selectedLanguage = document.querySelector("#sourceLanguage");
                    
                    let targetLanguage = document.querySelector("#targetLanguage");
                    let taskType = document.querySelector('#taskTypes') ;
                    let selectL = '' ;
                    let selectT = '' ;
                    let selectTask = '' ;

                    let allPages = document.querySelectorAll('.page')
 

                    selectedLanguage.addEventListener("change", function(){
                    
                        let page = document.querySelector(".page");
                        let url = page.href ;
                        selectedL = this.value;
                        console.log(`Value : ${ selectedL }`) 
                        let find = url.indexOf("sl/") ;
                        let findN = url.indexOf("tl") ;

                        let firstL = url.slice(0,find) ;
                        let firstR = url.slice(findN) ;
                        let newUrl = firstL + `sl/${ selectedL }/`+firstR

                         allPages.forEach(page=> {
                            let  firstPart = page.href.split('/tt') ;
                            console.log(firstPart);
                            let endPart = newUrl.split('/tt')
                            let finUrl = firstPart[0]+"/tt"+endPart[1]
                            console.log(finUrl)
                            page.href = finUrl ;
                          })
 
                    })

                     targetLanguage.addEventListener("change", function(){

                        let page = document.querySelector(".page");
                        let url = page.href ;
                        targetL =this.value
                        console.log(`Value : ${ targetL }`)
                        let find = url.indexOf("tl/") ;
                        let firstL = url.slice(0,find) ;
                        let newUrl = firstL+`tl/${ targetL }` ;
                        allPages.forEach(page=> { page.href = newUrl})
                        
                       

                    })

                     taskType.addEventListener("change", function(){

                        let page = document.querySelector(".page");
                        let url = page.href ;

                        selectTask =this.value
                        console.log(`Value : ${ selectTask }`)
                        let find = url.indexOf("tt/") ;
                        let findN = url.indexOf("/sl") ;

                        let firstL = url.slice(0,find) ;
                        let firstR = url.slice(findN) ;
                        let newUrl = firstL+`tt/${ selectTask }`+firstR;
                        allPages.forEach(page=> { page.href = newUrl})

                    })
 
                    pages.forEach(page => {
                        let hr = page.href;
                        console.log(hr)
                    
                        page.addEventListener("click", (e)=>{

                        e.preventDefault();

                      
                        navPage.length > 2 ? navPage.splice(1,1,page.id) : navPage.splice(1,0, page.id)
                        console.log(navPage)
                        let prev = page.id>1 ?page.id-1 : page.setAttribute("disabled",true);
                        let id = page.id>1 ? "/"+prev+"/" : "/"+page.id+ "/";
                   
                        previousUrl = navPage[0]+ id + navPage[2]
                        previous.href = previousUrl;

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
                                    const innerDiv = document.createElement("div");
                                  
                            
                                
                                
                                    const itemElement = document.createElement('div');
                                    itemElement.classList.add(  'mb-4', 'bg-body-tertiary', 'p-3', 'rounded-3');

    
                                 
                                   
                                    const itemNameElement = document.createElement('div');
                                    itemNameElement.classList.add('100') ;
                                    const itemSubFlex =  document.createElement('div');
                                    
                                    
                                   
                                    itemSubFlex.classList.add('d-flex','justify-content-between')
                                    const titleContainer = document.createElement('div')
                                    const title =  document.createElement('div')
                                    title.classList.add('text-primary' ,'d-inline-block')
                                    title.textContent = item.title ;
                                    const spanTitle = document.createElement('span')
                                    spanTitle.classList.add('fs-5', 'bg-primary' , 'border-2' ,'border-primary', 'opacity-75', 'rounded-circle', 'd-inline-block', 'px-2', 'text-white', 'align-self-start')
                                    spanTitle.textContent = "?"
                                    title.appendChild(spanTitle)



                                    titleContainer.classList.add('fw-bold','fs-4', 'align-middle')
                                    titleContainer.appendChild(title)


                                    const badgeContainer = document.createElement('div')
                                    badgeContainer.classList.add('d-flex', 'mt-2', 'mb-2')

                                    const badge = document.createElement('button')
                                    badge.classList.add('rounded-5', 'bg-greenish', 'border', 'border-0')
                                    const badgeSpan = document.createElement('span')
                                    badgeSpan.classList.add('fs-6', 'p-1', 'text-white', 'fw-bold', 'align-middle')
                                    badgeSpan.textContent = "Translation"
                                    badge.appendChild(badgeSpan)
                                    badgeContainer.appendChild(badge)

                                    const badgeW = document.createElement('button')
                                    badgeW.classList.add('ms-1', 'rounded-5', 'bg-quartenary', 'border', 'border-0' )
                                    const badgeDiv = document.createElement('div')
                                    badgeDiv.classList.add('fs-6', 'p-1', 'text-white','fw-bold','align-middle')
                                    badgeDiv.textContent =item.wordCount
                                    badgeW.appendChild(badgeDiv)
                                    badgeContainer.appendChild(badgeW)


                                    let languages = `<div>

                                    <span class="mb-1 text-muted">
                                                    Languages: <strong> Languages :  <strong> ${ item.sourceLocale.languageName } -  ${ item.targetLocale.languageName } </strong>
                                                </span>
                                        
                                                <span>
                                                <strong> Due By </strong>
                                                </span>
                                                <div class="process_deadline_utc" style="visibility: hidden">{$deadline_timestamps[$task_id]}</div>
                                    
                                    </div>`;

                                    const langHtml = document.createRange().createContextualFragment(languages);

                                    const viewTask = `<div class ="d-flex justify-content-between align-items-center flex-wrap ">
                                                        <div> Translation Project for  <span class="text-primary">Translations without Borders </span></div>
                                                        <div class="d-flex justify-content-end">
                                                            <a class="btn btn-secondary fs-5 px-3"  href="" target="_blank">View Task</a>
                                                        </div>
                            
                                                        </div>`;

                                    const viewHtml = document.createRange().createContextualFragment(viewTask);

                                    itemSubFlex.appendChild(titleContainer);
                                    
                                    itemNameElement.appendChild(itemSubFlex)
                                    itemNameElement.appendChild(badgeContainer);
                                    itemNameElement.appendChild(langHtml);
                                  
                                    
                                    itemElement.appendChild(itemNameElement);
                                    itemElement.appendChild(viewHtml);

                                    innerDiv.appendChild(itemElement);
                                    
                                  
                                    newData.appendChild(innerDiv) ;

                                   
                                                            
                        
                                    }
                                    console.log(newData)
                                    newDataString = newData.outerHTML;
                               
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
