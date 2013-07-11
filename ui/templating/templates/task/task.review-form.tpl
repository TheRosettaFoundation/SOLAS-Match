<form class="well" method="post" action="{$formAction}" 
        onsubmit="createHiddenFields()" id="TaskReviewForm">
    {foreach $tasks as $task}
        <h2>{$task->getTitle()}</h2>
        {if $task->getId() != null}
            {assign var="id" value=$task->getId()}
            <p>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_0)}
                <a href="{urlFor name="download-task-latest-version" options="task_id.$id"}">{Localisation::getTranslation(Strings::COMMON_HERE)}</a>.
            </p>
        {else}
            {assign var="id" value=$task->getProjectId()}
            <p>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_1)}
                <a href="{urlFor name="download-project-file" options="project_id.$id"}">{Localisation::getTranslation(Strings::COMMON_HERE)}</a>.
            </p>
        {/if}

        {if isset($reviews[$id])}
            {assign var='review' value=$reviews[$id]}
        {/if}

        {if isset($review)}
            {assign var='value' value=$review->getCorrections()}
        {else}
            {assign var='value' value=3}
        {/if}
        <h3>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_CORRECTIONS)} <small>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_2)}</h3>
        <p><i>(1 - 5) 5 = "{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_FEW_ERRORS)}" | 1 = "{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_A_LOT_OF_ERRORS)}"</i></p>
        <div class="rateit" data-rateit-value="{$value}" data-rateit-step="1" data-rateit-ispreset=true 
                data-rateit-resetable=false id="rateit_corrections_{$id}">
        </div>

        {if isset($review)}
            {assign var='value' value=$review->getGrammar()}
        {else}
            {assign var='value' value=3}
        {/if}
        <h3>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_GRAMMAR)} <small>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_3)}</small></h3>
        <p><i>(1 - 5) 5 = "{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_FEW_ERRORS)}" | 1 = "{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_A_LOT_OF_ERRORS)}"</i></p>
        <div class="rateit" data-rateit-value="{$value}" data-rateit-step="1" data-rateit-ispreset=true 
                data-rateit-resetable=false id="rateit_grammar_{$id}">
        </div>

        {if isset($review)}
            {assign var='value' value=$review->getSpelling()}
        {else}
            {assign var='value' value=3}
        {/if}
        <h3>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_SPELLING)} <small>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_4)}</small></h3>
        <p><i>(1 - 5) 5 = "{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_FEW_ERRORS)}" | 1 = "{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_A_LOT_OF_ERRORS)}"</i></p>
        <div class="rateit" data-rateit-value="{$value}" data-rateit-step="1" data-rateit-ispreset=true 
                data-rateit-resetable=false id="rateit_spelling_{$id}">
        </div>

        {if isset($review)}
            {assign var='value' value=$review->getConsistency()}
        {else}
            {assign var='value' value=3}
        {/if}
        <h3>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_CONSISTENCY)} <small>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_5)}</small></h3>
        <p><i>(1 - 5) 5 = "{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_FEW_ERRORS)}" | 1 = "{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_A_LOT_OF_ERRORS)}"</i></p>
        <div class="rateit" data-rateit-value="{$value}" data-rateit-step="1" data-rateit-ispreset=true 
                data-rateit-resetable=false id="rateit_consistency_{$id}">
        </div>

        <h3>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_COMMENT)} <small>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_6)}</small></h3>
        <textarea name="comment_{$id}" cols='40' rows='10' style="width: 80%">{if isset($review)}{$review->getComment()}{/if}</textarea>
    {/foreach}

    <br />
    <button class="btn btn-primary" type="submit" name="submitReview">
        <i class="icon-upload icon-white"></i> {Localisation::getTranslation(Strings::COMMON_SUBMIT)}
    </button>
    <button class="btn btn-inverse" type="submit" name="skip">
        <i class="icon-circle-arrow-right icon-white"></i> {Localisation::getTranslation(Strings::TASK_REVIEW_FORM_SKIP)}
    </button>
</form>
