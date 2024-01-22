
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


           <section>
     
        <div class="alert alert-success">
            <strong>{Localisation::getTranslation('common_success')}</strong> {Localisation::getTranslation('task_claimed_translation_0')} &ldquo;<strong>{TemplateHelper::uiCleanseHTML($task->getTitle())}</strong>&rdquo;.
        </div>

</section>
 




    </div>


  
   

</header>



<div class="bg-light-subtle py-4">


{assign var=task_id value=$task->getId()}

 

  <section class="container ">
    <div>

    {if isset($flash['error'])}
       
        
        <h4>Proofreading and Approval task claimed <small>Please proofread it!</small></h4>
    

        <p class="alert alert-error">
            <strong>{Localisation::getTranslation('common_success')}</strong> {sprintf('You have claimed the Proofreading and Approval task <strong>%s</strong>.', {TemplateHelper::uiCleanseHTML($task->getTitle())})}
        </p>
    {/if}
    </div>


  <div class="bg-body">

     <div class="d-flex justify-content-between fs-6 flex-wrap">

                <div class="d-flex flex-column justify-content-between flex-grow-1 p-4 ">

                    <div class="flex-grow-1">

                        {assign var="taskTypeId" value=$task->getTaskType()}
                        {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                            {if $taskTypeId == $task_type}
                                {include file=$ui['claimed_template'] task=$task}
                            {/if}
                        {/foreach}

                        

                             
                    </div>

                    <div>
                  

                        <button class="btn btn-grayish fs-6 ">  <img src="{urlFor name='home'}ui/img/download.svg" alt="download-icon" class="me-1" />  ({Localisation::getTranslation('common_cant_find_the_file_on_your_desktop')}
                        {sprintf('Download the <a href="%s">original file</a> in its source language and save it to your desktop.', {urlFor name="download-task" options="task_id.$task_id"})})
                         </button>
  
                  </div>

                </div>
       
                <div class=" h-100 text-center d-inline-block ms-4 ">

                        <img src="{urlFor name='home'}ui/img/languages.svg" alt="languages" class="mx-1 object-fit-cover" />

                </div>

            </div>  

       </div>    

       </div>  

    </section>

</div>




{include file="footer2.tpl"}


 

