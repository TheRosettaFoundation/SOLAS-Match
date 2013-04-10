{include file="header.tpl"}

{assign var="taskId" value=$task->getId()}

{include file="handle-flash-messages.tpl"}


    <h1 class="page-header">
        {$task->getTitle()}
        <small>
            <strong>
                - <span style="color: {$taskTypeColours[TaskTypeEnum::DESEGMENTATION]}">Desegmentation Task
            </strong>
        </small>   
        {assign var="task_id" value=$task->getId()}
    </h1>

{include file="task.details.tpl"}        

    <div class="well">
        <div class="page-header">
            <h1>
                {$task->getTitle()} <small>Post-Editing task details</small>
                <a href="{urlFor name="task-user-feedback" options="task_id.$task_id"}" style="float: right" class="btn btn-success">
                    <i class="icon-upload icon-white"></i> Provide Feedback
                </a>
            </h1>
        </div>

        {if $errorMessage}
            <p class="alert alert-error">{$errorMessage}</p>
        {/if}

        <h2>Download:</h2>
        <p>Download the following files and merge them:</p>

        {foreach from=$preReqTasks item=pTask}
            {assign var="pTaskId" value=$pTask->getId()}
            <p>Download {$pTask->getTitle()} <a href="{urlFor name="download-task-latest-version" options="task_id.$pTaskId"}">here</a></p>
        {/foreach}
        <p style="margin-bottom: 40px"/>

        <h2>Upload the merged file here:</h2>
        <form class="well" method="post" enctype="multipart/form-data" action="{urlFor name="task-desegmentation" options="task_id.$taskId"}">
            <p><input type="file" name="{$fieldName}" id="{$fieldName}" /></p>
            <p><button type="submit" class="btn btn-success"><i class="icon-upload icon-white"></i> Upload</button>
        </form>

        {if isset($file_previously_uploaded) && $file_previously_uploaded}
            <br />
            <div class="alert">
                <p>Thanks for providing your translation for this task. 
                {if $org != null && $org->getName() != ''}
                    {$org->getName()}
                {else}
                    This organisation
                {/if}
                will be able to use it for their benefit.</p>
                <p><strong>Warning! </strong>Uploading a new version of the file will overwrite the old one.</p>
            </div>
        {/if}

        <h3>Can't find the task file? <small>Misplaced the original file or the latest uploaded file?</small></h3>
        <br />
        <p>Click 
            <a href="{urlFor name="download-task" options="task_id.$task_id"}">here</a>
            to re-download the <strong>original task file</strong>.
        </p> 

        {if ($converter == "y")}
        <p>Click
            <a href="{urlFor name="download-task" options="task_id.$task_id"}?convertToXliff=true">here</a>   
            to re-download the <strong>original task file</strong> as XLIFF.
        </p>     
        {/if}  

        <p>Click 
            <a href="{urlFor name="download-task-latest-version" options="task_id.$task_id"}">here</a>
            to re-download the <strong>latest uploaded file</strong>.
        </p> 

        {if ($converter == "y")}
        <p>Click
            <a href="{urlFor name="download-task-latest-version" options="task_id.$task_id"}?convertToXliff=true">here</a>   
            to re-download the <strong>latest uploaded file</strong> as XLIFF.
        </p>     
        {/if}
    </div>

{include file="footer.tpl"}
