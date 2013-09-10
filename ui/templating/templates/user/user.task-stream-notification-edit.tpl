{include file="header.tpl"}

<div class="page-header">
    <h1>
        <img src="http://www.gravatar.com/avatar/{md5( strtolower( trim($user->getEmail())))}?s=80&r=g" alt="" />
        {$user->getDisplayName()}
        <small>{Localisation::getTranslation(Strings::USER_TASK_STREAM_NOTIFICATION_EDIT_0)}</small>
    </h1>
</div>

{if !(isset($strict))}
    {assign var="strict" value=false}
{/if}

{include file="handle-flash-messages.tpl"}

{assign var="user_id" value=$user->getId()}
<form method="post" action="{urlFor name="stream-notification-edit" options="user_id.$user_id"}">
    <p>
        {Localisation::getTranslation(Strings::USER_TASK_STREAM_NOTIFICATION_EDIT_1)}
    </p>
    <p>
        {Localisation::getTranslation(Strings::USER_TASK_STREAM_NOTIFICATION_EDIT_2)}
    </p>
    <p>
        <hr />
        {if isset($interval)}
            <p>
                {Localisation::getTranslation(Strings::COMMON_WHAT_TYPE_OF_EMAILS)}
                {if $strict}
                    <strong>{Localisation::getTranslation(Strings::COMMON_STRICT)}</strong>
                {/if}            
            </p>
            <p>
                {Localisation::getTranslation(Strings::COMMON_HOW_OFTEN_RECEIVING_EMAILS)}
                <strong>{$interval}</strong>
            </p>
            <p>
                {if $lastSent != null}
                    {sprintf(Localisation::getTranslation(Strings::COMMON_THE_LAST_EMAIL_WAS_SENT_ON), {$lastSent})}
                {else}
                    {Localisation::getTranslation(Strings::COMMON_NO_EMAILS_HAVE_BEEN_SENT_YET)}
                {/if}
            </p>
        {else}
            {Localisation::getTranslation(Strings::COMMON_YOU_ARE_NOT_CURRENTLY_RECEIVING_TASK_STREAM_NOTIFICATION_EMAILS)}
        {/if}
        <hr />
    </p>
    <p>
        {Localisation::getTranslation(Strings::USER_TASK_STREAM_NOTIFICATION_EDIT_5)}
    </p>
    <p>
        <select name="strictMode">
            <option value="disabled" {if (!$strict)}selected="true"{/if}>
                {Localisation::getTranslation(Strings::USER_TASK_STREAM_NOTIFICATION_EDIT_ALL)}
            </option>
            <option value="enabled" {if ($strict)}selected="true"{/if}>
                {Localisation::getTranslation(Strings::COMMON_STRICT)}
            </option>
        </select>
    </p>
    <p>
        {Localisation::getTranslation(Strings::USER_TASK_STREAM_NOTIFICATION_EDIT_6)}
    </p>
    <p>
        <select name="interval">
            <option value="0"
                {if !isset($intervalId)}
                    selected="true"
                {/if}
            >
               {Localisation::getTranslation(Strings::USER_TASK_STREAM_NOTIFICATION_EDIT_NEVER)}
            </option>
            <option value="{NotificationIntervalEnum::DAILY}"
                {if isset($intervalId) && $intervalId == NotificationIntervalEnum::DAILY}
                    selected="true"
                {/if}
            >
                {Localisation::getTranslation(Strings::USER_TASK_STREAM_NOTIFICATION_EDIT_DAILY)}
            </option>
            <option value="{NotificationIntervalEnum::WEEKLY}"
                {if isset($intervalId) && $intervalId == NotificationIntervalEnum::WEEKLY}
                    selected="true"
                {/if}
            >
                {Localisation::getTranslation(Strings::USER_TASK_STREAM_NOTIFICATION_EDIT_WEEKLY)}
            </option>
            <option value="{NotificationIntervalEnum::MONTHLY}"
                {if isset($intervalId) && $intervalId == NotificationIntervalEnum::MONTHLY}
                    selected="true"
                {/if}
            >
                {Localisation::getTranslation(Strings::USER_TASK_STREAM_NOTIFICATION_EDIT_MONTHLY)}
            </option>
        </select>
    </p>
    <button type="submit" value="Submit" class="btn btn-success">
        <i class="icon-upload icon-white"></i> {Localisation::getTranslation(Strings::COMMON_SUBMIT)}
    </button>      
</form>

{include file="footer.tpl"}
