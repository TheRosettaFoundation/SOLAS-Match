
{include file="new_header.tpl"}

{assign var="taskType" value=$task->getTaskType()}

{assign var="task_id" value=$task->getId()}

<header class="">

<div class="container py-2">

         <div class="py-2" >
            <a  class="text-decoration-none text-dark-subtle"  href="/"> Home </a> <img src="{urlFor name='home'}ui/img/bread.svg" alt="arrow" class="mx-1" >
 
            <a  href="{urlFor name="task-view" options="task_id.$task_id"}"  class="text-dark-subtle text-decoration-none"> Task </a> <img src="{urlFor name='home'}ui/img/bread.svg" alt="arrow" class="mx-1" >

            <a class="text-decoration-none text-primaryDark fw-bold" href="{urlFor name="task-claim-page" options="task_id.$task_id"}"> Claim </a>
        </div>


</div>
   

</header>

<div class="bg-light-subtle">

<div class="container-fluid  py-4 mb-2">

          <div class=" container  d-flex py-4  flex-wrap justify-content-between align-items-center">

               <div class="fw-bold">

                {if $task->getTitle() != ''}
                {TemplateHelper::uiCleanseHTML($task->getTitle())}  -
                    {assign var="type_id" value=$task->getTaskType()}
                    {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                        {if $type_id == $task_type}
                            <span style="color: {$ui['colour']}">{$ui['type_text']} Task</span>
                        {/if}
                    {/foreach}
                {else}
                {Localisation::getTranslation('common_task')} {$task->getId()}
                {/if}

                </div>

             <div class="mt-2 mt-md-0">

                              {if $taskType == TaskTypeEnum::PROOFREADING }
           
                                <form class=" fs-5" method="post" action="{urlFor name="task-claim-page" options="task_id.$task_id"}">
                                
                                    {if !empty($matecat_url)}
                                    <a href="{$matecat_url}" class="btn btn-primary" target="_blank">
                                        <i class="icon-th-list icon-white"></i> {Localisation::getTranslation('task_claim_view_on_kato')}
                                    </a>
                                    {/if}

                                    {if !empty($allow_download)}
                                    <a href="{urlFor name="download-task" options="task_id.$task_id"}" class=" btn btn-primary">
                                        <i class="icon-download icon-white"></i> {Localisation::getTranslation('common_download_file')}</a>
                                    {/if}
                              
                                    {if !empty($memsource_task)}
                                    <a href="{urlFor name="download-task" options="task_id.$task_id"}" class=" btn btn-primary">
                                        <i class="icon-download icon-white"></i> Download Original File in its source language</a>
                                    {/if}
                      
                                <p> 
                                <button type="submit" class="btn btn-primary fs-6">
                                   <img src="{urlFor name='home'}ui/img/yes.svg" alt="agree" class="mx-1 fw-bold text-white" /> {Localisation::getTranslation('task_claim_proofreading_5')}
                                </button>
                                <a href="{urlFor name="task-view" options="task_id.$task_id"}" class="btn fs-6 shadow ">
                                    <img src="{urlFor name='home'}ui/img/no.svg" alt="disagree" class="mx-1" /> {Localisation::getTranslation('common_no_just_bring_me_back_to_the_task_page')}
                                </a>
                                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                        </form>

             {elseif $taskType == TaskTypeEnum::TRANSLATION}


                        <form class="d-flex" method="post" action="{urlFor name="task-claim-page" options="task_id.$task_id"}">
                            
                            <div>
                                 <button type="submit" class="btn btn-secondary btn-sm fs-6 fw-bold text-white me-2" >
                                    <img src="{urlFor name='home'}ui/img/yes.svg" alt="agree" class="mx-1" > Yes, I promise I will translate this file
                                 </button>
                            </div>
                            
                            <div>
                                 <a href="{urlFor name="task-view" options="task_id.$task_id"}" class="btn btn-sm btn-light fs-6 shadow fw-bold ">
                                <img src="{urlFor name='home'}ui/img/no.svg" alt="disagree" class="mx-1" > {Localisation::getTranslation('common_no_just_bring_me_back_to_the_task_page')}
                                </a>
                            </div>
                           

                           
                            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
 
             {/if}

            </div>
       


          </div>


       </div>    
    

 <main >

 <div class="container-fluid bg-light-subtle">

        {if $taskType == TaskTypeEnum::SEGMENTATION}
        {include file="task/task.claim-segmentation.tpl"}

    {elseif $taskType == TaskTypeEnum::TRANSLATION}
        {include file="task/task.claim-translation.tpl"}
 
    {elseif $taskType == TaskTypeEnum::PROOFREADING}
        {include file="task/task.claim-proofreading.tpl"}
      
    {elseif $taskType == TaskTypeEnum::DESEGMENTATION}
        {include file="task/task.claim-desegmentation.tpl"}
       
    {elseif $taskType == TaskTypeEnum::APPROVAL}
        {include file="task/task.claim-approval.tpl"}
     
    {/if}
 
 
 </div>

 
 </main>

  

{include file="footer2.tpl"}
