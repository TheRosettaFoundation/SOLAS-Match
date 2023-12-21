{include file="new_header.tpl"}



<div class="container-fluid">

<header class="py-2">

<div class="container py-2">

         <div>
            <a href="#"> Home </a> >
            <a href="#" class="text-primary"> Task </a> >
            <a href="#"> Clain </a> >
        </div>


</div>
   

</header>

<section class="bg-light"> 

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

            <div>
             {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER))}
                <a href="{urlFor name="task-alter" options="task_id.$task_id"}" class= btn btn-primary">
                    <i class="icon-wrench icon-white"></i> {Localisation::getTranslation('task_view_edit_task_details')}
                </a>
            {/if}

            </div>

              


          </div>

        </div>


        <div class="container">

        
            <div class="row d-flex">

                <div class="col-6 "> 

                      test
                
                <div>

                <div class="col-4"> 

                        testing here..
                                                    
  

                
                <div>
            
        </div>
        
        
        

</section>

       
   
{include file="footer2.tpl"}
