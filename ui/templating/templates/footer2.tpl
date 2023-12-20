            <br/><br/>
            
          

                <footer>


                            <div class="text-center mb-4">
                                {sprintf(Localisation::getTranslation('footer_maintained_by'), "https://translatorswithoutborders.org", "Translators without Borders")}
                                <div class="my-4 py-4">
                                <a href="http://github.com/TheRosettaFoundation/SOLAS-Match" target="_blank">
                                    <img alt="Solas Logo" style="border-width:0" src="{urlFor name='home'}ui/img/logo.png" height="48px" />
                                    <h1 class="fs-5 text-decoration-none text-secondary"> TWB Platform is powered by <a href="https://github.com/TheRosettaFoundation/SOLAS-Match" class="text-primary ">Solas</a> </h1>
                                </div>
                              
                            
                            
                             </div>


                                                




                      <div class="bg-primary d-flex justify-content-between  flex-wrap text-white  mt-4 py-5">

                        <div class="d-md-flex wrap"> 
                           <div class ="mx-md-2 mx-5 text-white"> Follow Us :</div>
                           <div class ="mx-md-2  mx-5 mt-4 mt-md-0"> <a href="https://facebook.com/translatorswithoutborders" target ="_blank"> <img alt="" src="{urlFor name='home'}ui/img/facebook.svg" /> </a></div>
                           <div class ="mx-md-2 mx-5 mt-4 mt-md-0">  <a href="https://twitter.com/translatorswithoutborders" target ="_blank"> <img alt="" src="{urlFor name='home'}ui/img/x.svg" /> </a></div>
                            <div class ="mx-md-2 mx-5 mt-4 mt-md-0">  <a href="https://www.youtube.com/user/TranslatorsWB" target ="_blank"> <img alt="" src="{urlFor name='home'}ui/img/youtube.svg" /> </a> </div>
                            <div class ="mx-md-2 mx-5 mt-4 mt-md-0">  <a href=""https://linkedin.com/company/translators-without-borders" target ="_blank"> <img alt="" src="{urlFor name='home'}ui/img/linkdin.svg" /> </a></div>
                           
       
                        
                        </div>

                        <div class= "mx-5 mx-md-2 mt-4 mt-md-0">
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

                       

                        <div class="d-flex justify-content-end mt-4 mx-5 mx-md-2  mt-md-0">
                             <div  class ="text-end">   <img alt="" class="mx-2" src="{urlFor name='home'}ui/img/sub.svg" /> Subscribe To TWB Newsletter </div>
                            
                        </div>


                    </div>
 
                </footer>    
                
                    <script>

                    // Variables on the theme of the site

                    let pagePosition = {
                        "p":1 ,
                        "tt":0,
                        "sl":0,
                        "tl":0,
                        "prev":0,
                        "next":0
                    } ;

                    let tasksContainer = document.querySelector('.taskPagination');

                    let recents = document.querySelector('.recents') ;
                   
                    let userId =  recents.id ;

                    recents.addEventListener("click", function(e){

                       e.preventDefault();
                       fetchRecents();   

                    })

                  

                    let light = true ;
                    let theme = document.getElementById("theme");

                    let imgL = document.getElementById('light');
                    let imgN = document.getElementById('night');
                    let navi = document.getElementById("nav") ;
                    let pages = document.querySelectorAll(".page");
                   
                    let previous = document.querySelector('#previous');
                    let next = document.querySelector('#next');

                    let last = document.querySelector(".last");
                    let first = document.querySelector(".first");
                    let countPage = document.querySelector(".last").id

                    console.log(`countPage : ${ countPage }`);

                    last.addEventListener('click', function(e){

                        e.preventDefault();

                        let prevPage = document.getElementById(pagePosition.p).parentNode ;
 
                        prevPage.classList.remove('bg-primary' , 'opacity-75', 'text-primary');

                        let url  =`paged/${ countPage }/tt/${ pagePosition.tt }/sl/${ pagePosition.sl }/tl/${ pagePosition.tl }` ;
                        
                        pagePosition.p = countPage ;

                        requestPage(url);   

                        

                    })
                    
                    first.addEventListener('click', function(e){

                        e.preventDefault();

                        let prevPage = document.getElementById(pagePosition.p).parentNode ;

                        console.log('previous page');

                        console.log(prevPage);

                        prevPage.classList.remove('bg-primary' , 'opacity-75', 'text-primary');

                        let firstPage = document.getElementById("1").parentNode ;

                        pagePosition.p = 1 ;

                        if(pagePosition.p == 1){

                             firstPage.classList.add('bg-primary' , 'opacity-75', 'text-primary')
                        
                        }

     
                        let url  = `paged/1/tt/${ pagePosition.tt }/sl/${ pagePosition.sl }/tl/${ pagePosition.tl }` 
                     
                        pagePosition.p = 1 ;

                        requestPage(url);   

                    })

                    let selectedLanguage = document.querySelector("#sourceLanguage");
                    
                    let targetLanguage = document.querySelector("#targetLanguage");
                    let taskType = document.querySelector('#taskTypes') ;
                    let selectL = '' ;
                    let selectT = '' ;
                    let selectTask = '' ;

                    let allPages = document.querySelectorAll('.page')
                    let listPage = document.querySelectorAll('.listPage');
                    selectedLanguage.addEventListener("change", function(){                    
                    let page = document.querySelector(".page");
                    let url = page.href ;
                    pagePosition.sl = this.value ;
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

                        pagePosition.tl = this.value ;
                        console.log(`Value : ${ targetL }`)
                        let find = url.indexOf("tl/") ;
                        let firstL = url.slice(0,find) ;
                        let newUrl = firstL+`tl/${ targetL }` ;
                        allPages.forEach(page=> { page.href = newUrl})
                        
                       

                    })

                     taskType.addEventListener("change", function(){

                        let page = document.querySelector(".page");
                        let url = page.href ;

                        pagePosition.tt = this.value

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

                        let id = page.id ;
                
                        page.addEventListener("click", (e)=>{

                        e.preventDefault();
                          

                        for(var i = 0 ; i<listPage.length ; i++){

                            let pageC = listPage[i].firstElementChild;
                           
                            if(pageC.id == id ) {


                            listPage[i].classList.add('bg-primary' , 'opacity-75', 'text-primary')
                            
                            }else{
                                  listPage[i].classList.remove('bg-primary' , 'opacity-75', 'text-primary')
                            }

                        
                        }

                        if(page.id=="previous"){                            
 
                            requestPage(previous.href);

                            let newPrevPosition = pagePosition.p > 1 ?pagePosition.p-1 : 1 ;
                           
                           
                            pagePosition.prev =newPrevPosition;

                            let prevP = pagePosition.prev ;

                            if(pagePosition.p<=6){

                                 let pagePrev = document.getElementById(prevP).parentNode;

                                  pagePrev.classList.add('bg-primary' , 'opacity-75', 'text-primary')

                            }

                         let newPrevUrl = `paged/${ newPrevPosition }/tt/${ pagePosition.tt }/sl/${ pagePosition.sl }/tl/${ pagePosition.tl }`

                            previous.href = newPrevUrl ;

                            pagePosition.p = newPrevPosition ;

                            console.log(pagePosition);

 
                            } else  if(page.id =="next"){


                            requestPage(next.href);  

                            let newNextPosition = pagePosition.p<= countPage ?pagePosition.p+1 : pagePosition.p;  

                            let newNextUrl =  `paged/${ newNextPosition }/tt/${ pagePosition.tt }/sl/${ pagePosition.sl }/tl/${ pagePosition.tl }`                                                

                            pagePosition.next = newNextPosition;

                            let nextP = pagePosition.next ;
                        
                           if(pagePosition.next <= 6){
 
                        
                            let pageNext = document.getElementById(nextP).parentNode;

                            pageNext.classList.add('bg-primary' , 'opacity-75', 'text-primary')

                           }

                            if(pagePosition.p < countPage){


                                pagePosition.p = pagePosition.p + 1 ;
                            }


                            next.href = newNextUrl ;

  
                            } else {

             
                            pagePosition.p = parseInt(page.id);

                            let newPrevPosition = pagePosition.p > 1 ?pagePosition.p-1 : 1 ;

                            let newNextPosition = pagePosition.p <= countPage? pagePosition.p+1 : pagePosition.p ; 
                          
                            let newPrevUrl = `paged/${ newPrevPosition }/tt/${ pagePosition.tt }/sl/${ pagePosition.sl }/tl/${ pagePosition.tl }`

                            let newNextUrl =  `paged/${ newNextPosition }/tt/${ pagePosition.tt }/sl/${ pagePosition.sl }/tl/${ pagePosition.tl }`
                            

                            
                            if(pagePosition.p == 1){

                                previous.classList.add("disabled") ;
                            } else {

                                previous.classList.remove("disabled");
                        
                            }

                            
                            previous.href = newPrevUrl ;

                            next.href = newNextUrl ;


                            requestPage(hr);

                            }

                        

                })


                    } )

                      const requestPage = (url) =>{
                    
                        const req = new XMLHttpRequest();
                        req.addEventListener("load", reqListner);
                         req.open("GET" , url , true ) ;
                         req.send();
                                             
                    }


                    function displayTasks(pages){

                    
                     let parsed ;   
                     let images;
                     let projects;

                     try{
                               
                                parsed = typeof pages == "string" ? JSON.parse(pages) : pages ;
                     }
                    catch(error)
                        
                        {

                            console.log(` invalid json  ${ error }`)
                        }

  
                        if(parsed.hasOwnProperty("images")){

                              images = parsed.images ;

                        }

                        if(parsed.hasOwnProperty("projects")){

                              projects = parsed.projects ; ;

                        }

                     

                        console.log(`parsed projects`) ;

                        console.log(projects);

                        let newData = document.createElement("div");


                        console.log("displaying new tasks");

                                    

                        for (const item of parsed.tasks) {

                            let taskType = "" ;                            
                            let imageId;
                            let image ;
                            let imageHtml;
 

                    
                            const innerDiv = document.createElement("div");
                            const itemElement = document.createElement('div');
                            itemElement.classList.add(  'mb-4', 'bg-body-tertiary', 'p-3', 'rounded-3');
                            const itemNameElement = document.createElement('div');
                            itemNameElement.classList.add('100') ;

                            const itemFlexContainer = document.createElement ('id')
                            itemFlexContainer.classList.add('d-flex','justify-content-between')
                            const itemSubFlex =  document.createElement('div');
                            const titleContainer = document.createElement('div')
                            const title =  document.createElement('div')
                            title.classList.add('text-primaryDark' ,'d-inline-block')
                            title.textContent = item.title ;
                          
                            const spanTitle = document.createElement('div')
                            const spanImg = document.createElement('img')
                            spanImg.src = "/ui/img/question.svg"
                            spanImg.classList.add("mx-1");
                            spanTitle.appendChild(spanImg);
          
                            title.appendChild(spanImg)

                            titleContainer.classList.add('fw-bold','fs-4', 'd-flex' , 'align-items-center')
                            titleContainer.appendChild(title)


                            const badgeContainer = document.createElement('div')
                            badgeContainer.classList.add('d-flex', 'mt-2', 'mb-2')



                            if(item.taskType == 2){
                                taskType = "Translation"
                            } else if (item.taskType == 3){
                                taskType = "Revision"
                            } else {
                                taskType = "Approval"
                            }

                            const badge = document.createElement('span')
                            badge.classList.add('badge', 'rounded-pill', 'bg-greenish','border', 'border-2', 'border-greenBorder', 'border-opacity-25' , 'text-white', 'text-uppercase','fs-7','font-bold')            
                            badge.textContent = taskType
                            badgeContainer.appendChild(badge)

                            const badgeW = document.createElement('span')
                            badgeW.classList.add('ms-1', 'badge', 'rounded-pill','bg-quartenary', 'border'  , 'border-2', 'border-quartBorder', 'border-opacity-25','text-white','fs-7','font-bold' )
                            
                            badgeW.textContent =`${ item.wordCount } Words`
        
                            badgeContainer.appendChild(badgeW)

                            if(images){

                            imageId  = images[item.id]!== ""?images[item.id] : ""
                        
                            image = imageId.length > 2?  
                            `
                            <div>
                            
                                <div id=""  >
                                    <img style="width:100px ; height:100px"  src= ${ imageId }  class="image" />
                                </div>
                                </div>
        
                            ` : `<div> </div>`;
                            }

                            
                            let time = item.deadline.split(" ")  ;
                            let timezone = Intl.DateTimeFormat().resolvedOptions().timeZone ;

                                         
                            let languages = `<div class="mt-3 mb-3">
                            <span class="mb-1  text-muted">
                                            Languages:  ${ item.sourceLocale.languageName } -  ${ item.targetLocale.languageName }
                                        </span>
                            </div>
                            <div class="text-muted d-flex" > <div> Due by </div> 
                            <strong class="d-flex align-items-center"> <div class="mx-2 "> ${ time[0] } </div>  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#f89406" class="bi bi-clock" viewBox="0 0 16 16" class="mx-1">
                        <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/>
                        </svg>  <div class="mx-2"> ${ time[1] }</div>  </strong>
                        <div class="ms-2"> ${ timezone }</div>
                        
                         </div>
                            
                            `;

                     
                        console.log(timezone) ;
                      

                            if(image){
                             imageHtml =  document.createRange().createContextualFragment(image);
                            }

                            const langHtml = document.createRange().createContextualFragment(languages);
                            let projectItem  = projects? projects[item.id] : ""

                            const viewTask = `<div class ="d-flex justify-content-between align-items-center flex-wrap mt-3">
                                                <div> <span class="text-primaryDark"> ${ projectItem } </span> </div>
                                                <div class="d-flex justify-content-end mt-2 mt-sm-4 mt-md-0">
                                                    <a class="btn btn-secondary fs-5 px-3"  href= "task/${ item.id }/view" target="_blank">View Task</a>
                                                </div>
                    
                                                </div>`;

                            const viewHtml = document.createRange().createContextualFragment(viewTask);
                            itemSubFlex.appendChild(titleContainer);
                            itemFlexContainer.appendChild(itemSubFlex) ;
                            itemSubFlex.appendChild(badgeContainer);
                            itemSubFlex.appendChild(langHtml);

                            if(imageHtml){
                            itemFlexContainer.appendChild(imageHtml);
                            }
                            itemNameElement.appendChild(itemFlexContainer);
                          
                            itemElement.appendChild(itemNameElement);
                            itemElement.appendChild(viewHtml);

                            innerDiv.appendChild(itemElement);
                            

                            newData.appendChild(innerDiv) ;

                        
                                                    
                
                            }
                    
                            newDataString = newData.outerHTML;
                    
                            tasksContainer.innerHTML = newDataString ;



                    }

                     async function fetchRecents(){

                            const fetched =  await fetch(`/user/${ userId }/recent/tasks/`) ;

                            const res =    await fetched.json();

                            displayTasks(res);

                            let page = document.querySelector(".pagination") ;

                            page.remove();
                             
                            
                          
                        }  


                    function reqListner(){

                        let pages = this.response;
                       
                        try {

                                displayTasks(pages) ;
                           
                                  
                            } catch (error) {
                                console.log(error);
                             
                            }

                    }

                    
                    let savedTheme = localStorage.getItem('theme');

                    if (savedTheme == 'dark') {
                            imgL.classList.add("d-none");
                            imgN.classList.remove("d-none");
                           document.documentElement.setAttribute('data-bs-theme', 'dark')
                            navi.setAttribute('data-bs-theme', 'dark')
                              logo.src = "/ui/img/TWB_Logo1.svg" ;
                           localStorage.setItem('theme', 'dark');
                        
                    } else {

                        console.log('light')
                        
                        
                        }
       

                    theme.addEventListener("click" , function(e) {
                       
                       light = !light ;
                       let logo = document.querySelector('.logo')
   
                       if(light){
                        imgL.classList.remove("d-none");
                        imgN.classList.add("d-none");
                        document.documentElement.setAttribute('data-bs-theme', 'light')
                        navi.setAttribute('data-bs-theme', 'light')
                        logo.src = "/ui/img/TWB_Logo.svg" ;
                        localStorage.setItem('theme', 'light');
                        
                       }
                       else{
                          imgL.classList.add("d-none");
                          imgN.classList.remove("d-none");
                           document.documentElement.setAttribute('data-bs-theme', 'dark')
                            navi.setAttribute('data-bs-theme', 'dark')
                              logo.src = "/ui/img/TWB_Logo1.svg" ;
                           localStorage.setItem('theme', 'dark');
                       }

                       
                    })
                   
                    
                    </script>

                  
                 
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
                  
</html> 
