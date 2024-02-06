import DataTable from 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';

document.addEventListener('DOMContentLoaded', () => {
    new DataTable('#datatable', {
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
        }
    });
});
