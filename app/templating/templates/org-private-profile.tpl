{include file='header.tpl'}

{if isset($org)}
    <div class='page-header'><h1>
    {if $org->getName() != ''}
        {$org->getname()}
    {else}
        Organisation Profile
    {/if}
    <small>Alter your organisation's profile here</small>
    {assign var="org_id" value=$org->getId()}
    <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}" class="pull-right btn btn-primary">Public Profile</a>
    </h1></div>
{else}
    header({urlFor name='home'});
{/if}

{assign var="org_id" value=$org->getId()}
<form method='post' action='{urlFor name='org-private-profile' options="org_id.$org_id"}' class='well'>
     <label for='name'>Public display name:</label>
     <input type='text' name='name' id='name' placeholder='Name' />
     <label for='home_page'>Home Page:</label>
     <input type='text' name='home_page' id='home_page' placeholder='http://yoursite.com' />
     <label for='bio'>Biography:</label>
     <textarea name='bio' cols='40' rows='5'></textarea>
     <p>
         <button type='submit' class='btn btn-primary' name='submit'>Update</button>
     </p>
 </form>



{include file='footer.tpl'}
