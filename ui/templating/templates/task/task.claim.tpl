
{include file="new_header.tpl"}

{assign var="taskType" value=$task->getTaskType()}

{assign var="task_id" value=$task->getId()}

<header class="">

    <div class="container py-2 ">

            <div class="py-2" >
                  <a  class="text-decoration-none text-body fw-bold"  href="/"> Home </a> <i class="fa-solid fa-chevron-right mx-1"> </i>
    
                <a  href="{urlFor name="task-view" options="task_id.$task_id"}"  class="text-decoration-none text-body fw-bold"> Task </a> 
                 
                
                <i class="fa-solid fa-chevron-right mx-1"> </i>

                <a class="text-decoration-none text-primaryDark fw-bold" href="{urlFor name="task-claim-page" options="task_id.$task_id"}"> Claim </a>
              
            </div>

    </div>
   

</header>

<div class="bg-light-subtle py-4 ">

<div class="container-fluid  mb-2">

          <div class="container d-flex py-4  flex-wrap justify-content-between align-items-center">

               <div class="fw-bold mb-sm-2">

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

             <div class="mt-2 mt-md-0 pb-4">

                              {if $taskType == TaskTypeEnum::PROOFREADING }
           
                                <form class="d-flex flex-wrap mt-1 mt-md-0" method="post" action="{urlFor name="task-claim-page" options="task_id.$task_id"}">
                                
                                    {if !empty($matecat_url)}
                                    <a href="{$matecat_url}" class="btn btn-primary" target="_blank">
                                        <i class="icon-th-list icon-white"></i> {Localisation::getTranslation('task_claim_view_on_kato')}
                                    </a>
                                    {/if}

                                    {if !empty($allow_download)}
                                    <a href="{urlFor name="download-task" options="task_id.$task_id"}" class=" btn btn-primary">
                                        <i class="icon-download icon-white"></i> {Localisation::getTranslation('common_download_file')}</a>
                                    {/if}
                              
                                   
                                    <div class="mb-2 md:mb-0">
                                        
                                        <button type="submit" class="btn  btn-primary fs-6 fw-bold text-white me-2 py-1">
                                            <img src="{urlFor name='home'}ui/img/yes.svg" alt="agree" class="mx-1 fw-bold text-white" /> {Localisation::getTranslation('task_claim_proofreading_5')}
                                        </button>
                                    </div>
                                    
                                    <div>

                                        <a href="{urlFor name="task-view" options="task_id.$task_id"}" class="btn btn-light fs-6 fw-bold  me-2 mt-2 mt-md-0 py-1 border border-dark-subtle">
                                            <img src="{urlFor name='home'}ui/img/cancel.svg" alt="disagree" class="mx-1" /> {Localisation::getTranslation('common_no_just_bring_me_back_to_the_task_page')}
                                        </a>
                                    </div>
                                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                        </form>

                    {elseif $taskType == TaskTypeEnum::TRANSLATION}


                        <form class="d-flex flex-wrap " method="post" action="{urlFor name="task-claim-page" options="task_id.$task_id"}">
                            
                            <div class="mb-sm-2">
                                 <button type="submit" class="btn btn-primary fs-6 fw-bold text-white me-2 " >
                                    <img src="{urlFor name='home'}ui/img/yes.svg" alt="agree" class="mx-1" > Yes, I promise I will translate this file
                                 </button>
                            </div>
                            
                            <div>
                                <a href="{urlFor name="task-view" options="task_id.$task_id"}"  class="btn btn-light fs-6 fw-bold  me-2 border border-dark-subtle ">
                                <img src="{urlFor name='home'}ui/img/cancel.svg" alt="disagree" class="me-1" > {Localisation::getTranslation('common_no_just_bring_me_back_to_the_task_page')}
                                </a> 
                              
                            </div>
                           

                           
                            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                        </form>
            {elseif $taskType == TaskTypeEnum::APPROVAL}
                 <form class="d-flex flex-wrap " method="post" action="{urlFor name="task-claim-page" options="task_id.$task_id"}">
                            <div class="mb-sm-2">
                                 <button type="submit" class="btn btn-primary fs-6 fw-bold text-white me-2 " >
                                    <img src="{urlFor name='home'}ui/img/yes.svg" alt="agree" class="mx-1" > Yes, I promise I will proofread this file
                                 </button>
                            </div>
                            <div>
                                <a href="{urlFor name="task-view" options="task_id.$task_id"}"  class="btn btn-light fs-6 fw-bold  me-2 border border-dark-subtle ">
                                <img src="{urlFor name='home'}ui/img/cancel.svg" alt="disagree" class="me-1" > {Localisation::getTranslation('common_no_just_bring_me_back_to_the_task_page')}
                                </a>
                            </div>
                            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                        </form>
            {elseif $taskType == TaskTypeEnum::SPOT_QUALITY_INSPECTION}
                 <form class="d-flex flex-wrap " method="post" action="{urlFor name="task-claim-page" options="task_id.$task_id"}">
                            <div class="mb-sm-2">
                                 <button type="submit" class="btn btn-primary fs-6 fw-bold text-white me-2 " >
                                    <img src="{urlFor name='home'}ui/img/yes.svg" alt="agree" class="mx-1" > Yes, I promise I will spot quality inspect this file
                                 </button>
                            </div>
                            <div>
                                <a href="{urlFor name="task-view" options="task_id.$task_id"}"  class="btn btn-light fs-6 fw-bold  me-2 border border-dark-subtle ">
                                <img src="{urlFor name='home'}ui/img/cancel.svg" alt="disagree" class="me-1" > {Localisation::getTranslation('common_no_just_bring_me_back_to_the_task_page')}
                                </a>
                            </div>
                            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                        </form>
            {elseif $taskType == TaskTypeEnum::QUALITY_EVALUATION}
                 <form class="d-flex flex-wrap " method="post" action="{urlFor name="task-claim-page" options="task_id.$task_id"}">
                            <div class="mb-sm-2">
                                 <button type="submit" class="btn btn-primary fs-6 fw-bold text-white me-2 " >
                                    <img src="{urlFor name='home'}ui/img/yes.svg" alt="agree" class="mx-1" > Yes, I promise I will quality evaluate this file
                                 </button>
                            </div>
                            <div>
                                <a href="{urlFor name="task-view" options="task_id.$task_id"}"  class="btn btn-light fs-6 fw-bold  me-2 border border-dark-subtle ">
                                <img src="{urlFor name='home'}ui/img/cancel.svg" alt="disagree" class="me-1" > {Localisation::getTranslation('common_no_just_bring_me_back_to_the_task_page')}
                                </a>
                            </div>
                            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                        </form>
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
        {elseif $taskType == TaskTypeEnum::SPOT_QUALITY_INSPECTION}
            {include file="task/task.claim-spot_quality_inspection.tpl"}
        {elseif $taskType == TaskTypeEnum::QUALITY_EVALUATION}
            {include file="task/task.claim-quality_evaluation.tpl"}
    {/if}
 </div>
 </main>

{include file="footer2.tpl"}
