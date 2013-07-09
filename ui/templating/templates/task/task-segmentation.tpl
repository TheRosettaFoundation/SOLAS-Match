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
                {if $type_id == TaskTypeEnum::SEGMENTATION}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::SEGMENTATION]}">{Localisation::getTranslation(Strings::COMMON_SEGMENTATION_TASK)}</span>                                   
                {elseif $type_id == TaskTypeEnum::TRANSLATION}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">{Localisation::getTranslation(Strings::COMMON_TRANSLATION_TASK)}</span>
                {elseif $type_id == TaskTypeEnum::PROOFREADING}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">{Localisation::getTranslation(Strings::COMMON_PROOFREADING_TASK)}</span>
                {elseif $type_id == TaskTypeEnum::DESEGMENTATION}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::DESEGMENTATION]}">{Localisation::getTranslation(Strings::COMMON_DESEGMENTATION_TASK)}</span>
                {/if}
            </strong>
        </small>   
        {assign var="task_id" value=$task->getId()}
    </h1>
        
{include file="task/task.details.tpl"}

    {if isset($errors)}
        <div class="alert alert-error">
            <h3>{Localisation::getTranslation(Strings::COMMON_PLEASE_FILL_IN_ALL_REQUIRED_FIELDS)}:</h3>
            <ol>
                {foreach from=$errors item=error}
                        <li>{$error}</li>
                {/foreach}
            </ol>
        </div>                        
    {/if}
    
    <div class="well">
        <form method="post" enctype="multipart/form-data" action="{urlFor name="task-segmentation" options="task_id.$task_id"}">
        <input type="hidden" id="totalWordCount" name="totalWordCount" value="{$task->getWordCount()}" />
        <table border="0" width="100%">
            <tbody id="taskSegments">
                <input type="hidden" id="totalWordCount" name="totalWordCount" value="{$task->getWordCount()}" />
                <tr>
                    <td colspan="4">
                        <label for="title">
                            <h2>{Localisation::getTranslation(Strings::COMMON_SEGMENTATION)}:
                                <a href="{urlFor name="task-user-feedback" options="task_id.$task_id"}" style="float: right" class="btn btn-success">
                                    <i class="icon-upload icon-white"></i> {Localisation::getTranslation(Strings::COMMON_PROVIDE_FEEDBACK)}
                                </a>
                            </h2>
                        </label>
                        <p class="desc">{Localisation::getTranslation(Strings::TASK_SEGMENTATION_0)}<br />
                            {Localisation::getTranslation(Strings::TASK_SEGMENTATION_2)}<br />
                            {Localisation::getTranslation(Strings::TASK_SEGMENTATION_3)}
                            <a href="{urlFor name="download-task" options="task_id.$task_id"}">{Localisation::getTranslation(Strings::COMMON_HERE)}</a>.

                            {Localisation::getTranslation(Strings::TASK_SEGMENTATION_6)}
                            <a href="{urlFor name="home"}api/v0/projects/{$task->getProjectId()}/file">{Localisation::getTranslation(Strings::COMMON_HERE)}</a>.
                        </p>

                        <hr/>
                    </td>    

                </tr>
                <tr>
                    <td><strong>{Localisation::getTranslation(Strings::TASK_SEGMENTATION_NUMBER_OF_SEGMENTS)}</strong></td>         
                    <td align="center" valign="bottom"><strong>{Localisation::getTranslation(Strings::COMMON_TRANSLATION)}</strong></td>
                    <td align="center" valign="bottom"><strong>{Localisation::getTranslation(Strings::COMMON_PROOFREADING)}</strong></td>
                    <td align="center" valign="bottom"><strong>{Localisation::getTranslation(Strings::COMMON_DESEGMENTATION)}</strong></td>
                </tr>
                <tr>
                    <td id="segmentationElements"></td>  
                    <td align="center" title="{Localisation::getTranslation(Strings::COMMON_CREATE_A_TRANSLATION_TASK_FOR_VOLUNTEER_TRANSLATORS_TO_PICK_UP)}" valign="middle">
                        <input type="checkbox" id="translation_0" name="translation_0" value="y" />
                     </td>
                    <td align="center" title="{Localisation::getTranslation(Strings::COMMON_CREATE_A_PROOFREADING_TASK_FOR_EVALUATING_THE_TRANSLATION_PROVIDED_BY_A_VOLUNTEER)}" valign="middle">
                        <input type="checkbox" id="proofreading_0" name="proofreading_0" value="y" />
                    </td>
                    <td align="center" title="{Localisation::getTranslation(Strings::COMMON_THANKS_FOR_PROVIDING_YOUR_TRANSLATION_FOR_THIS_TASK)} "valign="middle">
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
                        <p class="desc">{Localisation::getTranslation(Strings::TASK_SEGMENTATION_UPLOAD_YOUR_SEGMENTED_FILE)} {Localisation::getTranslation(Strings::COMMON_MAXIMUM_FILE_SIZE_IS)} 8 MB.</p>
                        <input type="file" name="segmentationUpload_0" id="segmentationUpload_0"/>
                        <label>{Localisation::getTranslation(Strings::COMMON_WORD_COUNT)}:</label>
                        <input type="text" name="wordCount_0" id="wordCount_0" />
                        <hr/>
                    </td>                
                </tr>
                <tr id="taskUploadTemplate_1" valign="top">
                    <td colspan="4"> 
                        <p class="desc">{Localisation::getTranslation(Strings::TASK_SEGMENTATION_UPLOAD_YOUR_SEGMENTED_FILE)} {Localisation::getTranslation(Strings::COMMON_MAXIMUM_FILE_SIZE_IS)} 8 MB.</p>
                        <input type="file" name="segmentationUpload_1" id="segmentationUpload_1"/>
                        <label>{Localisation::getTranslation(Strings::COMMON_WORD_COUNT)}:</label>
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
                        <i class="icon-upload icon-white"></i> {Localisation::getTranslation(Strings::TASK_SEGMENTATION_SUBMIT_SEGMENTED_TASKS)}
                    </button>
                </td>
            </tr>                        
        </table>
        </form>
    </div>
        
{include file="footer.tpl"}
