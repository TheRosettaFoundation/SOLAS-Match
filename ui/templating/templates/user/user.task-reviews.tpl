{include file="header.tpl"}

<div class="page-header">
    <h1>{$task->getTitle()} <small>{Localisation::getTranslation(Strings::USER_TASK_REVIEWS_0)}</small></h1>
</div>

<p>
    {Localisation::getTranslation(Strings::USER_TASK_REVIEWS_1)}
    {Localisation::getTranslation(Strings::USER_TASK_REVIEWS_2)}
    {Localisation::getTranslation(Strings::USER_TASK_REVIEWS_3)}
</p>

<div class="well">
    {if count($reviews) == 0}
        <span class="alert alert-info">{Localisation::getTranslation(Strings::USER_TASK_REVIEWS_4)}</span>
    {else}
        {assign var="count" value=1}
        {assign var="id" value=$task->getId()}
        {foreach $reviews as $review}
            <h2>Review #{$count}</h2>
            <p>
                {Localisation::getTranslation(Strings::TASK_REVIEW_FORM_0)}
                <a href="{urlFor name="download-task-latest-version" options="task_id.$id"}">
                    {Localisation::getTranslation(Strings::COMMON_HERE)}
                </a>.
            </p>

            <h3>
                {Localisation::getTranslation(Strings::TASK_REVIEW_FORM_CORRECTIONS)} 
                <small>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_2)}</small>
            </h3>
            <p>
                <i>
                    (1 - 5) 5 = "{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_FEW_ERRORS)}" |
                    1 = "{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_A_LOT_OF_ERRORS)}"
                </i>
            </p>
            <div class="rateit" data-rateit-value="{$review->getCorrections()}" data-rateit-step="1" data-rateit-ispreset=true 
                    data-rateit-resetable=false data-rateit-readonly=true>
            </div>

            <h3>
                {Localisation::getTranslation(Strings::TASK_REVIEW_FORM_GRAMMAR)} 
                <small>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_3)}</small>
            </h3>
            <p>
                <i>
                    (1 - 5) 5 = "{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_FEW_ERRORS)}" |
                    1 = "{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_A_LOT_OF_ERRORS)}"
                </i>
            </p>
            <div class="rateit" data-rateit-value="{$review->getGrammar()}" data-rateit-step="1" data-rateit-ispreset=true 
                    data-rateit-resetable=false data-rateit-readonly=true>
            </div>

            <h3>
                {Localisation::getTranslation(Strings::TASK_REVIEW_FORM_SPELLING)}
                <small>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_4)}</small>
            </h3>
            <p>
                <i>
                    (1 - 5) 5 = "{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_FEW_ERRORS)}" |
                    1 = "{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_A_LOT_OF_ERRORS)}"
                </i>
            </p>
            <div class="rateit" data-rateit-value="{$review->getSpelling()}" data-rateit-step="1" data-rateit-ispreset=true 
                    data-rateit-resetable=false data-rateit-readonly=true>
            </div>

            <h3>
                {Localisation::getTranslation(Strings::TASK_REVIEW_FORM_CONSISTENCY)}
                <small>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_5)}</small>
            </h3>
            <p>
                <i>
                    (1 - 5) 5 = "{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_FEW_ERRORS)}" |
                    1 = "{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_A_LOT_OF_ERRORS)}"
                </i>
            </p>
            <div class="rateit" data-rateit-value="{$review->getConsistency()}" data-rateit-step="1" data-rateit-ispreset=true 
                    data-rateit-resetable=false data-rateit-readonly=true>
            </div>

            <h3>
                {Localisation::getTranslation(Strings::TASK_REVIEW_FORM_COMMENT)}
                <small>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_6)}</small>
            </h3>
            {if $review->getComment() != ''}
                <p>{$review->getComment()}</p>
            {else}
                <p>{Localisation::getTranslation(Strings::COMMON_NO_COMMENT_HAS_BEEN_LISTED)}</p>
            {/if}
            {assign var="count" value=($count + 1)}
            <hr />
            <br />
        {/foreach}
    {/if}
</div>

{include file="footer.tpl"}
