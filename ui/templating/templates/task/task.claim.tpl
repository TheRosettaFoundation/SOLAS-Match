{include file="new_header.tpl"}


    {assign var="taskType" value=$task->getTaskType()}
    

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
