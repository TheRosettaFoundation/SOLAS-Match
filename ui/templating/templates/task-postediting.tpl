{include file="header.tpl"}

{assign var="taskId" value=$task->getId()}

{include file="handle-flash-messages.tpl"}


<h1 class="page-header">
    {$task->getTitle()}
    <small>
        <b>
            - <span style="color: {$taskTypeColours[TaskTypeEnum::POSTEDITING]}">Postediting Task
        </b>
    </small>   
    {assign var="task_id" value=$task->getId()}
</h1>
        
        
<table class="table table-striped">
    <thead>            
        <th style="text-align: left"><b>Project</b></th>

        <th><b>Source Language</b></th>
        <th><b>Target Language</b></th>
        <th><b>Created</b></th> 
        <th><b>Task Deadline</b></th>
        <th><b>Word Count</b></th>
    </thead>
    <tbody>
        <tr>
            <td style="text-align: left">
                {if isset($project)}
                    {assign var="projectId" value=$project->getId()}
                    <a href="{urlFor name="project-view" options="project_id.$projectId"}">
                        {$project->getTitle()}
                    </a>
                {/if}
            </td>

            <td>
                {TemplateHelper::getTaskSourceLanguage($task)} 
            </td>
            <td>
                {TemplateHelper::getTaskTargetLanguage($task)}
            </td>
            <td>
                {date("D dS, M Y", strtotime($task->getCreatedTime()))}
            </td>
            <td>
                {date("D dS, M Y", strtotime($task->getDeadline()))}
            </td>
            <td>
                {if $task->getWordCount() != ''}               
                    {$task->getWordCount()}
                {else}
                    -
                {/if}
            </td> 
        </tr> 
    </tbody>
</table>        
      
<div class="well">
    <table width="100%">
        <thead>
        <th width="48%" align="left">Task Comment:<hr/></th>
        <th></th>
        <th width="48%" align="left">Project Description:<hr/></th>
        </thead>
        <tbody>
            <tr>
                <td>
                    <i>
                    {if $task->getComment() != ''}
                        {$task->getComment()}
                    {else}
                       No comment has been added.
                    {/if}
                    </i>
                </td>
                <td></td>
                <td>
                    <i>
                    {if $project->getDescription() != ''}
                        {$project->getDescription()}
                    {else}
                        No description has been added.
                    {/if}
                    </i>
                </td>
            </tr>
        </tbody>
    </table>
</div>
                    
<div id="debug">
     
</div>

<div class="well">

    <div class="page-header">
        <h1>{$task->getTitle()} <small>Post-Editing task details</small></h1>
    </div>

    {if $errorMessage}
        <p class="alert alert-error">{$errorMessage}</p>
    {/if}

    <h2>Download:</h2>
    <p>Download the following files and merge them:</p>
    
    {foreach from=$preReqTasks item=pTask}
        {assign var="pTaskId" value=$pTask->getId()}
        <p>Download {$pTask->getTitle()} <a href="{urlFor name="download-task" options="task_id.$pTaskId"}">here</a></p>
    {/foreach}
    <p style="margin-bottom: 40px"/>

    <h2>Upload the merged file here:</h2>
    <form class="well" method="post" enctype="multipart/form-data" action="{urlFor name="task" options="task_id.$taskId"}">
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
