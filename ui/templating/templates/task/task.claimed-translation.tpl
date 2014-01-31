{assign var=task_id value=$task->getId()}

    <section>
        <div class="page-header">
            <h1>{Localisation::getTranslation(Strings::TASK_CLAIMED_TRANSLATION_TRANSLATION_TASK_CLAIMED)} <small>{Localisation::getTranslation(Strings::TASK_CLAIMED_TRANSLATION_PLEASE_TRANSLATE_IT)}</small></h1>
        </div>
        <div class="alert alert-success">
            <strong>{Localisation::getTranslation(Strings::COMMON_SUCCESS)}</strong> {Localisation::getTranslation(Strings::TASK_CLAIMED_TRANSLATION_0)} &ldquo;<strong>{$task->getTitle()}</strong>&rdquo;.
        </div>
    </section>

    {include file="handle-flash-messages.tpl"}
        
    <section>
            <h1>What now? <small>{Localisation::getTranslation(Strings::TASK_CLAIMED_TRANSLATION_WE_NEED_YOUR_TRANSLATION)}</small></h1>
            <p>{Localisation::getTranslation(Strings::COMMON_THIS_THIS_WHAT_YOU_NEED_TO_DO_AS_SOON_AS_POSSIBLE)}</p>
            <ol>
                <li>{Localisation::getTranslation(Strings::COMMON_CAN_YOU_OPEN_FILE)}</li>
                <li>{sprintf(Localisation::getTranslation(Strings::TASK_CLAIMED_TRANSLATION_TRANSLATE_THE_FILE_TO), {TemplateHelper::getLanguage($task->getTargetLocale())})}</li>
                <li>{Localisation::getTranslation(Strings::TASK_CLAIMED_TRANSLATION_5)}</li>
            </ol>

        {if isset($user)}
            <p>{sprintf(Localisation::getTranslation(Strings::COMMON_WE_HAVE_ALSO_EMAILED_YOU_THESE_INSTRUCTIONS_TO), {$user->getEmail()})}</p>
        {/if}
    </section>
    
    <!--Added to test Pootle connection SIMPLESTRINGS-->
    <div class="well">
    	<form method="post" action="{urlFor name="task-claimed" options="task_id.$task_id"}" enctype="multipart/form-data">
            <h3>You can also translate this file using a third party tool</h3>
            <br />
    		<p>
                <button type="submit" value="pootleBtn" name="submit" class="btn btn-primary">
            	    <i class="icon-share-alt icon-white"></i> Translate via Pootle
                </button>
        	</p>
    	</form>

        <h3>{Localisation::getTranslation(Strings::TASK_CLAIMED_TRANSLATION_4)}</h3>
        <br />
        <p>
            <a href="{urlFor name="task" options="task_id.$task_id"}" class="btn btn-primary">
                <i class="icon-share-alt icon-white"></i> {Localisation::getTranslation(Strings::TASK_CLAIMED_TRANSLATION_UPLOAD_TRANSLATED_TASK)}
            </a>
            <a href="{urlFor name="home"}" class="btn">
                <i class="icon-arrow-left icon-black"></i> {Localisation::getTranslation(Strings::COMMON_NO_JUST_BRING_ME_BACK_TO_THE_TASK_PAGE)}
            </a>            
        </p>
    </div>

    <p>
        <small>
            ({Localisation::getTranslation(Strings::COMMON_CANT_FIND_THE_FILE_ON_YOUR_DESKTOP)}
            {sprintf(Localisation::getTranslation(Strings::COMMON_DOWNLOAD_THE_FILE), {urlFor name="download-task" options="task_id.$task_id"})})
        </small>
    </p>

