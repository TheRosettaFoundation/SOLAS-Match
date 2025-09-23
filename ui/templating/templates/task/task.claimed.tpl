
{include file="new_header.tpl"}

{assign var="taskType" value=$task->getTaskType()}

{assign var="task_id" value=$task->getId()}

<header class="">

    <div class="container ">

            <div class="py-4" >
                <a  class="text-decoration-none text-body fw-bold"  href="/"> Home </a> <i class="fa-solid fa-chevron-right mx-1"> </i>

                <a  href="{urlFor name="task-view" options="task_id.$task_id"}"  class="text-decoration-none text-body fw-bold"> Task </a> 
        
                <i class="fa-solid fa-chevron-right mx-1"> </i>

                <a class="text-decoration-none text-primaryDark fw-bold" href="{urlFor name="task-claim-page" options="task_id.$task_id"}"> Claim </a>
             
              
            
            </div>

    </div>

      <section class="container">

           
                 <div class="alert alert-success alert-dismissible fade show mt-2  ">
                        <div >
                         {if $taskType == TaskTypeEnum::TRANSLATION}

                        <img src="{urlFor name='home'}ui/img/success.svg" alt="translator" class="mx-1 " />
                        <strong>{Localisation::getTranslation('common_success')}</strong>  {Localisation::getTranslation('task_claimed_translation_0')} &ldquo;<strong>{TemplateHelper::uiCleanseHTML($task->getTitle())}</strong>&rdquo;.
                        {elseif $taskType == TaskTypeEnum::PROOFREADING}
                        
                        <img src="{urlFor name='home'}ui/img/success.svg" alt="translator" class="mx-1 " />
                         <strong>{Localisation::getTranslation('common_success')}</strong> {sprintf(Localisation::getTranslation('task_claimed_proofreading_0'), {TemplateHelper::uiCleanseHTML($task->getTitle())})}

                        {elseif $taskType == TaskTypeEnum::APPROVAL}
                            <img src="{urlFor name='home'}ui/img/success.svg" alt="translator" class="mx-1 " />
                            <strong>{Localisation::getTranslation('common_success')}</strong> {sprintf('You have claimed the Proofreading and Approval task <strong>%s</strong>.', {TemplateHelper::uiCleanseHTML($task->getTitle())})}
                        {elseif $taskType == TaskTypeEnum::SPOT_QUALITY_INSPECTION}
                            <img src="{urlFor name='home'}ui/img/success.svg" alt="translator" class="mx-1 " />
                            <strong>{Localisation::getTranslation('common_success')}</strong> {sprintf('You have claimed the Spot Quality Inspection task <strong>%s</strong>.', {TemplateHelper::uiCleanseHTML($task->getTitle())})}
                        {elseif $taskType == TaskTypeEnum::QUALITY_EVALUATION}
                            <img src="{urlFor name='home'}ui/img/success.svg" alt="translator" class="mx-1 " />
                            <strong>{Localisation::getTranslation('common_success')}</strong> {sprintf('You have claimed the Quality Evaluation task <strong>%s</strong>.', {TemplateHelper::uiCleanseHTML($task->getTitle())})}
                         {/if}

                        </div>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         
                </div>
    </section>
         
       
 
   

</header>

<div class="bg-light-subtle py-4">


{assign var=task_id value=$task->getId()}

 

  <section class="container ">
  


  <div class="bg-body ">

     <div class="d-block d-md-flex justify-content-between fs-5  align-items-center align-middle">

                <div class="d-xs-block d-md-flex flex-column justify-content-between flex-wrap ">

                    <div class="p-4 ">

                        {assign var="taskTypeId" value=$task->getTaskType()}
                        {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                            {if $taskTypeId == $task_type}
                                {include file=$ui['claimed_template'] task=$task}
                            {/if}
                        {/foreach}

                             
                    </div>



                </div>
       
                <div class="h-100 text-center align-middle ms-2  ">

                        <img src="{urlFor name='home'}ui/img/languages.svg" alt="translator" class="mx-1 object-fit-cover" />

                </div>

            </div>  

       </div>    

       </div>  

    </section>



<div class="bg-light-subtle py-4 ps-4 ">

     <section class="container">
        <h3 class="fw-bold">When you have finished translating: </h3>
        <p>
        
            {if isset($user)}
            <a href="{urlFor name="claimed-tasks" options="user_id.{$user->getId()}"}" class="btn btn-light-subtle border border-dark-subtle">
            {else}
            <a href="{urlFor name="home"}" class="btn btn-gray-subtle text-dark fw-bold border border-dark-subtle mt-2 mt-md-0 ">
            {/if}
                <img src="{urlFor name='home'}ui/img/no.svg" alt="back" class="mx-1 " />  {Localisation::getTranslation('common_no_just_bring_me_back_to_claimed_tasks')}
            </a>
            {if $isSiteAdmin}
            <a href="{urlFor name="project-view" options="project_id.{$task->getProjectId()}"}" class="btn btn-gray text-dark fw-bold F">
               <img src="{urlFor name='home'}ui/img/no.svg" alt="back" class="mx-1" /> Just bring me back to the project page.
            </a>
            {/if}
        </p>
    

            <div class="py-2 fs-6">
        
                <div class="">    ({Localisation::getTranslation('common_cant_find_the_file_on_your_desktop')}
                {sprintf('Download the <a href="%s" class="text-primary  fw-bold">original file</a> in its source language and save it to your desktop.', {urlFor name="download-task" options="task_id.$task_id"})})
                </div>

            </div>
    </section>

</div>









{include file="footer2.tpl"}


 

