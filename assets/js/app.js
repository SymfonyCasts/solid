import '../css/app.css';
import forever_scroll from './components/forever_scroll';
import axios from 'axios';

const sightingsListEl = document.querySelector('.js-sightings-list');
if (sightingsListEl) {
    let nextPage = 2;
    let isLoading = false;
    const url = sightingsListEl.dataset.url;

    forever_scroll(sightingsListEl, () => {
        // avoid repeated requests while loading
        if (isLoading) {
            return;
        }

        // check to make sure there *is* a next page
        if (null === nextPage) {
            return;
        }

        isLoading = true;
        axios.get(url+'?page='+nextPage).then((response) => {
            sightingsListEl.insertAdjacentHTML('beforeend', response.data.html);
            isLoading = false;
            nextPage = response.data.next;
        })
    });
}
