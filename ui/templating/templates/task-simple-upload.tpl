{include file="header.tpl"}
{assign var="task_id" value=$task->getId()}

{include file="handle-flash-messages.tpl"}

{include file="header.tpl"}

<h1 class="page-header">
    {if $task->getTitle() != ''}
        {$task->getTitle()}
    {else}
        Task {$task->getId()}
    {/if}
    <small>
        <b>
            -
            {assign var="type_id" value=$task->getTaskType()}
            {if $type_id == TaskTypeEnum::TRANSLATION}
                <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">Translation Task
            {elseif $type_id == TaskTypeEnum::PROOFREADING}
                <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">Proofreading Task
            {/if}
        </b>
    </small>   
    {assign var="task_id" value=$task->getId()}
</h1>

{include file="task.details.tpl"}        

<div class="well">

    <div class="page-header">
        <h1>Finished translating? <small>{$filename}</small></h1>
    </div>
    {if isset($upload_error)}
            <div class="alert alert-error">
                    <strong>Upload error</strong> {$upload_error}
            </div>
    {/if}
    <h3>Upload your translated version of {$filename}</h3>
    <form class="well" method="post" action="{urlFor name="task" options="task_id.$task_id"}" enctype="multipart/form-data">
            <input type="hidden" name="task_id" value="{$task->getId()}"/>
            <input type="file" name="{$fieldName}" id="{$fieldName}"/>
            <p class="help-block">
                    Max file size {$max_file_size}MB.
            </p> 
            <button type="submit" value="submit" name="submit" class="btn btn-success"><i class="icon-upload icon-white"></i> Upload</button>
        {if ($converter == "y")}
            <button type="submit" value="XLIFF" name="submit" class="btn btn-success"><i class="icon-upload icon-white"></i> Upload as XLIFF</button>
        {/if}
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
        to re-download the <b>original task file</b>.
    </p> 

    {if ($converter == "y")}
    <p>Click
        <a href="{urlFor name="download-task" options="task_id.$task_id"}?convertToXliff=true">here</a>   
        to re-download the <b>original task file</b> as XLIFF.
    </p>     
    {/if}  

    <p>Click 
        <a href="{urlFor name="download-task-latest-version" options="task_id.$task_id"}">here</a>
        to re-download the <b>latest uploaded file</b>.
    </p> 

    {if ($converter == "y")}
    <p>Click
        <a href="{urlFor name="download-task-latest-version" options="task_id.$task_id"}?convertToXliff=true">here</a>   
        to re-download the <b>latest uploaded file</b> as XLIFF.
    </p>     
    {/if}

</div>

{include file="footer.tpl"}
