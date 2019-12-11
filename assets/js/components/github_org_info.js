import axios from 'axios';
import moment from 'moment';

/**
 * Dynamically loads some GitHub organization data.
 *
 * Hmm, this would be nicer with a front-end framework! :)
 */
export default function(wrapperEl) {
    axios.get(wrapperEl.dataset.url).then(response => {
        const organization = response.data.organization;

        const listHtml = response.data.repositories.map(repository => {
            return `
<tr>
    <td>
    <a class="text-white" href="${repository.url}">${repository.name}</a> 
    </td>
    <td class="table-content">
    ${moment(repository.updatedAt).fromNow()}
    </td>
</tr>
            `;
        });

        const html = `
        <h3>${organization.name} Repos</h3>
        <small>${organization.repositoryCount} public repositories</small>
        <table class="table table-striped table-dark table-borderless table-hover">
            <thead>
                <tr class="bg-info">
                    <th>Repo Name</th>
                    <th>Updated</th>
                </tr>
            </thead>
            <tbody>
                ${listHtml.join('')}
            </tbody>
        </table>
        `;

        wrapperEl.insertAdjacentHTML('beforeend', html);
    });
}
