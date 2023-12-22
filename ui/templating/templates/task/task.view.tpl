{include file="new_header.tpl"}



<div class="container-fluid">

<header class="py-2">

<div class="container py-2">

         <div>
            <a href="#"> Home </a> >
            <a href="#" class="text-primary"> Task </a> >
            <a href="#"> Claim </a> >
        </div>


</div>
   

</header>

<section class="bg-light-subtle"> 

        <div class="container ">

          <div class="d-flex py-4 justify-content-between">

               <div>

                {if $task->getTitle() != ''}
                {TemplateHelper::uiCleanseHTMLNewlineAndTabs($task->getTitle())}
                {else}
                {Localisation::getTranslation('common_task')} {$task->getId()}
                {/if}

                </div>

             <div>
                {if $task->getTaskStatus() == TaskStatusEnum::PENDING_CLAIM && !$is_denied_for_task && !TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}
                {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $LINGUIST + $NGO_LINGUIST)) && $user_within_limitations}
                    <a href="{urlFor name="task-claim-page" options="task_id.$task_id"}" class="btn btn-primary">
                    <i class="icon-share-alt icon-white"></i> {Localisation::getTranslation('task_view_download_task')}</a>&nbsp;&nbsp;
                {/if}
                {/if}

            </div>
       


          </div>


       </div>


        <div class="container">

        
            <div class="row d-flex justify-content-between">

                <div class=" col-12 col-md-6 "> 

                         {include file="task/task.details.tpl"} 

                
                </div>

                <div class=" col-12 col-md-4"> 

                                                        
                            {if ($alsoViewedTasksCount>0)}
                            <div class="row"></div>
                                <div class="row">
                                    <div class="span4 pull-right">
                                        <h3>{Localisation::getTranslation('users_also_viewed')}</h3>
                                        
                                        {if isset($alsoViewedTasks)}
                                        <div id="also-viewed-tasks">
                                            <div class="ts">
                                                {for $count=0 to $alsoViewedTasksCount-1}
                                                    {assign var="alsoViewedTask" value=$alsoViewedTasks[$count]}
                                                    <div class="ts-task">
                                                        {assign var="also_viewed_task_id" value=$alsoViewedTask->getId()}
                                                        {assign var="also_viewed_type_id" value=$alsoViewedTask->getTaskType()}
                                                        {assign var="also_viewed_status_id" value=$alsoViewedTask->getTaskStatus()}
                                                        {assign var="also_viewed_task_title" value=$alsoViewedTask->getTitle()}
                                                        <div class="task">
                                                            <h2>
                                                            <a id="also_viewed_task_{$also_viewed_task_id}" href="{$siteLocation}task/{$also_viewed_task_id}/view">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($also_viewed_task_title)}</a>
                                                            </h2>
                                                        {if TaskTypeEnum::$enum_to_UI[$also_viewed_type_id]['source_and_target']}
                                                            <p>
                                                                {Localisation::getTranslation('common_from')}: <strong>{TemplateHelper::getLanguageAndCountryNoCodes($alsoViewedTask->getSourceLocale())}</strong>
                                                            </p>
                                                        {/if}
                                                            <p>
                                                                {Localisation::getTranslation('common_to')}: <strong>{TemplateHelper::getLanguageAndCountryNoCodes($alsoViewedTask->getTargetLocale())}</strong>
                                                            </p>
                                                            <div>
                                                                <p>
                                                                    <span class="label label-info" style="background-color:rgb(218, 96, 52);">{$taskStatusTexts[$also_viewed_status_id]}</span>
                                                                    &nbsp;|&nbsp;
                                                            <span class="label label-info" style="background-color: {TaskTypeEnum::$enum_to_UI[$also_viewed_type_id]['colour']}">{TaskTypeEnum::$enum_to_UI[$also_viewed_type_id]['type_text_short']}</span>
                                                                    &nbsp;|&nbsp;
                                                                    {if $alsoViewedTask->getWordCount()}
                                                                <span class="label label-info" style="background-color:rgb(57, 165, 231);">{$alsoViewedTask->getWordCount()} {TaskTypeEnum::$enum_to_UI[$also_viewed_type_id]['unit_count_text_short']}</span>
                                                                    {/if}
                                                                </p>
                                                            </div>
                                                            <p>
                                                            Due by <strong><span class="convert_utc_to_local_deadline" style="display: inline-block; visibility: hidden">{$deadline_timestamps[$also_viewed_task_id]}</span></strong>
                                                            </p>
                                                        <p id="also_viewed_parents_{$also_viewed_task_id}">{TemplateHelper::uiCleanseNewlineAndTabs($projectAndOrgs[$also_viewed_task_id])}</p>
                                                        </div>
                                                    </div>
                                                {/for}
                                            </div>
                                        </div>
                                        {/if}
                                        
                                    </div>
                                    <div class="pull-left" style="max-width: 70%;">
                            {/if}
           
        
                </div>
            
            </div>

        </div>


 
        

</section>

       {if !empty($file_preview_path)}
		    <table width="100%">
		        <thead>
                <th>{Localisation::getTranslation('task_view_source_document_preview')} - {TemplateHelper::uiCleanseHTML($filename)}<hr/></th>
		        </thead>
		        <tbody>
		            <tr>
		                <td align="center"><iframe src="https://docs.google.com/viewer?url={$file_preview_path}&embedded=true" width="800" height="780" style="border: none;"></iframe></td>
		            </tr>
		        </tbody>
		    </table>
        {/if}

  

       
   
{include file="footer2.tpl"}
