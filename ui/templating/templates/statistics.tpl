
    <h3><i class="icon-info-sign"></i> {Settings::get('site.name')} {Localisation::getTranslation(Strings::STATISTICS_STATISTICS)}</h3>
    <table width="100%">
        <tr>
            <td>{Localisation::getTranslation(Strings::STATISTICS_USERS)}</td>
            <td></td>
            <td style="padding-left: 50px; text-align: right">
                <strong>{$statsArray['Users']->getValue()}</strong>
            </td>
        </tr>
        <tr>
            <td>{Localisation::getTranslation(Strings::COMMON_ORGANISATIONS)}</td>
            <td></td>
            <td style="padding-left: 50px; text-align: right">
                <strong>{$statsArray['Organisations']->getValue()}</strong>
            </td>
        </tr>
        <tr>
            <td>{Localisation::getTranslation(Strings::STATISTICS_PROJECTS)}</td>
            <td></td>
            <td style="padding-left: 50px; text-align: right">
                <strong>{$statsArray['Projects']->getValue()}</strong>
            </td>
        </tr>
        <tr>
            <td>{Localisation::getTranslation(Strings::STATISTICS_PROJECTS_ARCHIVED)}</td>
            <td></td>
            <td style="padding-left: 50px; text-align: right">
                <strong>{$statsArray['ArchivedProjects']->getValue()}</strong>
            </td>
        </tr>
        <tr>
            <td>{Localisation::getTranslation(Strings::STATISTICS_TASKS)}</td>
            <td></td>
            <td style="padding-left: 50px; text-align: right">
                <strong>{$statsArray['Tasks']->getValue()}</strong>
            </td>
        </tr>
        <tr>
            <td>{Localisation::getTranslation(Strings::STATISTICS_TASKS_CLAIMED)}</td>
            <td></td>
            <td style="padding-left: 50px; text-align: right">
                <strong>{$statsArray['ClaimedTasks']->getValue()}</strong>
            </td>
        </tr>
        <tr>
            <td>{Localisation::getTranslation(Strings::STATISTICS_TASKS_UNCLAIMED)}</td>
            <td></td>
            <td style="padding-left: 50px; text-align: right">
                <strong>{$statsArray['UnclaimedTasks']->getValue()}</strong>
            </td>
        </tr>
        <tr>
            <td>{Localisation::getTranslation(Strings::STATISTICS_TASKS_WITH_PREREQUISITES)}</td>
            <td></td>
            <td style="padding-left: 50px; text-align: right">
                <strong>{$statsArray['TasksWithPreReqs']->getValue()}</strong>
            </td>
        </tr>
        <tr>
            <td>{Localisation::getTranslation(Strings::STATISTICS_TASKS_ARCHIVED)}</td>
            <td></td>
            <td style="padding-left: 50px; text-align: right">
                <strong>{$statsArray['ArchivedTasks']->getValue()}</strong>
            </td>
        </tr>
        <tr>
            <td>{Localisation::getTranslation(Strings::COMMON_BADGES)}</td>
            <td></td>
            <td style="padding-left: 50px; text-align: right">
                <strong>{$statsArray['Badges']->getValue()}</strong>
            </td>
        </tr>        
        <tr>
            <td>{Localisation::getTranslation(Strings::COMMON_TAGS)}</td>
            <td></td>
            <td style="padding-left: 50px; text-align: right">
                <strong>{$statsArray['Tags']->getValue()}</strong>
            </td>
        </tr>
    </table>