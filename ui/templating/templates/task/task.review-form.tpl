{if isset($formAction)}
    <form class="well" method="post" action="{$formAction}"  onsubmit="createHiddenFields()" id="TaskReviewForm" accept-charset="utf-8">
{else}
    <div class="well">
{/if}
    <h2>{$reviewedTask->getTitle()}</h2>
    {if $reviewedTask->getId() != null}
        {assign var="id" value=$reviewedTask->getId()}
        <p>
            {sprintf(Localisation::getTranslation(Strings::TASK_REVIEW_FORM_0), {urlFor name="download-task-latest-version" options="task_id.$id"})}
        </p>
    {else}
        {assign var="id" value=$reviewedTask->getProjectId()}
        <p>
            {sprintf(Localisation::getTranslation(Strings::TASK_REVIEW_FORM_1), {urlFor name="download-project-file" options="project_id.$id"})}
        </p>
    {/if}

    {if isset($review)}
        {assign var='value' value=$review->getCorrections()}
        {assign var='readonly' value="data-rateit-readonly=true"}
    {else}
        {assign var='value' value=3}
        {assign var='readonly' value=""}
    {/if}
    <h3>
        {Localisation::getTranslation(Strings::TASK_REVIEW_FORM_CORRECTIONS)} 
        <small>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_2)}</small>
    </h3>
    <p><i>(1 - 5) 5 = "{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_FEW_ERRORS)}" | 1 = "{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_A_LOT_OF_ERRORS)}"</i></p>
    <div class="rateit" data-rateit-value="{$value}" data-rateit-step="1" data-rateit-ispreset=true 
            data-rateit-resetable=false id="rateit_corrections_{$id}" {$readonly}>
    </div>

    {if isset($review)}
        {assign var='value' value=$review->getGrammar()}
        {assign var='readonly' value="data-rateit-readonly=true"}
    {else}
        {assign var='value' value=3}
        {assign var='readonly' value=""}
    {/if}
    <h3>
        {Localisation::getTranslation(Strings::TASK_REVIEW_FORM_GRAMMAR)} 
        <small>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_3)}</small>
    </h3>
    <p><i>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_RATING)}</i></p>
    <div class="rateit" data-rateit-value="{$value}" data-rateit-step="1" data-rateit-ispreset=true 
            data-rateit-resetable=false id="rateit_grammar_{$id}" {$readonly}>
    </div>

    {if isset($review)}
        {assign var='value' value=$review->getSpelling()}
        {assign var='readonly' value="data-rateit-readonly=true"}
    {else}
        {assign var='value' value=3}
        {assign var='readonly' value=""}
    {/if}
    <h3>
        {Localisation::getTranslation(Strings::TASK_REVIEW_FORM_SPELLING)}
        <small>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_4)}</small>
    </h3>
    <p><i>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_RATING)}</i></p>
    <div class="rateit" data-rateit-value="{$value}" data-rateit-step="1" data-rateit-ispreset=true 
            data-rateit-resetable=false id="rateit_spelling_{$id}" {$readonly}>
    </div>

    {if isset($review)}
        {assign var='value' value=$review->getConsistency()}
        {assign var='readonly' value="data-rateit-readonly=true"}
    {else}
        {assign var='value' value=3}
        {assign var='readonly' value=""}
    {/if}
    <h3>
        {Localisation::getTranslation(Strings::TASK_REVIEW_FORM_CONSISTENCY)}
        <small>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_5)}</small>
    </h3>
    <p><i>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_RATING)}</i></p>
    <div class="rateit" data-rateit-value="{$value}" data-rateit-step="1" data-rateit-ispreset=true 
            data-rateit-resetable=false id="rateit_consistency_{$id}" {$readonly}>
    </div>

    <h3>
        {Localisation::getTranslation(Strings::TASK_REVIEW_FORM_COMMENT)}
        <small>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_6)}</small>
    </h3>
    {if isset($review)}
        <p>{$review->getComment()}</p>
    {else}
        <textarea name="comment_{$id}" cols='40' rows='10' style="width: 80%"></textarea>
    {/if}

    {if isset($formAction)}
        <br />
        {if !isset($review)}
            <button class="btn btn-primary" type="submit" name="submitReview">
                <i class="icon-upload icon-white"></i> {Localisation::getTranslation(Strings::TASK_REVIEW_FORM_SUBMIT_REVIEW)}
            </button>
        {/if}
        <button class="btn btn-inverse" type="submit" name="skip">
            <i class="icon-circle-arrow-right icon-white"></i> {Localisation::getTranslation(Strings::TASK_REVIEW_FORM_SKIP)}
        </button>
    {/if}
{if isset($formAction)}
    </form>
{else}
    </div>
{/if}

