<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" >

<head>
    <!-- Editor Hint: ¿áéíóú -->
    <meta charset="utf-8" content="application/xhtml+xml" />
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/bootstrap/css/bootstrap.min1.css"/>
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/css/style.1.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/jquery-ui.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/solas2.css"/>
    <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-1.9.0.js"></script>
    <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-ui.js"></script>
</head>

<body>

{if isset($all_users) && count($all_users) > 0}

<table id="myTable" style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
  <thead>
    <th width="7%">ID</th>
    <th width="25%">Title</th>
    <th width="24%">Creator</th>
    <th width="25%">Partner</th>
    <th width="11%">Phrase TMS URL</th>
    <th width="8%">Word Count</th>
  </thead>

  <tbody>
  {foreach $all_users as $user_row}

    <tr>
      <td>{$user_row['project_id']}</td>
      <td><a href="{urlFor name="project-view" options="project_id.{$user_row['project_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($user_row['title'])}</a></td>
      {if !empty($user_row['creator_id'])}
      <td><a href="{urlFor name="user-public-profile" options="user_id.{$user_row['creator_id']}"}" target="_blank">{$user_row['creator_email']}</a></td>
      {else}
      <td></td>
      {/if}
      <td><a href="{urlFor name="org-public-profile" options="org_id.{$user_row['organisation_id']}"}" target="_blank">{$user_row['name']|escape:'html':'UTF-8'}</a></td>
      {if preg_match('/^\d*$/', $user_row['memsource_project_uid'])}
      <td>Shell Project</td>
      {else}
      <td><a href="https://twbplatform.org/simplesaml/saml2/idp/SSOService.php?spentityid=https://eu.phrase.com/idm/saml/hoKMZESrFauJjF1hpLXwwo&RelayState=https://cloud.memsource.com/web/project2/show/{$user_row['memsource_project_uid']}" target="_blank">Phrase Project</a></td>
      {/if}
      <td>{if $user_row['word-count'] != 1}{$user_row['word-count']}{else}None{/if}</td>
    </tr>

  {/foreach}
  </tbody>

</table>

{else}<p class="alert alert-info">No Users</p>{/if}

</body>
</html>
