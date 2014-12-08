    <h3><i class="icon-info-sign"></i> {sprintf(Localisation::getTranslation('statistics_statistics'), {Settings::get("site.name")})}</h3>
    
    <table width="100%">
        <tr>
            <td>{Localisation::getTranslation('statistics_users')}</td>
            <td></td>
            <td style="padding-left: 50px; text-align: right">
                <strong>{$statsArray['Users']->getValue()}</strong>
            </td>
        </tr>
        <tr>
            <td>{Localisation::getTranslation('common_organisations')}</td>
            <td></td>
            <td style="padding-left: 50px; text-align: right">
                <strong>{$statsArray['Organisations']->getValue()}</strong>
            </td>
        </tr>
        <tr>
            <td>{Localisation::getTranslation('statistics_projects')}</td>
            <td></td>
            <td style="padding-left: 50px; text-align: right">
                <strong>{$statsArray['Projects']->getValue()}</strong>
            </td>
        </tr>
        <tr>
            <td>{Localisation::getTranslation('statistics_projects_archived')}</td>
            <td></td>
            <td style="padding-left: 50px; text-align: right">
                <strong>{$statsArray['ArchivedProjects']->getValue()}</strong>
            </td>
        </tr>
        <tr>
            <td>{Localisation::getTranslation('statistics_tasks')}</td>
            <td></td>
            <td style="padding-left: 50px; text-align: right">
                <strong>{$statsArray['Tasks']->getValue()}</strong>
            </td>
        </tr>
        <tr>
            <td>{Localisation::getTranslation('statistics_tasks_claimed')}</td>
            <td></td>
            <td style="padding-left: 50px; text-align: right">
                <strong>{$statsArray['ClaimedTasks']->getValue()}</strong>
            </td>
        </tr>
        <tr>
            <td>{Localisation::getTranslation('statistics_tasks_unclaimed')}</td>
            <td></td>
            <td style="padding-left: 50px; text-align: right">
                <strong>{$statsArray['UnclaimedTasks']->getValue()}</strong>
            </td>
        </tr>
        <tr>
            <td>{Localisation::getTranslation('statistics_tasks_with_prerequisites')}</td>
            <td></td>
            <td style="padding-left: 50px; text-align: right">
                <strong>{$statsArray['TasksWithPreReqs']->getValue()}</strong>
            </td>
        </tr>
        <tr>
            <td>{Localisation::getTranslation('statistics_tasks_archived')}</td>
            <td></td>
            <td style="padding-left: 50px; text-align: right">
                <strong>{$statsArray['ArchivedTasks']->getValue()}</strong>
            </td>
        </tr>
        <tr>
            <td>{Localisation::getTranslation('common_badges')}</td>
            <td></td>
            <td style="padding-left: 50px; text-align: right">
                <strong>{$statsArray['Badges']->getValue()}</strong>
            </td>
        </tr>        
        <tr>
            <td>{Localisation::getTranslation('common_tags')}</td>
            <td></td>
            <td style="padding-left: 50px; text-align: right">
                <strong>{$statsArray['Tags']->getValue()}</strong>
            </td>
        </tr>
    </table>