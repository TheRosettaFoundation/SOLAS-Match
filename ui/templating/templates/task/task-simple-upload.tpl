{include file="header.tpl"}

{assign var="task_id" value=$task->getId()}

{include file="handle-flash-messages.tpl"}

{include file="header.tpl"}

    <h1 class="page-header">
        {if $task->getTitle() != ''}
            {$task->getTitle()}
        {else}
            {Localisation::getTranslation(Strings::COMMON_TASK)} {$task->getId()}
        {/if}
        <small>
            <strong>
                -
                {assign var="type_id" value=$task->getTaskType()}
                {if $type_id == TaskTypeEnum::TRANSLATION}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">{Localisation::getTranslation(Strings::COMMON_TRANSLATION_TASK)}
                {elseif $type_id == TaskTypeEnum::PROOFREADING}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">{Localisation::getTranslation(Strings::COMMON_PROOFREADING_TASK)}
                {/if}
            </strong>
        </small>   
        {assign var="task_id" value=$task->getId()}
    </h1>

{include file="task/task.details.tpl"}        

    <div class="well">
        <div class="page-header">
            <h1>{Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_FINISHED_PROCESSING)}
                <form method="post" action="{urlFor name="task-user-feedback" options="task_id.$task_id"}" enctype="application/x-www-form-urlencoded">
                    <button style="float: right" class="btn btn-success" type="submit" value="Submit Feedback"><i class="icon-upload icon-white"></i> {Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_PROVIDE_FEEDBACK)}</button>   
                </form>
            </h1>
            <div class="pull-right" >

            </div>
        </div>
        {if isset($upload_error)}
                <div class="alert alert-error">
                        <strong>{Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_UPLOAD_ERROR)}</strong> {$upload_error}
                </div>
        {/if}
        {if $type_id == TaskTypeEnum::TRANSLATION}
            <h3>{Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_0)} {$filename}</h3>
        {else}
            <h3>{Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_0_PROOFREADING)} {$filename}</h3>
        {/if}   
        <form class="well" method="post" action="{urlFor name="task-simple-upload" options="task_id.$task_id"}" enctype="multipart/form-data">
                <input type="hidden" name="task_id" value="{$task->getId()}"/>
                <input type="file" name="{$fieldName}" id="{$fieldName}"/>
                <p class="help-block">
                        {Localisation::getTranslation(Strings::COMMON_MAXIMUM_FILE_SIZE_IS)} {$max_file_size}MB.
                </p> 
                <button type="submit" value="submit" name="submit" class="btn btn-success"><i class="icon-upload icon-white"></i> {Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_UPLOAD)}</button>
            {if ($converter == "y")}
                <button type="submit" value="XLIFF" name="submit" class="btn btn-success"><i class="icon-upload icon-white"></i> {Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_AS_XLIFF)}</button>
            {/if}
        </form>

        {if isset($file_previously_uploaded) && $file_previously_uploaded}
            <br />
            <div class="alert">
                <p>{Localisation::getTranslation(Strings::COMMON_THANKS_FOR_PROVIDING_YOUR_TRANSLATION_FOR_THIS_TASK)}
                {if $org != null && $org->getName() != ''}
                    {$org->getName()}
                {else}
                    {Localisation::getTranslation(Strings::COMMON_THIS_ORGANISATION)}
                {/if}
                {Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_1)}</p>
                <p><strong>{Localisation::getTranslation(Strings::COMMON_WARNING)}! </strong>{Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_2)}</p>
            </div>
        {/if}

        <h3>{Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_3)} <small>{Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_4)}</small></h3>
        <br />
        <p>
            {Localisation::getTranslation(Strings::COMMON_CLICK)} <a href="{urlFor name="home"}api/v0/projects/{$task->getProjectId()}/file">{Localisation::getTranslation(Strings::COMMON_HERE)}</a> {Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_TO_DOWNLOAD_THE)}
            <strong>{Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_ORIGINAL_PROJECT_FILE)}</strong>.
        </p>
        <p>{Localisation::getTranslation(Strings::COMMON_CLICK)}
            <a href="{urlFor name="download-task" options="task_id.$task_id"}">{Localisation::getTranslation(Strings::COMMON_HERE)}</a>
            {Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_TO_REDOWNLOAD_THE)} <strong>{Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_ORIGINAL_TASK_FILE)}</strong>.
        </p> 

        {if ($converter == "y")}
        <p>{Localisation::getTranslation(Strings::COMMON_CLICK)}
            <a href="{urlFor name="download-task" options="task_id.$task_id"}?convertToXliff=true">{Localisation::getTranslation(Strings::COMMON_HERE)}</a>   
            {Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_TO_REDOWNLOAD_THE)} <strong>{Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_ORIGINAL_TASK_FILE)}</strong> {Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_AS_XLIFF)}
        </p>     
        {/if}  

        <p>{Localisation::getTranslation(Strings::COMMON_CLICK)}
            <a href="{urlFor name="download-task-latest-version" options="task_id.$task_id"}">{Localisation::getTranslation(Strings::COMMON_HERE)}</a>
            {Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_TO_REDOWNLOAD_THE)} <strong>{Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_LATEST_UPLOADED_FILE)}</strong>.
        </p> 

        {if ($converter == "y")}
        <p>{Localisation::getTranslation(Strings::COMMON_CLICK)}
            <a href="{urlFor name="download-task-latest-version" options="task_id.$task_id"}?convertToXliff=true">{Localisation::getTranslation(Strings::COMMON_HERE)}</a>   
            {Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_TO_REDOWNLOAD_THE)} <strong>{Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_LATEST_UPLOADED_FILE)}</strong> {Localisation::getTranslation(Strings::TASK_SIMPLE_UPLOAD_AS_XLIFF)}
        </p>     
        {/if}
    </div>

{include file="footer.tpl"}
