{include file="new_header.tpl"}


{assign var="taskType" value=$task->getTaskType()}


<header class="">

<div class="container py-2">

         <div class="py-2" >
            <a  class="text-decoration-none text-dark-subtle"  href="/"> Home </a> <img src="{urlFor name='home'}ui/img/bread.svg" alt="arrow" class="mx-1" >
 
            <a   href="#" class="text-dark-subtle text-decoration-none"> Task </a> <img src="{urlFor name='home'}ui/img/bread.svg" alt="arrow" class="mx-1" >

            <a class="text-decoration-none text-primaryDark fw-bold" href="{urlFor name="task-claim-page" options="task_id.$task_id"}"> Claim </a>
        </div>


</div>
   

</header>


<div class="container ">

          <div class="d-flex py-4  flex-wrap justify-content-between">

               <div class="fw-bold">

                {if $task->getTitle() != ''}
                {TemplateHelper::uiCleanseHTMLNewlineAndTabs($task->getTitle())}
                {else}
                {Localisation::getTranslation('common_task')} {$task->getId()}
                {/if}

                </div>

             <div class="mt-2 mt-md-0">
           
                {if $task->getTaskStatus() == TaskStatusEnum::PENDING_CLAIM && !$is_denied_for_task && !TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}
                {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $LINGUIST + $NGO_LINGUIST)) && $user_within_limitations}
                    <a href="{urlFor name="task-claim-page" options="task_id.$task_id"}" class="btn btn-primaryDark shadow text-white">
                      {Localisation::getTranslation('task_view_download_task')} <img src="{urlFor name='home'}ui/img/alarm.svg" alt="alarm-icon" > </a>
                {/if}
                {/if}

            </div>
       


          </div>


       </div>    
    

 <main class="container">

 <div class="container">

          {if $taskType == TaskTypeEnum::SEGMENTATION}
        {include file="task/task.claim-segmentation.tpl"}
        <div> SEGM</div>
    {elseif $taskType == TaskTypeEnum::TRANSLATION}
        {include file="task/task.claim-translation.tpl"}
         <div> TRAN</div>
    {elseif $taskType == TaskTypeEnum::PROOFREADING}
        {include file="task/task.claim-proofreading.tpl"}
         <div> PROOFREADING</div>
    {elseif $taskType == TaskTypeEnum::DESEGMENTATION}
        {include file="task/task.claim-desegmentation.tpl"}
         <div> DESEGM</div>
    {elseif $taskType == TaskTypeEnum::APPROVAL}
        {include file="task/task.claim-approval.tpl"}
         <div> APPR</div>
    {/if}
 
 
 </div>

 
 </main>

  

{include file="footer2.tpl"}
