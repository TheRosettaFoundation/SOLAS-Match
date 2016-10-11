{include file="header.tpl"}

    {if $task->getTaskStatus() == TaskStatusEnum::COMPLETE}
        <div class="alert alert-info">
            <p>{Localisation::getTranslation('task_segmentation_7')}</p>
        </div>
    {/if}

    <h1 class="page-header">
        {if $task->getTitle() != ''}
            {TemplateHelper::uiCleanseHTML($task->getTitle())}
        {else}
            {Localisation::getTranslation('common_task')} {$task->getId()}
        {/if}
        <small>
            <strong>
                -
                {assign var="type_id" value=$task->getTaskType()}
                {if $type_id == TaskTypeEnum::SEGMENTATION}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::SEGMENTATION]}">{Localisation::getTranslation('common_segmentation_task')}</span>                                   
                {elseif $type_id == TaskTypeEnum::TRANSLATION}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">{Localisation::getTranslation('common_translation_task')}</span>
                {elseif $type_id == TaskTypeEnum::PROOFREADING}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">{Localisation::getTranslation('common_proofreading_task')}</span>
                {elseif $type_id == TaskTypeEnum::DESEGMENTATION}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::DESEGMENTATION]}">{Localisation::getTranslation('common_desegmentation_task')}</span>
                {/if}
            </strong>
        </small>   
        {assign var="task_id" value=$task->getId()}
    </h1>
        
{include file="task/task.details.tpl"}

    {if isset($errors)}
        <div class="alert alert-error">
            <h3>{Localisation::getTranslation('common_please_fill_in_all_required_fields')}</h3>
            <ol>
                {foreach from=$errors item=error}
                        <li>{$error}</li>
                {/foreach}
            </ol>
        </div>                        
    {/if}

    {if $task->getTaskStatus() != TaskStatusEnum::COMPLETE}
        <div class="well">
            <form method="post" enctype="multipart/form-data" action="{urlFor name="task-segmentation" options="task_id.$task_id"}" accept-charset="utf-8">
            <input type="hidden" id="totalWordCount" name="totalWordCount" value="{$task->getWordCount()}" />
            <table border="0" width="100%">
                <tbody id="taskSegments">
                    <input type="hidden" id="totalWordCount" name="totalWordCount" value="{$task->getWordCount()}" />
                    <tr>
                        <td colspan="4">
                            <label for="title">
                                <h2>{Localisation::getTranslation('common_segmentation')}:
                                    <a href="{urlFor name="task-user-feedback" options="task_id.$task_id"}" style="float: right" class="btn btn-success">
                                        <i class="icon-upload icon-white"></i> {Localisation::getTranslation('common_provide_feedback')}
                                    </a>
                                </h2>
                            </label>
                            <p class="desc">{Localisation::getTranslation('task_segmentation_0')}<br />
                                {Localisation::getTranslation('task_segmentation_2')}<br />
                                {Localisation::getTranslation('task_segmentation_3')}
                                <a href="{urlFor name="download-task" options="task_id.$task_id"}">{Localisation::getTranslation('common_here')}</a>.

                                {Localisation::getTranslation('task_segmentation_6')}
                                <a href="{urlFor name="home"}project/{$task->getProjectId()}/file">{Localisation::getTranslation('common_here')}</a>.
                            </p>
    
                            <hr/>
                        </td>    

                    </tr>
                    <tr>
                        <td><strong>{Localisation::getTranslation('task_segmentation_number_of_segments')}</strong></td>         
                        <td align="center" valign="bottom"><strong>{Localisation::getTranslation('common_translation')}</strong></td>
                        <td align="center" valign="bottom"><strong>{Localisation::getTranslation('common_proofreading')}</strong></td>
                        <td align="center" valign="bottom"><strong>{Localisation::getTranslation('common_desegmentation')}</strong></td>
                    </tr>
                    <tr>
                        <td id="segmentationElements"></td>  
                        <td align="center" title="{Localisation::getTranslation('common_create_a_translation_task_for_volunteer_translators_to_pick_up')}" valign="middle">
                            <input type="checkbox" id="translation_0" name="translation_0" value="y" />
                         </td>
                        <td align="center" title="{Localisation::getTranslation('common_create_a_proofreading_task_for_evaluating_the_translation_provided_by_a_volunteer')}" valign="middle">
                            <input type="checkbox" id="proofreading_0" name="proofreading_0" value="y" />
                        </td>
                        <td align="center" title="{Localisation::getTranslation('common_thanks_for_providing_your_translation_for_this_task')} "valign="middle">
                            <input type="checkbox" id="desegmentation_0" checked="true" name="desegmentation_0" value="y" disabled />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5">
                        <hr/>
                        </td>
                    </tr>
                    <tr id="taskUploadTemplate_0" valign="top">
                        <td colspan="4">
                            <p class="desc">
                                <strong id="label_0">File #1:</strong> 
                                {Localisation::getTranslation('task_segmentation_upload_your_segmented_file')}
                                {sprintf(Localisation::getTranslation('common_maximum_file_size_is'), TemplateHelper::maxFileSizeMB())} 
                            </p>
                            <input type="file" name="segmentationUpload_0" id="segmentationUpload_0"/>
                            <label>{Localisation::getTranslation('common_word_count')}:</label>
                            <input type="text" name="wordCount_0" id="wordCount_0" />
                            <hr/>
                        </td>                
                    </tr>
                    <tr id="taskUploadTemplate_1" valign="top">
                        <td colspan="4"> 
                            <p class="desc">
                                <strong>File #2: </strong> 
                                {Localisation::getTranslation('task_segmentation_upload_your_segmented_file')}
                                {sprintf(Localisation::getTranslation('common_maximum_file_size_is'), TemplateHelper::maxFileSizeMB())} 
                            </p>
                            <input type="file" name="segmentationUpload_1" id="segmentationUpload_1"/>
                            <label>{Localisation::getTranslation('common_word_count')}:</label>
                            <input type="text" name="wordCount_1" id="wordCount_1" />
                            <hr/>
                        </td>                
                    </tr>
                </tbody>    
            </table> 
            <table width="100%">
                <tr>
                    <td align="center" colspan="5">
                        <p style="margin-bottom:20px;"></p> 
                        <button type="submit" name="createSegmentation" value="1" class="btn btn-success">
                            <i class="icon-upload icon-white"></i> {Localisation::getTranslation('task_segmentation_submit_segmented_tasks')}
                        </button>
                    </td>
                </tr>                        
            </table>
            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
        </form>
    </div>
{/if}
        
{include file="footer.tpl"}
