        <div id="registrations-id">
            {if $sent}
             
                <h3 class="mt-5">Sent Invitations</h3><br />

                <table class="table">
                    <thead>
                        <tr>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col">Used</th>
                        <th scope="col">Expires at</th>
                        <th scope="col">Link</th>
                        </tr>
                    </thead>
                    <tbody>
                     {foreach $sent  as $rec}
                        <tr>                     
                            <td><a href="mailto:{$rec.email}?subject={rawurlencode('TWB Registration')}" target="_blank">{$rec.email}</a></td>
                            <td>
                                {if $rec.roles === $NGO_LINGUIST}
                                    LINGUIST
                                {/if}

                                {if $rec.roles === ($NGO_LINGUIST + $LINGUIST)}
                                    LINGUIST+
                                {/if}

                                {if ($rec.roles === $NGO_ADMIN )}
                                    ADMIN
                                {/if}

                                {if ($rec.roles === $NGO_PROJECT_OFFICER || $rec.roles === $PROJECT_OFFICER)}
                                    PROJECT OFFICER
                                {/if}

                                {if ($rec.roles === $COMMUNITY_OFFICER )}
                                    COMMUNITY OFFICER
                                {/if}
                            </td>
                            <td>
                                 {if ($rec.used === 0 )}

                                 {/if}

                                  {if ($rec.used > 0 )}
                                     Yes
                                 {/if}
                            </td>
                            <td>
                                {$rec.date_expires}
                            </td>
                            <td>
                                {Settings::get('site.location')}{$rec.url}
                            </td>
                        </tr>
                     {/foreach}
                    </tbody>
                </table>
            {/if}
        </div>
