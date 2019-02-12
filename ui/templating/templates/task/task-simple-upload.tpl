{include file="header.tpl"}
<!-- Editor Hint: ¿áéíóú -->

{assign var="task_id" value=$task->getId()}

{include file="handle-flash-messages.tpl"}

    <h1 class="page-header">
        {if $task->getTitle() != ''}
            {TemplateHelper::uiCleanseHTMLNewlineAndTabs($task->getTitle())}
        {else}
            {Localisation::getTranslation('common_task')} {$task->getId()}
        {/if}
        <small>
            <strong>
                -
                {assign var="type_id" value=$task->getTaskType()}
                {if $type_id == TaskTypeEnum::TRANSLATION}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">{Localisation::getTranslation('common_translation_task')}
                {elseif $type_id == TaskTypeEnum::PROOFREADING}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">{Localisation::getTranslation('common_proofreading_task')}
                {/if}
            </strong>
        </small>   
        {assign var="task_id" value=$task->getId()}
    </h1>

{include file="task/task.details.tpl"}        

    <div class="well">
        <div class="page-header">
            <h1>{Localisation::getTranslation('task_simple_upload_finished_processing')}
                <form method="post" action="{urlFor name="task-user-feedback" options="task_id.$task_id"}" enctype="application/x-www-form-urlencoded">
                    <button style="float: right" class="btn btn-success" type="submit" value="Submit Feedback"><i class="icon-upload icon-white"></i> {Localisation::getTranslation('task_simple_upload_provide_feedback')}</button>   
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                </form>
            </h1>
            <div class="pull-right" >

            </div>
        </div>
        {if isset($upload_error)}
                <div class="alert alert-error">
                        <strong>{Localisation::getTranslation('task_simple_upload_upload_error')}</strong> {$upload_error}
                </div>
        {/if}
        {if $type_id == TaskTypeEnum::TRANSLATION}
            <h3>{sprintf(Localisation::getTranslation('task_simple_upload_0'), {TemplateHelper::uiCleanseHTML($filename)})}</h3>
        {else}
            <h3>{sprintf(Localisation::getTranslation('task_simple_upload_0_proofreading'), {TemplateHelper::uiCleanseHTML($filename)})}</h3>
            <p>{Localisation::getTranslation('task_simple_upload_clean_upload')}</p>
        {/if}   
        <form class="well" method="post" action="{urlFor name="task-simple-upload" options="task_id.$task_id"}" enctype="multipart/form-data">
                <input type="hidden" name="task_id" value="{$task->getId()}"/>
                <input type="file" name="{$fieldName}" id="{$fieldName}"/>
                <p class="help-block">
                        {sprintf(Localisation::getTranslation('common_maximum_file_size_is'), {$max_file_size})}
                </p> 
                <button type="submit" value="submit" name="submit" class="btn btn-success"><i class="icon-upload icon-white"></i> {Localisation::getTranslation('task_simple_upload_upload')}</button>
            {if ($converter == "y")}
                <button type="submit" value="XLIFF" name="submit" class="btn btn-success"><i class="icon-upload icon-white"></i> {Localisation::getTranslation('task_simple_upload_as_xliff')}</button>
            {/if}
            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
        </form>

        {if $matecat_url != ''}
        <form class="well" method="post" action="{urlFor name="task-simple-upload" options="task_id.$task_id"}" enctype="application/x-www-form-urlencoded">
            <input type="hidden" name="task_id" value="{$task->getId()}" />
            <input type="hidden" name="copy_from_matecat" value="1" />
            {if $type_id == TaskTypeEnum::TRANSLATION}
            {Localisation::getTranslation('task_claimed_alternative_option')} <button type="submit" value="submit" name="submit" class="btn btn-success"><i class="icon-upload icon-white"></i> {Localisation::getTranslation('task_simple_upload_copy_from_kato')}</button>
            <p>
                {if empty($chunks)}
                {sprintf(Localisation::getTranslation('task_simple_upload_view_on_kato'), {$matecat_url})}<br />
                {/if}
                {sprintf(Localisation::getTranslation('task_simple_upload_download_from_kato'), {$matecat_download_url})}
            </p>
            {elseif $type_id == TaskTypeEnum::PROOFREADING}
            {Localisation::getTranslation('task_claimed_alternative_option')} <button type="submit" value="submit" name="submit" class="btn btn-success"><i class="icon-upload icon-white"></i> {Localisation::getTranslation('task_simple_upload_copy_from_kato_proofread')}</button>
            <p>
                {if empty($chunks)}
                {sprintf(Localisation::getTranslation('task_simple_upload_view_on_kato_proofread'), {$matecat_url})}<br />
                {/if}
                {sprintf(Localisation::getTranslation('task_simple_upload_download_from_kato_proofread'), {$matecat_download_url})}
            </p>
            {/if}
            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
        </form>
        {/if}

        {if isset($file_previously_uploaded) && $file_previously_uploaded}
            <br />
            <div class="alert">
                <p>{Localisation::getTranslation('common_thanks_for_providing_your_translation_for_this_task')}
                {if $org != null && $org->getName() != ''}
                    {sprintf(Localisation::getTranslation('task_simple_upload_1'), {$org->getName()})}
                {else}
                    {Localisation::getTranslation('task_simple_upload_8')}
                {/if}
                </p>
                <p><strong>{Localisation::getTranslation('common_warning')}! </strong>{Localisation::getTranslation('task_simple_upload_2')}</p>
            </div>
        {/if}

        {if !empty($chunks)}
            {assign var="chunk_number" value=0}
            <table class="table table-striped">
                <thead>
                    <th style="text-align: left">Chunk Number</th>
                    <th>Kató TM Translate URL</th>
                    <th>Kató TM Revise URL</th>
                    <th>Kató TM Status</th>
                </thead>
                <tbody>
                    {foreach $chunks as $chunk}
                        <tr>
                            <td style="text-align: left; word-break:break-all; width: 50px">{$chunk_number}</td>
                            <td><a href="{$chunk['translate_url']}">{$chunk['translate_url']}</a></td>
                            <td><a href="{$chunk['revise_url']}"   >{$chunk['revise_url']}</a></td>
                            <td>{$chunk['DOWNLOAD_STATUS']}</td>
                        </tr>
                        {assign var="chunk_number" value=($chunk_number + 1)}
                    {/foreach}
                </tbody>
            </table>
        {/if}

        <h3>{Localisation::getTranslation('task_simple_upload_3')} <small>{Localisation::getTranslation('task_simple_upload_4')}</small></h3>
        <br />
        <p>             
            {sprintf(Localisation::getTranslation('task_simple_upload_original_project_file'), {"{urlFor name="home"}project/{$task->getProjectId()}/file/"})}
        </p>
        
        <p>
            {sprintf(Localisation::getTranslation('task_simple_upload_original_task_file'), {urlFor name="download-task" options="task_id.$task_id"})}
        </p> 

        {if ($converter == "y")}
        <p>  
            {sprintf(Localisation::getTranslation('task_simple_upload_original_task_file'), {"{urlFor name="download-task" options="task_id.$task_id"}?convertToXliff=true"})} - {Localisation::getTranslation('task_simple_upload_as_xliff')}
        </p>     
        {/if}  

        <p>
            {sprintf(Localisation::getTranslation('task_simple_upload_latest_uploaded_file'), {urlFor name="download-task-latest-version" options="task_id.$task_id"})}
        </p> 

        {if ($converter == "y")}
        <p>
            {sprintf(Localisation::getTranslation('task_simple_upload_latest_uploaded_file'), {"{urlFor name="download-task-latest-version" options="task_id.$task_id"}?convertToXliff=true"})} - {Localisation::getTranslation('task_simple_upload_as_xliff')}
        </p>     
        {/if}
    </div>

{include file="footer.tpl"}
