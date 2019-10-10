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
<div>
    <a href="${repository.url}">${repository.name}</a> (last updated ${moment(repository.updatedAt).fromNow()})
</div>
            `;
        });

        const html = `
<h3>${organization.name}</h3>
<small>${organization.description} (${organization.repositoryCount} repositories)</small>
<div>
    ${listHtml.join('')}
</div>
        `;

        wrapperEl.insertAdjacentHTML('beforeend', html);
    });
}
