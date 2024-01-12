{include file="new_header.tpl"}

    {assign var="task_id" value=$task->getId()}

    {assign var="type_id" value=$task->getTaskType()}

                    
                    



<div class="container-fluid">

    <header class="">

        <div class="container py-2">

                <div class="py-2" >
                    <a  class="text-decoration-none text-dark-subtle"  href="/"> Home </a> <img src="{urlFor name='home'}ui/img/bread.svg" alt="arrow" class="mx-1" >
        
                    <a   href="{urlFor name="task-view" options="task_id.$task_id"}" class="text-primaryDark fw-bold text-decoration-none"> Task </a> <img src="{urlFor name='home'}ui/img/bread.svg" alt="arrow" class="mx-1" >

                    <a class="text-decoration-none text-dark-subtle" href="{urlFor name="task-claim-page" options="task_id.$task_id"}"> Claim </a>
                </div>

        </div>
    

    </header>

<section class="bg-light-subtle my-4"> 

        <div class="container py-5 ">

          <div class="d-flex  flex-wrap justify-content-between">

               <div class="fw-bold primaryDark fs-3">

                {if $task->getTitle() != ''}
                {TemplateHelper::uiCleanseHTMLNewlineAndTabs($task->getTitle())} 
                {else}
                {Localisation::getTranslation('common_task')} {$task->getId()}
                {/if}

                <small>
                <strong>
                     -
                    {assign var="type_id" value=$task->getTaskType()}
                    {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                        {if $type_id == $task_type}
                            <span style="color: {$ui['colour']}">{$ui['type_text']} Task</span>
                        {/if}
                    {/foreach}
                </strong>
                 </small>  

                </div>

             <div class="mt-2 mt-md-0">
           
                {if $task->getTaskStatus() == TaskStatusEnum::PENDING_CLAIM && !$is_denied_for_task && !TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}
                    {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $LINGUIST + $NGO_LINGUIST)) && $user_within_limitations}
                        <a href="{urlFor name="task-claim-page" options="task_id.$task_id"}" class="btn btn-primaryDark shadow text-white">
                        {Localisation::getTranslation('task_view_download_task')} <img src="{urlFor name='home'}ui/img/alarm.svg" alt="alarm-icon" > </a>
                    {/if}
                {/if}
                 {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER))}
                    <a href="{urlFor name="task-alter" options="task_id.$task_id"}" class='pull-right fixMargin btn btn-primary' style="margin-top: -12.5%;margin-right: 45%;">
                        <i class="icon-wrench icon-white"></i> {Localisation::getTranslation('task_view_edit_task_details')}
                    </a>
                {/if}
                
            </div>
       


        </div>

                {if $task->getTaskStatus() > TaskStatusEnum::PENDING_CLAIM}
                <p class="alert alert-info">
                    {Localisation::getTranslation('task_view_0')}
                </p>
                {elseif $is_denied_for_task && $type_id != TaskTypeEnum::TRANSLATION}
                    <p class="alert alert-info">
                        Note: You cannot claim this task, because you have previously claimed the matching translation task.
                    </p>
                {elseif $is_denied_for_task}
                    <p class="alert alert-info">
                        Note: You cannot claim this task, because you have previously claimed the matching revision or proofreading task.
                    </p>
                {/if}
            
                {if isset($flash['success'])}
                    <p class="alert alert-success">
                        <strong>{Localisation::getTranslation('common_success')}:</strong> {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}
                    </p>
                {/if}

                {if isset($flash['error'])}
                    <p class="alert alert-error">
                        <strong>{Localisation::getTranslation('common_warning')}:</strong> {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
                    </p>
                {/if}
        


       </div>


        <div class="container">

        
            <div class="row d-flex justify-content-between ">

                <div class=" col-12  col-md-6 "> 

                         {include file="task/task.details.tpl"} 

                
                </div>

                <div class=" col-12  col-md-4"> 

                                                        
                            {if ($alsoViewedTasksCount>0)}
                            <div class="row"></div>
                                <div>
                                    <div>
                                        <h4 class="fw-bold">{Localisation::getTranslation('users_also_viewed')}</h4>
                                        
                                        {if isset($alsoViewedTasks)}
                                        <div>
                                            <div >
                                                {for $count=0 to $alsoViewedTasksCount-1}
                                                    {assign var="alsoViewedTask" value=$alsoViewedTasks[$count]}
                                                    <div class="">
                                                        {assign var="also_viewed_task_id" value=$alsoViewedTask->getId()}
                                                        {assign var="also_viewed_type_id" value=$alsoViewedTask->getTaskType()}
                                                        {assign var="also_viewed_status_id" value=$alsoViewedTask->getTaskStatus()}
                                                        {assign var="also_viewed_task_title" value=$alsoViewedTask->getTitle()}
                                                        <div class="card mt-4 p-2 fs-5 shadow rounded-1">
                                                            <div class="px-1">
                                                            <a  href="{$siteLocation}task/{$also_viewed_task_id}/view" class="text-decoration-none fw-bold "> <h4>{TemplateHelper::uiCleanseHTMLNewlineAndTabs($also_viewed_task_title)} </h4> </a>
                                                            </div>
                                                         <div class="mt-2 px-1">

                                                                      {if TaskTypeEnum::$enum_to_UI[$also_viewed_type_id]['source_and_target']}
                                                                        <span>
                                                                            {Localisation::getTranslation('common_from')}: <strong>{TemplateHelper::getLanguageAndCountryNoCodes($alsoViewedTask->getSourceLocale())}</strong>
                                                                        </span>

                                                                         <img src="{urlFor name='home'}ui/img/lang_arr.svg" alt="arrow" class="mx-1" >
                                                                     {/if}
                                                                        <span>
                                                                            {Localisation::getTranslation('common_to')}: <strong>{TemplateHelper::getLanguageAndCountryNoCodes($alsoViewedTask->getTargetLocale())}</strong>
                                                                        </span>
                                                         
                                                         
                                                         </div>   
                                                  
                                                        <div class="mt-2 d-flex align-items-center">                                                                
                                
                                                                    <span type="button" class=" ms-1 rounded-pill badge bg-greenish border border-2 border-greenBorder border-opacity-25  text-white font-bold fs-7">{TaskTypeEnum::$enum_to_UI[$also_viewed_type_id]['type_text_short']}</span>

                                                             
                                                                    {if $alsoViewedTask->getWordCount()}
                                                                        <span type="button" class="ms-1 rounded-pill badge bg-quartenary border border-2 border-quartBorder border-opacity-25  text-white font-bold fs-7 ">{$alsoViewedTask->getWordCount()} {TaskTypeEnum::$enum_to_UI[$also_viewed_type_id]['unit_count_text_short']}</span>
                                                                    {/if}
                                                            
                                                        </div>
                                                            <p class="px-1 mt-2">
                                                            <span class="text-muted">Due by </span> <strong><span class="convert_utc_to_local_deadline" style="display: inline-block; visibility: hidden">{$deadline_timestamps[$also_viewed_task_id]}</span></strong>
                                                            </p>
                                                            <p class="px-1">{TemplateHelper::uiCleanseNewlineAndTabs($projectAndOrgs[$also_viewed_task_id])}</p>
                                                        </div>



                                                    </div>
                                                {/for}
                                            </div>
                                        </div>
                                        {/if}
                                        
                                    </div>
                                  
                            {/if}
           
        
                </div>
            
            </div>

        </div>


         

</section>


 <div class="container-sm">

         {if !empty($file_preview_path)}


          <div class="py-4 d-flex  justify-content-between align-items-center flex-wrap"> 
          
          <div class="fw-bold">

                {Localisation::getTranslation('task_view_source_document_preview')} - {TemplateHelper::uiCleanseHTML($filename)}
          
          </div>

          <div class="d-flex ">

                   
                        <img src="{urlFor name='home'}ui/img/print.svg" alt="print" id="print" class="mx-4" >
       
                   

                     <a href="$file_preview_path" download = "{TemplateHelper::uiCleanseHTML($filename)}"> 
                    
                            <img src="{urlFor name='home'}ui/img/download.svg" alt="download" id="download"  >

                     </a>


          
          
          </div>
          
          </div>
         <div style="padding-bottom:56.25%; position:relative; display:block; width: 100%">
            <iframe width="100%" height="100%" id="iframe"
                src="https://docs.google.com/viewer?url={$file_preview_path}&embedded=true"
                frameborder="0" allowfullscreen="" style="position:absolute; top:0; left: 0">
            </iframe>
         </div>

       
        {/if}
 
 
 </div>

 </div>
      

  

       
   
{include file="footer2.tpl"}
