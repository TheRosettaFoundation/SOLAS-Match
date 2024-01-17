


{assign var=task_id value=$task->getId()}

 
    {if isset($flash['error'])}
       
        <div class="page-header">
            <h1>Proofreading and Approval task claimed <small>Please proofread it!</small></h1>
        </div>

        <p class="alert alert-error">
            <strong>{Localisation::getTranslation('common_success')}</strong> {sprintf('You have claimed the Proofreading and Approval task <strong>%s</strong>.', {TemplateHelper::uiCleanseHTML($task->getTitle())})}
        </p>
    {/if}

  <section class="container ">


  <div class="bg-body">

     <div class="d-flex justify-content-between  flex-wrap">

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
       
                  <div class="bg-grayish h-100 text-center d-inline-block ms-4 ">

                        <img src="{urlFor name='home'}ui/img/translator.svg" alt="translator" class="mx-1 object-fit-cover" />

                  </div>

            </div>  

       </div>      

    </section>
</div>

 

