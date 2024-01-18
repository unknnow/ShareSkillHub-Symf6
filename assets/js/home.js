// import * as Vue from 'vue';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import bootstrap5Plugin from '@fullcalendar/bootstrap5';

document.addEventListener('DOMContentLoaded', () => {
    // Vue.createApp({
    //     name: "App",
    //     delimiters: ["{[{", "}]}"],
    //     data() {
    //         return { a: 1 }
    //     },
    // }).mount('#app');

    let dataEl = document.querySelector('.data');
    let events = JSON.parse(dataEl.dataset.sessions);

    let calendarEl = document.getElementById('calendar');
    let calendar = new Calendar(calendarEl, {
        plugins: [ dayGridPlugin, timeGridPlugin, listPlugin, bootstrap5Plugin ],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        buttonText: {
            today: "Ajourd'hui",
            month: 'Mois',
            week: 'Semaine',
            day: 'Jour',
            list: 'Liste'
        },
        events: events,
        eventMouseEnter: (info) => {
            $(info.el).addClass('bg-custom-blue border-custom-blue text-white');
        },
        eventMouseLeave: (info) => {
            $(info.el).removeClass('bg-custom-blue border-custom-blue text-white');
        },
        themeSystem: 'bootstrap5',
        locale: 'fr',
    });
    calendar.render();
});
